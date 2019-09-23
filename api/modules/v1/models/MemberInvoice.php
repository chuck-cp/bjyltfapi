<?php

namespace api\modules\v1\models;

use common\libs\ToolsClass;
use Kafka\Exception;
use Yii;
use api\modules\v1\models\Order;

class MemberInvoice extends \api\core\ApiActiveRecord
{
    public $order_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_invoice}}';
    }
    /*
     * 生成发票
     */
    public function saveInvoice(){
        if(empty($this->order_id)){
            return false;
        }
        $orderArr = ToolsClass::explode(',', $this->order_id);
        $num = Order::find()->where(['id'=>$orderArr,'payment_status'=>3,'is_billing'=>0])->count();
        if((int)$num !== count($orderArr)){
            return 'ERROR';
        }
        $dbTrans = \Yii::$app->db->beginTransaction();
        try{
            //添加发票
            $this->order_price = (new Order())->getOrdersFieldsSum($orderArr,'order_price');
            $this->member_id = Yii::$app->user->id;
            $info = (new Member())->getMemberByShop(Yii::$app->user->id);
            $this->member_name = isset($info['name']) ? $info['name'] : '';
            $this->member_phone = isset($info['mobile']) ? $info['mobile'] : '';
            $this->order_num = count($orderArr);
            $this->invoice_value = (new Order())->getOrdersFieldsSum($orderArr,'final_price');
            $this->create_at = date('Y-m-d H:i:s');
            $this->save();
            //关联订单
            Order::updateAll(['is_billing'=>$this->id], ['id'=>$orderArr]);
            $dbTrans->commit();
            return 'SUCCESS';
        }catch (Exception $e){
            $dbTrans->rollBack();
            Yii::error($e->getMessage(),'db');
            return 'ERROR';

        }




    }
    /*
     * 获取用户历史开票记录
     */
    public function getOrderHistoryList(){
        $historyList = self::find()->where(['member_id'=>Yii::$app->user->id])->select('id, order_num, invoice_value, status, create_at')->orderBy('id DESC')->asArray()->all();
        if(!empty($historyList)){
            $historyList[0]['month'] = ToolsClass::judgeDate($historyList[0]['create_at']);
            foreach ($historyList as $k => $v){
                $historyList[$k]['type'] = '增值税普通发票';
                $historyList[$k]['invoice_value'] = ToolsClass::priceConvert($v['invoice_value']).'元';
                if($k > 0){
                    if($this->getYearMonth($v['create_at']) !== $this->getYearMonth($historyList[$k-1]['create_at'])){
                        $historyList[$k]['month'] = ToolsClass::judgeDate($v['create_at']);
                    }else{
                        $historyList[$k]['month'] = '';
                    }
                }
            }
        }
        return $historyList;
    }
    /*
    * 获取某日期的年月
    */
    private function getYearMonth($date){
        return date('Y-m', strtotime($date));
    }
    /*
     * 关联order表
     */
    public function getOrder(){
        return $this->hasMany(Order::className(),['is_billing'=>'id']);
    }
    //检查发票类型数值是否合法
    public function checkInvoiceType($type){
        return in_array($type,[1,2]);
    }
    //检查手机号或者电话是否合法
    public function checkPhone($phone){
        if(!$phone){
            return true;
        }
        $n = preg_match("/^1[3578]{1}\d{9}$/", $phone);
        $g = preg_match("/^([0-9]{3,4}-)?[0-9]{7,8}$/",$phone);
        return $n > 0 || $g >0;
    }
    //检查手机号或者电话是否合法
    public function checkPhones($phone){
        if(!$phone){
            return false;
        }
        $n = preg_match("/^1[3578]{1}\d{9}$/", $phone);
        $g = preg_match("/^([0-9]{3,4}-)?[0-9]{7,8}$/",$phone);
        return $n > 0 || $g >0;
    }
    //获取发票详情
    public function getInvoiceDetail($id){
        if(!$id) {return false;}
        $invoiceModel = self::find()->where(['id'=>$id])->select('receiver, contact_phone, address_id, address_detail, taxplayer_id, invoice_title, invoice_value, create_at, tracking_number, logistics_name, status, update_at, invoice_title_type')->asArray()->one();
        if(!empty($invoiceModel)){
            $grr['time'] = $invoiceModel['create_at'];
            $grr['ftime'] = $invoiceModel['create_at'];
            $invoiceModel['content'] = '广告费';
            $invoiceModel['invoice_value'] = ToolsClass::priceConvert($invoiceModel['invoice_value']).'元';
            $invoiceModel['create_at'] = date('Y-m-d',strtotime($invoiceModel['create_at']));
            if($invoiceModel['tracking_number'] && $invoiceModel['logistics_name']){
                $invoiceModel['logistics'] = json_decode($this->getWlInfo($invoiceModel['logistics_name'],$invoiceModel['tracking_number']),true);
                $lrr['time'] = $invoiceModel['update_at'];
                $lrr['ftime'] = $invoiceModel['update_at'];
                $lrr['context'] = '发票已开出等待揽收';
                $lrr['location'] = '';
                $invoiceModel['logistics']['data'][] = $lrr;
            }
            $grr['context'] = '发票申请已受理';
            $grr['location'] = '';
            $invoiceModel['logistics']['data'][] = $grr;
            $invoiceModel['address'] = SystemAddress::getAreaNameById($invoiceModel['address_id']).$invoiceModel['address_detail'];
            return $invoiceModel;
        }
        return [];
    }
    //物流信息
    private function getWlInfo($type, $code){
        $requestData= "{'OrderCode':'','ShipperCode':".$type.",'LogisticCode':".$code."}";
        $sign = $this->encrypt($requestData,'');
        //$code = '804134340318613406';
        //$type = 'yuantong';
        $url = 'https://www.kuaidi100.com/query?ak=00001&v=3.0&f=json&locale=zh_CN&postid='.$code.'&type='.$type.'&sign='.$sign;
        //echo $url;die;
        // 1. 初始化
        $ch = curl_init();
        // 2. 设置选项，包括URL
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_HEADER,0);
        // 3. 执行并获取HTML文档内容
        $output = curl_exec($ch);
        if($output === FALSE ){
            echo "CURL Error:".curl_error($ch);
        }
        // 4. 释放curl句柄
        curl_close($ch);
        //var_dump($output);exit;
        return $output;
    }
    /**
     * 电商Sign签名生成
     * @param data 内容
     * @param appkey Appkey
     * @return DataSign签名
     */
    private function encrypt($data, $appkey) {
        return urlencode(base64_encode(md5($data.$appkey)));
    }
    /*
     * 计算title长度
     */
    public function checkInvoiceTitle($title)
    {
        return mb_strlen($title) <= 30;
    }

    public function checkTaxplayerId($tid)
    {
        return strlen($tid) < 21;
    }
    /*
     * 场景
     */
    public function scenes(){
        return [
            'create' => [
                'invoice_title_type'=>[
                    [
                        [
                            'function'=>'this::checkInvoiceType',
                            'result'=>'INVOICE_TYPE_ERROR'
                        ],
                    ],
                    [
                        'required'=>'1',
                        'type' => 'int',
                    ],

                ],
                'invoice_title'=>[
                        ['required'=>'1',],
                        [
                            [
                                'function'=>'this::checkInvoiceTitle',
                                'result'=>'INVOICE_TITLE_TOO_LONG'
                            ],
                        ],
                ],
//                'taxplayer_id'=>[
//                        'type' => 'string',
//                        'max_length' => 20,
//                ],
                'taxplayer_id' => [
                    [
                        [
                            'function'=>'this::checkTaxplayerId',
                            'result'=>'TAXPLAYER_ID_TOO_LONG'
                        ],
                    ],
                ],
                'contact_phone'=>[
                    [
                        [
                            'function'=>'this::checkPhones',
                            'result'=>'PHONE_ERROR'
                        ],
                    ],
                    [
                        'required'=>'1',
                        'type' => 'string',
                        'max_length' => 15,
                    ],
                ],
                'receiver' => [
                    'required'=>'1',
                    'type' => 'string',
                    'max_length' => 20,
                ],
                'address_detail'=>[
                        'required'=>'1',
                        'type' => 'string',
                        'max_length' => 80,

                ],
                'address_id'=>[
                    'required'=>'1',
                    'type' => 'int',
                ],
                'remark'=>[
                        'type' => 'string',
                        'max_length' => 50,
                        'result' => 'FIELD_TOO_LONG'
                ],
                'invoice_address'=>[
                        'type' => 'string',
                        'max_length' => 120,
                        'result' => 'FIELD_TOO_LONG'
                ],
                'invoice_phone'=>[
                    [
                        [
                            'function'=>'this::checkPhone',
                            'result'=>'PHONE_ERROR'
                        ],
                    ],
                    [
                        'type' => 'string',
                        'max_length' => 15,
                        'result' => 'FIELD_TOO_LONG'
                    ],
                ],
                'bank_name'=>[
                        'type' => 'string',
                        'max_length' => 40,
                        'result' => 'FIELD_TOO_LONG'
                ],
                'bank_account'=>[
                        'type' => 'string',
                        'max_length' => 30,
                        'result' => 'FIELD_TOO_LONG'
                ],
                'order_id'=>[
                    'required'=>'1',
                    'type' => 'string',
                ],
            ]
        ];
    }
    
    
}
