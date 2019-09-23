<?php

namespace api\modules\v1\models;

use common\libs\ToolsClass;
use Yii;
use yii\base\Exception;
use yii\data\Pagination;

/**
 * 流水
 */
class LogAccount extends \api\core\ApiActiveRecord
{
    const CREATE_SHOP_TO_KEEPER = 20000;
    const CREATE_SHOP_TO_SALESMAN = 15000;
    const CREATE_SHOP_TO_NOSALESMAN = 10000;

    public static function tableName()
    {
        return '{{%log_account}}';
    }

    public function getMemberAccountList(){
        $accountModel = self::find()->where(['member_id'=>Yii::$app->user->id])->select('title,desc,type,price,status,create_at')->andFilterWhere(['type'=>$this->type]);
        if($this->create_at){
            $start_at = $this->create_at.'-01 00:00:00';
            $end_at = date('Y-m-d H:i:s',strtotime('+1 month',strtotime($start_at)));
            $accountModel = $accountModel->andWhere(['and',['>=','create_at',$start_at],['<','create_at',$end_at]]);
        }
        $pagination = new Pagination(['totalCount'=>$accountModel->count()]);
        $pagination->validatePage = false;
        $resultAccount = $accountModel->orderBy("id desc")->limit($pagination->limit)->offset($pagination->offset)->asArray()->all();
        if(empty($resultAccount)){
            return [];
        }
        foreach($resultAccount as $key=>$account){
            $resultAccount[$key]['price'] = ToolsClass::priceConvert($account['price']);
            $resultAccount[$key]['create_at'] = date('Y-m-d',strtotime(($account['create_at'])));
        }
        return $resultAccount;
    }

    /*
     * 获取日期列表
     * */
    public function getDateList(){
        $createList = MemberAccountCount::find()->where(['member_id'=>Yii::$app->user->id])->select('create_at')->asArray()->all();
        if(empty($createList)){
            return [];
        }
        foreach($createList as $date){
            $dateMatch = explode("-",$date['create_at']);
            $resultDate[$dateMatch[0]][] = $dateMatch[1];
        }
        foreach($resultDate as $key=>$date){
            $resultReform[] = [
                'years'=>(string)$key,
                'month'=>$date
            ];
        }
        return $resultReform;
    }

    /*
     * 获取金钱统计
     * */
    public function getPriceCount(){
        $income = 0;
        $pay = 0;
        if((int)\Yii::$app->request->get('page') < 2){
            if(empty($this->create_at)){
                $accountModel = MemberAccount::find()->where(['member_id'=>Yii::$app->user->id])->select('withdraw_price,count_price,frozen_price')->asArray()->one();
            }else{
                $accountModel = MemberAccountCount::find()->where(['member_id'=>Yii::$app->user->id,'create_at'=>$this->create_at])->select('withdraw_price,count_price,frozen_price')->asArray()->one();
            }
        }
        if(!empty($accountModel)){
            $pay = ToolsClass::priceConvert($accountModel['withdraw_price']);
            $income = ToolsClass::priceConvert($accountModel['count_price'] + $accountModel['frozen_price']);
        }
        return [$income,$pay];
    }

    /*
     * 获取店主应获得的金额
     * */
    public static function getKeeperPrice($area){
        $area = substr($area,0,9);
        $systemZone = SystemAddressLevel::find()->where(['area_id'=>$area,'type'=>1])->select('level')->asArray()->one();
        if(empty($systemZone)){
            return ['price'=>SystemConfig::getAreaInstallPrice($area,'system_price_first_install_'),'month_price'=>SystemConfig::getAreaInstallPrice($area,'system_price_subsidy_')];
        }
        return ['price'=>SystemConfig::getAreaInstallPrice($area,'system_price_first_install_'),'month_price'=>SystemConfig::getAreaInstallPrice($area,'system_price_subsidy_')];
    }

    public static function getMemberPrice($member_id){
        $update_status = 0;
        //if(Member::getMemberFieldById('member_type') == 1){
        if(Member::getMemberFieldByWhere(['id'=>$member_id],'member_type') == 1){
            $price = LogAccount::CREATE_SHOP_TO_NOSALESMAN;
        }else{
            $price = LogAccount::CREATE_SHOP_TO_SALESMAN;
        }
        return [$price,$update_status];
    }
    /*
     * 写日志
     * @param int price 金额
     * @param int type 类型(1、收入 2、支出)
     * @param string desc 描述
     * @param int member_id 用户ID
     * */
    public static function writeLog($price,$type=1,$desc='',$shop_name,$member_id=0,$screen_number=0,$area=0,$shop_number=0){
        if(empty($member_id) || empty($price)){
            return true;
        }
        try{
            //$shop_number = $desc == '店铺费用' ? 0 : 1;
            $logModel = new LogAccount();
            $logModel->member_id = $member_id;
            $logModel->type = $type;
            $logModel->before_price = MemberAccount::getMemberPrice($member_id);
            $logModel->price = $price;
            $logModel->account_type = 1;
            $logModel->title = $desc;
            $logModel->desc = $shop_name;
            $logModel->save();
            $accountModel = MemberAccount::getOrCreateAccount($member_id);
            $accountModel->loadAccount($type,$price,$screen_number,$shop_number);
            $accountModel->save();
            $countModel = MemberAccountCount::getOrCreateAccount($member_id);
            $countModel->loadAccountCount($type,$price,$screen_number,$shop_number);
            $countModel->save();
            $messageModel = new MemberAccountMessage();
            $messageModel->member_id = $member_id;
            $str = '';
            if($type == 1){
                $str = '收入'.$desc;
            }elseif ($type == 2){
                $str = '支出'.$desc;
            }else{
                $str = $desc;
            }
            if($desc == '业务合作人红包'){
                $str = '成为业务合作人现金红包';
            }
            $messageModel->title = $str.ToolsClass::priceConvert($price).'元';
            $messageModel->save();

            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }

    }
    /*
     * 场景
     * */
    public function scenes(){
        return [
            'index'=>[
                'type'=>[],
                'create_at'=>[],
            ],
        ];
    }
}
