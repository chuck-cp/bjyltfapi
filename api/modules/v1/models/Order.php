<?php

namespace api\modules\v1\models;

use api\modules\v1\models\LogPayment;
use common\libs\ArrayClass;
use common\libs\RedisClass;
use common\libs\ToolsClass;
use function GuzzleHttp\Psr7\str;
use api\modules\v1\models\MemberAccount;
use api\modules\v1\models\MemberAccountCount;
use Yii;
use yii\base\Exception;
use yii\data\Pagination;
use yii\db\Expression;

/**
 * 店铺管理
 */
class Order extends \api\core\ApiActiveRecord
{

    public $type;
    public $order_id;
    public $parent_id;
    public $order_list;
    public $show_self;
    public $advert_bind;
    public $advert_group;
    public $start_at;
    public $end_at;
    public $page;
    public static function tableName()
    {
        return '{{%order}}';
    }
    /*
     * 获取用户订单列表
     * */
    public function getMemberOrder()
    {
        if(!empty($this->order_list)){
            $str="yl_order.member_name,";
            $str2="yl_order.salesman_id";
        }else{
            $str="";
            $str2="yl_order.member_id";
        }
        $member_id = Yii::$app->user->id;
        if(!empty($member_id)){
            $orderModel = self::find()->joinWith('orderDate',$eagerLoading = false)->where([$str2=>$member_id])->orderBy('id desc')->select($str.'yl_order.id,yl_order.order_code,yl_order.final_price,yl_order.unit_price,yl_order.payment_status,yl_order.advert_time,yl_order.advert_id,yl_order.advert_name,yl_order.examine_status,yl_order_date.start_at,yl_order_date.end_at,yl_order.advert_key');
            if(isset($this->type)){
                if($this->type==1){
                    $orderModel->andWhere(['in','payment_status',[2,$this->type]]);
                }else{
                    $orderModel->andWhere(['payment_status'=>$this->type]);
                }
            }else{
                $orderModel->andWhere(['>','payment_status',-2]);
            }
            $pagination = new Pagination(['totalCount'=>$orderModel->count(),'pageSize' => 20]);
            $pagination->validatePage = false;
            $orderModel = $orderModel->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
            if(!empty($orderModel)){
                foreach($orderModel as $key=>$order){
                    $orderModel[$key]['order_price'] = (string)ToolsClass::priceConvert($order['final_price']);
                    $orderModel[$key]['unit_price'] = (string)ToolsClass::priceConvert($order['unit_price']);
                }
            }
            return $orderModel;

        }
    }
    /*
     * 获取订单指定字段
     */
    public function getAppointFields($fields){
        return self::find()->where(['id'=>$this->order_id,'member_id'=>$this->member_id])->select($fields)->asArray()->one();
    }
    /*
     *获取指定订单详情
     *  */
    public function getOneOrder($order_code){
        $orderModel = self::find()
            ->joinWith('orderDate',$eagerLoading = false)
            ->where(['and',['yl_order.id'=>$order_code],['or',['member_id'=> Yii::$app->user->id],['salesman_id' => Yii::$app->user->id]]])
            ->select('final_price,preferential_way,member_name,member_mobile as mobile,advert_id,payment_type,payment_price,overdue_at,payment_at,salesman_name,salesman_mobile,custom_service_name,custom_service_mobile,order_price,unit_price,area_name,total_day,rate,advert_time,yl_order.advert_name,yl_order.payment_status,yl_order.id,yl_order.order_code,yl_order.examine_status,yl_order.create_at,yl_order_date.start_at,yl_order_date.end_at,yl_order_date.is_update,yl_order.company_area_id,yl_order.deal_price,yl_order.schedule_status,yl_order.buy_agreed,remarks')->asArray()->one();
        if (empty($orderModel)) {
            return '';
        }
        $orderModel['remarks'] = $orderModel['remarks'] == true ? $orderModel['remarks'] : '无';
        if($orderModel['examine_status']==2){
            $shenhe=new LogExamine();
            $orderModel['examine_desc']=$shenhe::find()->where(['foreign_id'=>$order_code,'examine_key'=>5,'examine_result'=>2])->select('examine_desc')->asArray()->one()['examine_desc'];
        }
        if($orderModel['payment_status']==0){
            $huikuan = new LogPayment();
            $type=$huikuan::find()->where(['order_id'=>$order_code])->select('pay_type,pay_status')->orderBy('id desc')->asArray()->one();
            if(isset($type['pay_type']) && $type['pay_type'] == 4 && $type['pay_status'] == 0)$orderModel['remittance']="1";
        }
        if($orderModel['payment_status']==1){
            $day=strtotime($orderModel['overdue_at']) - strtotime(date('Y-m-d'));
            $orderModel['days_remaining']=(string)floor($day/86400);
            if ($orderModel['days_remaining'] < 0) {
                $orderModel['days_remaining']="已逾期";
            }else{
                $orderModel['days_remaining'] .= '天';
            }
            $orderModel['retainage']=(string)ToolsClass::priceConvert($orderModel['final_price']-$orderModel['payment_price']);
            if($orderModel['retainage']<=0)$orderModel['retainage']="已逾期";
        }
        if($orderModel['payment_type']==2){
            $orderModel['retainage']=(string)ToolsClass::priceConvert($orderModel['final_price']-$orderModel['payment_price']);
        }
        if(empty($this->order_list)){
            //获取对接人投诉状态
            $complain = new OrderComplain();
            $res=$complain->find()->where(['order_id'=>$order_code,'member_type'=>1,'complain_type'=>1])->asArray()->one();
            if($res){
                $orderModel['custom_complain_type']="1";
            }else{
                $orderModel['custom_complain_type']="2";
            }
            //业务合作人投诉状态
            $res=$complain->find()->where(['order_id'=>$order_code,'member_type'=>1,'complain_type'=>2])->asArray()->one();
            if($res){
                $orderModel['salesman_complain_type']="1";
            }else{
                $orderModel['salesman_complain_type']="2";
            }
        }else{
            //获取广告对接人投诉状态
            $complain = new OrderComplain();
            $res=$complain->find()->where(['order_id'=>$order_code,'member_type'=>2,'complain_type'=>1])->asArray()->one();
            if($res){
                $orderModel['custom_complain_type']="1";
            }else{
                $orderModel['custom_complain_type']="2";
            }

        }
        if(!empty($orderModel['company_area_id'])){
            $orderModel['address']=SystemAddress::getAreaNameById($orderModel['company_area_id']);
        }
        $orderModel['unit_price']=(string)ToolsClass::priceConvert($orderModel['unit_price']);
        $orderModel['deal_price']=(string)ToolsClass::priceConvert($orderModel['deal_price']);
        $orderModel['order_price']=(string)ToolsClass::priceConvert($orderModel['order_price']);
        $orderModel['final_price']=(string)ToolsClass::priceConvert($orderModel['final_price']);
        $orderModel['payment_price']=(string)ToolsClass::priceConvert($orderModel['payment_price']);
        $orderModel['service_phone'] = SystemConfig::getConfig('service_phone');
        $orderModel['is_update'] =(string)(3-$orderModel['is_update']);
        //订单还剩多少时间待支付
        if($orderModel['payment_status'] == 0){
            if(isset($orderModel['remittance']) && $orderModel['remittance'] == '1'){//线下付款取消订单时间为3天
                $orderModel['count_down'] = $this->getCountDownTime($orderModel['create_at'],'offline');
            }else{//线上付款取消订单时间为1个小时
                $orderModel['count_down'] = $this->getCountDownTime($orderModel['create_at']);
            }
        }else{
            $orderModel['count_down'] = 0;
        }
        if($advertPosition = AdvertPosition::findByOrderView($orderModel['advert_id'])){
            $orderModel = array_merge($orderModel,$advertPosition);
        }
        //如果用户已付款并且实际购买金额为0,就显示无购买地区
        if($orderModel['deal_price'] == 0 && $orderModel['payment_status'] > 0){
            $orderModel['area_name'] = '无购买地区';
        }
        $orderModel['buy_url'] = Yii::$app->params['baseWapUrl'].'/order/buy-contract';
        // 判断是否显示投放报告
        $orderModel['throw_view'] = $this->getOrderThrowViewStatus($orderModel['end_at']);
        return $orderModel;
    }

