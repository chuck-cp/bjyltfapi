<?php

namespace api\modules\v1\models;

use common\libs\DataClass;
use common\libs\ToolsClass;
use Yii;
use yii\base\Exception;
use yii\db\Expression;

/**
 * 提现
 */
class MemberWithdraw extends \api\core\ApiActiveRecord
{
    public $payment_password;
    public $bank_id;
    public static function tableName()
    {
        return '{{%member_withdraw}}';
    }

    /*
     * 提现前加载数据
     * */
    public function loadBank(){
        $bankModel = MemberBank::find()->where(['id'=>$this->bank_id, 'status' => 1])->select('name,bank_name,mobile,number,type,bank_id')->asArray()->one();
        if(empty($bankModel)){
            return false;
        }
        if($bankModel['type'] == 2){
            $this->bank_name = SystemBank::systemBanks()[$bankModel['bank_id']]['name'].$bankModel['bank_name'];
        }else{
            $this->bank_name = $bankModel['bank_name'];
        }
        $this->bank_mobile = $bankModel['mobile'];
        $this->payee_name = $bankModel['name'];
        $this->bank_account = $bankModel['number'];
        $this->account_type = $bankModel['type'];
        return true;
    }

    public function beforeSave($insert){
        if($insert){
            $userModel = Yii::$app->user->identity;
            $this->member_id = $userModel->id;
            $this->member_name = $userModel->name;
            $this->mobile = $userModel->mobile;
            $this->serial_number = Yii::$app->user->id.time();
        }
        return parent::beforeSave($insert);
    }
    /*
     * 提现
     * */
    public function withdraw()
    {
        $this->price = ToolsClass::priceConvert($this->price,2);
        if($this->price < 100){
            return 'WITHDRAW_PRICE_MIN_ONE';
        }
        $accountModel = MemberAccount::findOne(['member_id'=>Yii::$app->user->id]);
        if(!$accountModel){
            return 'MEMBER_NO_BALANCE';
        }
        if($accountModel->balance < $this->price){
            return 'MEMBER_NO_BALANCE';
        }

        $dbTrans = Yii::$app->db->beginTransaction();
        try{
            $logModel = new LogAccount();
            $logModel->account_type = -1;
            $logModel->title = '提现申请';
            $logModel->member_id = Yii::$app->user->id;
            $logModel->type = 2;
            $logModel->before_price = MemberAccount::getMemberPrice();
            $logModel->price =  $this->price;
            $logModel->save();
            /*
            $accountModel->balance -= $this->price;
            $accountModel->save();
            */
            if (!MemberAccount::updateAll(['balance'=>new Expression('balance - '.$this->price)],['and',['member_id'=>Yii::$app->user->id],['>=','balance',$this->price]])) {
                throw new \Exception('提现失败，提现金额不能大于余额');
            }
            $this->account_id = $logModel->id;
            $this->save();
            $dbTrans->commit();
            return "SUCCESS";
        }catch (Exception $e){
            Yii::warning($e->getMessage(),'db');
            $dbTrans->rollBack();
            return false;
        }
    }

    /*
     * 如果提现金额大于配置金额,需判断用户是否上传身份证
     * */
    public function checkMemberId(){
//        if($this->price > SystemConfig::getConfig("sales_money")){
//            return MemberInfo::find()->where(['member_id'=>Yii::$app->user->id,'examine_status'=>1])->asArray()->count();
//        }
//        return true;
        return MemberInfo::find()->where(['member_id'=>Yii::$app->user->id,'examine_status'=>1])->asArray()->count();
    }

    /*
     * 场景
     * */
    public function scenes(){
        return [
            'create'=>[
                'payment_password'=>[
                    'required'=>'1',
                    'result'=>'PAYMENT_PASSWORD_EMPTY'
                ],
                'bank_id'=>[
                    'required'=>'1',
                    'result'=>'BANK_ID_EMPTY'
                ],
                'price'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'WITHDRAW_PRICE_EMPTY'
                        ],
                        [
                            'function'=>'this::checkMemberId',
                            'result'=>'WITHDRAW_PRICE_EXCEED_MAX'
                        ]
                    ]
                ],
            ],
        ];
    }
}