    /*
     * 获取订单是否可以查看投放报告
     * @param end_at date 订单投放结束时间
     * */
    public function getOrderThrowViewStatus($end_at)
    {
        $end_at = strtotime('+7 day',strtotime($end_at));
        return (int)(time() >= $end_at);
    }
    /*
     * 计算未支付订单下单后待支付时间倒计时
     */
    public function getCountDownTime($create_at, $type='online'){
        if(!$create_at){
            return 0;
        }
//        if($type == 'online'){
//            $tm = strtotime($create_at.'+1 hours') - time();
//            return  $tm < 0 ? 0 : $tm;
//        }
        $tm = strtotime($create_at.'+15 days') - time();
        return $tm < 0 ? 0 : $tm;
    }
    public function getOrderDate()
    {
//第一个参数为要关联的子表模型类名，
//第二个参数指定 通过子表的customer_id，关联主表的id字段
        return $this->hasOne(OrderDate::className(), ['order_id' => 'id'])->select('order_id');
    }
    public function getmember()
    {
        return $this->hasOne(member::className(), ['id' => 'member_id']);
    }

    /*
     * 获取订单投诉人信息
     * */
    public function ordercomplain(){
        $order_list = self::find()->where(['id'=>$this->order_id])->select('id,salesman_name,custom_service_name')->asArray()->one();
        return $order_list;
    }

    /*
     * 创建订单
     * */
    public function createOrder(){
        try{
            list($priceStatus,$orderPrice,$unitPrice,$screen_number) = (new AdvertPrice())->selectAdvertPrice($this->advert_id,$this->advert_time,0,$this->rate,0,$this->number,$this->start_at,$this->end_at);
            if($priceStatus != 'SUCCESS'){
                return $priceStatus;
            }
            // 检查优惠价格是否超出系统设置的最大优惠折扣
            if ($this->final_price < $orderPrice * SystemConfig::getConfig('order_maximum_discount') / 10) {
                return 'FINAL_PRICE_ERROR';
            }
            $this->screen_number = $screen_number;
            $this->salesman_mobile = Yii::$app->user->identity->mobile;
            $this->salesman_name = Yii::$app->user->identity->name;
            $this->salesman_id = Yii::$app->user->id;
			$this->part_time_order = Yii::$app->user->identity->part_time_business;
            $this->order_code = $this->generateOrderCode();
            $this->order_price = $orderPrice;
            $this->unit_price = $unitPrice;
            //$this->remarks = $this->remarks;
            $this->payment_price = $this->payment_type == 2 ? $this->final_price * (SystemConfig::getConfig('prepayment_ratio') / 100) : 0;
            $this->overdue_at = date('Y-m-d',strtotime('+15 day'));
            $this->total_day = ((strtotime($this->end_at) - strtotime($this->start_at)) / 86400) + 1;
            $this->area_name = OrderAreaCache::getAreaAndDefaultAreaName();
            $this->create_at = date("Y-m-d H:i:s");
            if(!$this->setCustomMember()){
                throw new Exception("设置广告对接人失败");
            }
            $this->save();
            //写入order_message
            OrderMessage::Log($this->id,'生成订单',1);
            OrderMessage::Log($this->id,'生成订单',2);
            RedisClass::set("system_order_code:".$this->order_code,1,3,3600);
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return 'ERROR';
        }
    }

    /*
     * 广告广告对接人
     * */
    public function setCustomMember(){
        try{
            $redis = RedisClass::init(4);
            $customList = AuthAssignment::find()->joinWith('user',false)->select('yl_user.username,yl_user.id,yl_user.phone,yl_auth_assignment.user_id')->where(['item_name'=>'广告对接人','status'=>1])->asArray()->all();
            if(empty($customList)){
                return true;
            }
            if(count($customList) == 1){
                $customMember = $customList[0];
            }else{
                $customMemberSort = $redis->get("custom_config_sort");
                if(empty($customMemberSort)){
                    $customMemberSort = 0;
                    $redis->set("custom_config_sort",1);
                }elseif($customMemberSort >= count($customList)){
                    $customMemberSort = 0;
                    $redis->set("custom_config_sort",1);
                }else{
                    $redis->incr("custom_config_sort");
                }
                $customMember = $customList[$customMemberSort];
            }
            $this->custom_member_id = $customMember['id'];
            $this->custom_service_name = $customMember['username'];
            $this->custom_service_mobile = $customMember['phone'];
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }


    /*
     * 验证业务员手机号
     * */
    public function checkSalesmanMobile($mobile){
        $memberModel = Member::find()->where(['mobile'=>$mobile])->select('id,name')->asArray()->one();
        if(empty($memberModel)){
            return true;
        }
        $this->member_id = $memberModel['id'];
        return true;
    }

    /*
     * 检查广告ID
     * */
    public function checkAdvertId($advert_id){
        $advertModel = AdvertPosition::find()->where(['id'=>$advert_id])->select('name,key,bind,group')->asArray()->one();
        if(empty($advertModel) || empty($advertModel['key']) || empty($advertModel['name'])){
            return false;
        }
        $this->advert_name = $advertModel['name'];
        $this->advert_key = $advertModel['key'];
        $this->advert_bind = $advertModel['bind'];
        $this->advert_group = $advertModel['group'];
        return true;
    }

    /*
     * 计算频率
     * */
    public function reduceRateNumber(){
        if(!$this->number = AdvertPosition::reduceRate($this->advert_id,$this->rate)){
            return false;
        }
        return true;
    }

    /*
     * 生成订单编号
     * */
    public function generateOrderCode(){
        if($this->payment_type == 1){
            $orderCode = 'Q';
        }else{
            $orderCode = 'D';
        }
        $orderCode = $orderCode.date('YmdH').rand(100,999);
        if(RedisClass::get('system_order_code:'.$orderCode,3)){
            return $this->generateOrderCode();
        }
        return $orderCode;
    }

    /*
     * 获取订单地区
     * */
    public function getOrderArea($oder_id){
        $cell_out_area_id = '';
        if(empty($oder_id)){
            $create_at = null;
            $areaModel = OrderAreaCache::getAreaByToken();
            if($this->type == 1){
                #获取确认地区页面选择的地区
                $area_data = $areaModel['area_id'];
                #排除掉那些已经买完的街道
                $cell_out_area_id = $areaModel['cell_out_area_id'];
            }else{
                #获取购买广告页选择的地区
                $area_data = $areaModel['parent_area_id'];
            }
        }else{
            if(!$orderModel = self::find()->where(['id'=>$oder_id])->select('create_at,salesman_id,member_id')->asArray()->one()){
                return false;
            }
            if($orderModel['member_id'] != Yii::$app->user->id && $orderModel['salesman_id'] != Yii::$app->user->id){
                return false;
            }
            $areaModel = OrderArea::find()->where(['order_id'=>$oder_id])->select('street_area,area_type')->asArray()->one();
            $create_at = $orderModel['create_at'];
            $area_data = $areaModel['street_area'];
        }
        if(empty($area_data)){
            return false;
        }
        $area_data = ToolsClass::explode(",",$area_data);
        $area_type = SystemAddress::reduceAreaType($area_data[0]);
        $parent_area_type = SystemAddress::reduceAreaType($this->parent_id);
        $resultAddress = SystemAddress::getOrderSelectArea($area_data,$area_type,$this->parent_id,$create_at,$this->show_self);
        if($parent_area_type == 3 && !empty($cell_out_area_id)){
            #如果选择的是区或县并且有已卖完的地区ID就把已卖完的地区ID从结果中去掉
            $cell_out_area_id = explode(",",$cell_out_area_id);
            $reformAddress = [];
            foreach($resultAddress as $key=>$address){
                if(!in_array($address['id'],$cell_out_area_id)){
                    $reformAddress[] = $resultAddress[$key];
                }
            }
            $resultAddress = $reformAddress;
        }
        return $resultAddress;
    }

    /*
     * 写入支付日志
     * */
    public function writePaymentLog($pay_type){
        $orderModel = self::find()->where(['order_code'=>$this->order_code,'member_id'=>Yii::$app->user->id])->select('advert_id,id,advert_name,order_code,final_price,payment_type,payment_price,payment_status')->asArray()->one();
        if(empty($orderModel)){//订单号错误
            return ['ORDER_CODE_ERROR',0];
        }elseif($orderModel['payment_status'] == -1){//订单已取消
            return ['ORDER_CANCELED',0];
        }elseif($orderModel['payment_status'] == -2) {//订单已取消
            return ['ORDER_OVERDUE',0];
        }elseif($orderModel['payment_status'] == 3){//订单已支付完成
            return ['ORDER_ALREADY_PAID',0];
        }
        try{
            //付款方式(1、全款 2、定金 3、尾款)
            $pay_style = 1;
            if($orderModel['payment_type'] == 2){ //预付款
                if($orderModel['payment_status'] == 0){//还未付款
                    $pay_style = 2;
                    $orderModel['final_price'] = $orderModel['payment_price'];
                }else{//支付剩余款项
                    $pay_style = 3;
                    $orderModel['final_price'] = $orderModel['final_price'] - $orderModel['payment_price'];
                }
            }
            $paymentModel = new LogPayment();
            if($pay_type == 4){
                Order::updateAll(['line_pay'=>1],['id'=>$orderModel['id']]);
                //如果是线下汇款,检查之前是否生成过随机码
                $paymentData = $paymentModel->find()->where(['order_code'=>$this->order_code,'pay_status'=>0,'pay_type'=>4,'pay_style'=>$pay_style])->select('payment_code,serial_number')->limit(1)->asArray()->one();
                if(!empty($paymentData)){
                    $orderModel['serial_number'] = $paymentData['serial_number'];
                    $orderModel['payment_code'] = $paymentData['payment_code'];
                    return ['SUCCESS',$orderModel];
                }
            }

            $paymentModel->order_id = $orderModel['id'];
            $paymentModel->order_code = $orderModel['order_code'];
            $paymentModel->price = $orderModel['final_price'];
            $paymentModel->pay_style = $pay_style;
            $paymentModel->pay_type = $pay_type;
            $paymentModel->save();
            $orderModel['serial_number'] = $paymentModel->serial_number;
            $orderModel['payment_code'] = $paymentModel->payment_code;
            $orderModel['order_code'] = $orderModel['order_code'].'_'.$pay_style;
            return ['SUCCESS',$orderModel];
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return ['PAYMENT_LOG_ERROR',0];
        }
    }

    /*
     * 支付回调
     * @param string serial_number 支付流水号
     * @param string payment_price 支付金额
     * @param string payment_at 支付时间
     * @param string other_account 第三方平台账号
     * @param string other_serial 第三方流水号
     * */
    public function paymentCallBack($serial_number,$payment_price,$payment_at,$other_account,$other_serial){
        $paymentModel = LogPayment::find()->where(['serial_number'=>$serial_number])->select('order_id,id,pay_status,price')->orderBy('id desc')->limit(1)->asArray()->one();
        if(empty($paymentModel)){
            \Yii::error('[error]找不到支付流水信息,流水ID:'.$paymentModel['id'],'payment');
            return false;
        }
        if($paymentModel['pay_status'] == 1){
            \Yii::error('[error]该订单已支付,流水ID:'.$paymentModel['id'],'payment');
            return false;
        }
//        if(($payment_price*100) != $paymentModel['price']){
//            \Yii::error('[error]支付金额不正确,流水ID:'.$paymentModel['id'],'payment');
//            return false;
//        }
        $order_id = $paymentModel['order_id'];
        $orderModel = Order::find()->where(['id'=>$order_id])->select('salesman_id,member_id,advert_id,total_day,advert_key,number,advert_time,payment_type,payment_status,examine_status')->asArray()->one();
        if(empty($orderModel)){
            \Yii::error('[error]找不到订单信息,流水ID:'.$paymentModel['id'].',订单号:'.$order_id,'payment');
            return false;
        }
        $dbTrans = \Yii::$app->db->beginTransaction();
        try{
            $payment_price = ToolsClass::priceConvert($payment_price,2);
            LogPayment::updateAll(['other_account'=>$other_account,'other_serial'=>$other_serial,'pay_at'=>$payment_at,'pay_status'=>1],['id'=>$paymentModel['id']]);
            $flag = false;
            if($orderModel['payment_type'] == 1){
                $resultLog = OrderMessage::Log($order_id,"完成付款");
                if($resultLog){
                    $resultLog = OrderMessage::Log($order_id,"广告素材待提交",2);
                }
                //全款支付
                Order::updateAll(['last_payment_at'=>$payment_at,'payment_at'=>$payment_at,'payment_status'=>3],['id'=>$order_id]);
                $flag = true;
            }else{
                //定金支付
                if($orderModel['payment_status'] == 0){
                    $resultLog = OrderMessage::Log($order_id,"完成首付款");
                    if($resultLog){
                        $resultLog = OrderMessage::Log($order_id,"广告素材待提交",2);
                    }
                    //第一次支付
                    Order::updateAll(['last_payment_at'=>$payment_at,'payment_at'=>$payment_at,'payment_status'=>1],['id'=>$order_id]);
                    $flag = true;
                }elseif($orderModel['payment_status'] == 1 || $orderModel['payment_status'] == 2){
                    $resultLog = OrderMessage::Log($order_id,"完成尾款");
                    //第二次支付
                    Order::updateAll(['last_payment_at'=>$payment_at,'payment_status'=>3],['id'=>$order_id]);
                }else{
                    throw new Exception('[error]订单状态错误,流水ID:'.$paymentModel['id'].',订单号:'.$order_id);
                }
            }
//            if(empty($resultLog)){
//                throw new Exception("[error]订单日志写入失败");
//            }
            if(!SystemAcount::UpdateAccount($paymentModel['price'])){
                throw new Exception("[error]系统总收入计算失败");
            }
            //付款成功增加业务员订单数量业绩统计
            /*************************************/
            if($flag == true){
                MemberAccount::updateAllCounters(['order_number'=>1], ['member_id'=>$orderModel['salesman_id']]);
                $date = date('Y-m',strtotime($payment_at));
                $accountAccountModel = MemberAccountCount::getOrCreateAccount($orderModel['salesman_id'],$date);
                if(!$accountAccountModel){
                    throw new Exception("[error]按月统计订单业绩失败");
                }
                if(!MemberAccountCount::updateAll(['order_number'=>new Expression('order_number + 1')],['create_at'=>$date,'member_id'=>$orderModel['salesman_id']])){
                    throw new Exception("[error]按月统计订单业绩失败");
                }
            }
            /*************************************/
            //首次付款成功,开始写入排期
            if($orderModel['payment_status'] == 0){
                $orderDateModel = OrderDate::find()->where(['order_id'=>$order_id])->select('start_at,end_at')->one();
                $orderAreaModel = OrderArea::find()->where(['order_id'=>$order_id])->select('area_id')->one();
                $advertModel = AdvertPosition::find()->where(['id'=>$orderModel['advert_id']])->select('`group`,bind')->one();
                RedisClass::rpush("system_create_order_list",json_encode([
                    'type'=>'create_order',
                    'order_id'=>$order_id,
                    'delete_date'=>'',
                    'advert_key'=>strtolower($orderModel['advert_key']),
                    'rate'=>$orderModel['number'],
                    'start_at'=>$orderDateModel['start_at'],
                    'end_at'=>$orderDateModel['end_at'],
                    'area_id'=>$orderAreaModel['area_id'],
                    'group'=>$advertModel['group'],
                    'bind'=>strtolower($advertModel['bind']),
                    'advert_time'=>$orderModel['advert_time'],
                    'token'=>md5("wwwbjyltfcom{$orderModel['advert_time']}{$orderModel['advert_key']}{$orderModel['number']}{$orderModel['member_id']}")
                ]),4);
            }
            $dbTrans->commit();
            return true;
        }catch (Exception $e){
            $dbTrans->rollBack();
            \Yii::error($e->getMessage(),'payment');
            return false;
        }
    }

    public static function getEverydayFromTwoDay($oldday,$newday,$type = 'second'){
        if(!$newday || $oldday){
            return false;
        }
        $days = ToolsClass::timediff(strtotime($newday), strtotime($oldday));
        if($days < 1) return false;
        $days_list = '';
        if($type == 'second'){
            $days_list = $newday;
            $days_list .= date('Y-m-d',strtotime($newday) + $days*86400).',';
        }else{
            $days_list = $oldday;
            $days_list .= date('Y-m-d',strtotime($oldday) + $days*86400).',';
        }
        return $days_list;

    }

    public function getDate(){
        return $this->hasOne(OrderDate::className(),['order_id'=>'id'])->select('id,start_at,end_at,order_id');
    }
    /*
     * 获取已支付单未开发票的列表
     */
    public function getOrderList($type='invoice'){
        $where['member_id'] = Yii::$app->user->id;
        $where['payment_status'] = 3;
        $sort = 'last_payment_at desc';
        if($type == 'invoice'){
            $where['is_billing'] = 0;
        }elseif ($type == 'contact'){
            $where['contact_status'] = 0;
        }elseif ($type == 'history'){
            $where = ['and',$where,['>', 'contact_status', 0]];
            $sort = 'contact_at desc';
        }
        $orderList = self::find()->joinWith('date',$eagerLoading = false)->where($where)->select('advert_key, advert_name, advert_time, final_price, examine_status, yl_order.id, start_at, end_at, last_payment_at, contact_status')->orderBy($sort)->asArray()->all();
        if(!empty($orderList)){
            $orderList[0]['month'] =ToolsClass::judgeDate($orderList[0]['last_payment_at']);
            foreach ($orderList as $k => $v){
                $orderList[$k]['advert_time'] = str_replace(['s','m'],['秒','分'],$v['advert_time']);
                $orderList[$k]['order_price_pristine'] = ToolsClass::priceConvert($v['final_price']);
                $orderList[$k]['order_price'] = ToolsClass::priceConvert($v['final_price']).'元';
                if($k > 0){
                    if($this->getYearMonth($v['last_payment_at']) !== $this->getYearMonth($orderList[$k-1]['last_payment_at'])){
                        $orderList[$k]['month'] = ToolsClass::judgeDate($v['last_payment_at']);
                    }else{
                        $orderList[$k]['month'] = '';
                    }
                }
            }
            return $orderList;
        }
        return [];
    }
    /*
     * 获取某日期的年月
     */
    private function getYearMonth($date){
        return date('Y-m', strtotime($date));
    }
    /*
     * 获取订单某字段的数值之和
     */
    public function getOrdersFieldsSum($orders,$field){
        if(!is_array($orders)){
            return 0;
        }
        $res = self::find()->where(['in','id', $orders])->select("sum({$field}) as {$field}")->asArray()->one();
        return isset($res[$field]) ? intval($res[$field]) : 0;
    }
    /*
     * 申请合同
     */
    public function saveContact(){
        if(!$this->order_id){
            return false;
        }
        $order_ids = ToolsClass::explode(',',$this->order_id);
        $num = self::find()->where(['id'=>$order_ids, 'contact_status'=>[1,2]])->count();
        if(intval($num) > 0){return false;}
        return self::updateAll(['contact_status'=>1,'contact_at'=>date('Y-m-d H:i:s')], ['id'=>$order_ids]);
    }

    /*
     * 获取线下付款详情
     * */
    public function getRemittance($order_id){
        if(!self::find()->where(['id'=>$order_id,'member_id'=>Yii::$app->user->id])->count()){
            return false;
        }
        $configData = SystemConfig::getAllConfigById(['system_receiver_address','system_receiver_bank_name','system_receiver_bank_number','system_receiver_name']);
        $logPayment = LogPayment::find()->where(['order_id'=>$order_id])->select('payment_code,price')->orderBy('id desc')->asArray()->one();
        return [
            'system_receiver_address' => isset($configData['system_receiver_address']) ? $configData['system_receiver_address'] : '',
            'system_receiver_bank_name' => isset($configData['system_receiver_bank_name']) ? $configData['system_receiver_address'] : '',
            'system_receiver_bank_number' => isset($configData['system_receiver_bank_number']) ? $configData['system_receiver_address'] : '',
            'system_receiver_name' => isset($configData['system_receiver_name']) ? $configData['system_receiver_address'] : '',
            'payment_code' => isset($logPayment['payment_code']) ? $logPayment['payment_code'] : '',
            'order_price' => isset($logPayment['price']) ? (string)ToolsClass::priceConvert($logPayment['price']) : '',
        ];
    }
    /*
     *同意或取消购买协议
     */
    public function updateBuyAgree(){
        $obj = self::find()->where(['id'=>$this->id,'member_id'=>Yii::$app->user->id])->one();
        if(!$obj){
            return 'ORDER_NOT_EXIST';
        }
        try{

            $obj->buy_agreed = $this->buy_agreed;
            $re = $obj->save();
            if($re){
                return 'SUCCESS';
            }
            return 'ERROR';
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return 'ERROR';
        }
    }


    /*
     * 场景
     * */
    public function scenes()
    {
        return [
            'contact' => [
                'order_id' => [
                    'required'=>'1',
                ],
            ],
            'payment'=>[
                'order_code'=>[
                    'required'=>'1',
                    'result'=>'ORDER_CODE_EMPTY'
                ],
            ],
            'select-area'=>[
                'parent_id'=>[
                    'type'=>'int',
                ],
                'show_self'=>[
                    'type'=>'int',
                ],
                'type'=>[
                    'type'=>'int',
                ]
            ],
            'create'=>[
                'member_mobile' => [
                    [
                        [
                            'required'=>'1',
                            'result'=>'MEMBER_MOBILE_EMPTY'
                        ],
                        [
                            'function'=>'this::checkSalesmanMobile',
                            'result'=>'ORDER_SALESMAN_MOBILE_ERROR'
                        ],
                    ]
                ],
                'member_name' => [
                    'required' => 1,
                    'result' => 'MEMBER_NAME_EMPTY',
                ],
//                'salesman_mobile'=>[
//                    [
//                        [
//                            'required'=>'1',
//                            'result'=>'ORDER_SALESMAN_MOBILE_EMPTY'
//                        ],
//                        [
//                            'function'=>'this::checkSalesmanMobile',
//                            'result'=>'ORDER_SALESMAN_MOBILE_ERROR'
//                        ],
//                    ]
//                ],
                'advert_id'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'ORDER_ADVERT_ID_EMPTY'
                        ],
                        [
                            'function'=>'this::checkAdvertId',
                            'result'=>'ORDER_ADVERT_ID_ERROR'
                        ]
                    ]
                ],
                'rate'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'ORDER_RATE_EMPTY'
                        ],
                        [
                            'function'=>'this::reduceRateNumber',
                            'result'=>'ORDER_RATE_ERROR'
                        ]
                    ]
                ],
                'final_price' => [
                    'required'=>'1',
                    'type' => 'int',
                    'result'=>'ORDER_RATE_EMPTY'
                ],
                'preferential_way' => [],
                'advert_time'=>[
                    'required'=>'1',
                    'result'=>'ORDER_ADVERT_TIME_EMPTY'
                ],
                'payment_type'=>[
                    'required'=>'1',
                    'result'=>'ORDER_PAYMENT_EMPTY'
                ],
                'company_area_id'=>[
                    'required'=>'1',
                    'result'=>'COMPANY_AREA_ID_EMPTY'
                ],
                'start_at'=>[
                    'required'=>'1',
                    'result'=>'START_AT_EMPTY'
                ],
                'end_at'=>[
                    'required'=>'1',
                    'result'=>'END_AT_EMPTY'
                ],
                'remarks' => [],
            ],
            'index'=>[
                'type'=>[
                    'type' => 'int',
                ],
                'order_list'=>[
                    'type' => 'int',
                ],
            ],
            'view'=>[
                'order_list'=>[
                    'type'=>'int',
                ],
            ],
            'ordercomplain'=>[
                'order_id'=>[
                    'required'=>'1',
                    'type' => 'int',
                ],
            ],
            'prev-view'=>[
                'order_id'=>[
                    'required'=>'1',
                    'type' => 'int',
                ],
                'member_id'=>[
                    'required'=>'1',
                    'type' => 'int',
                ],
            ],
            //店铺同意购买协议
            'agree-buy-contract' => [
                'id' => [
                    'required'=>'1',
                    'type' => 'int',
                ],
                'buy_agreed' => [
                    'required'=>'1',
                    'type' => 'int',
                ]
            ],
        ];
    }
}
