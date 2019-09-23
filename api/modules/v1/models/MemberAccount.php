<?php

namespace api\modules\v1\models;

use common\libs\ToolsClass;
use Yii;
use yii\base\Exception;
use yii\db\Expression;

/*
 * 业绩总表
 * */
class MemberAccount extends \api\core\ApiActiveRecord
{

    const INCOME = 1;
    const PAY = 2;

    public static function tableName()
    {
        return '{{%member_account}}';
    }

    public static function getMemberPrice($member_id=0){
        $member_id = empty($member_id) ? Yii::$app->user->id : $member_id;
        $accountModel = MemberAccount::find()->where(['member_id'=>$member_id])->select('balance')->limit(1)->asArray()->one();
        if($accountModel){
            return $accountModel['balance'];
        }
        return 0;
    }

    public static function getMemberShopNumber(){
        $accountModel = MemberAccount::find()->where(['member_id'=>Yii::$app->user->id])->select('shop_number')->limit(1)->asArray()->one();
        if($accountModel){
            return $accountModel['shop_number'];
        }
        return false;
    }

    public function getMemberAccount(){
        $accountModel = self::find()->where(['member_id'=>Yii::$app->user->id])->select('balance,frozen_price,install_screen_number,shop_number,order_number,reward_frozen_price')->asArray()->one();
        if(empty($accountModel)){
            return [
                'balance'=>0,
                'screen_number'=>0,
                'shop_number'=>0
            ];
        }else{
            $accountModel['screen_number'] = $accountModel['install_screen_number'];
            $accountModel['balance'] = (string)ToolsClass::priceConvert($accountModel['balance'] + $accountModel['frozen_price'] + $accountModel['reward_frozen_price']);
        }
        return $accountModel;
    }

    /*
   * 写入金钱信息
   * */
    public function loadAccount($type,$price,$screen_number=0,$shop_number = 0){
        if($type == self::INCOME){
            $this->count_price += $price;
            $this->balance += $price;
            if($screen_number > 0){
                $this->screen_number += $screen_number;
                $this->shop_number += $shop_number;
            }
        }elseif($type == self::PAY){
            $this->balance -= $price;
        }
    }

    public static function getOrCreateAccount($member_id){
        $accountModel = new MemberAccount();
        if($resultModel = $accountModel->findOne($member_id)){
            return $resultModel;
        }
        $accountModel->member_id = $member_id;
        if($accountModel->save()){
            return $accountModel;
        }
        return false;
    }

    /*
     * 解冻成为正式人员之前冻结的金额
     */
    public static function unfreezeMoneyById($member_id){
        try{
            $accountModel = self::findOne($member_id);
            if(empty($accountModel)){
                throw new Exception("用户账户数据为空");
            }
            if($accountModel->informal_frozen > 0){
                $informal_frozen = $accountModel->informal_frozen;
                self::updateAll(['balance'=>new Expression('balance + '.$informal_frozen),'frozen_price'=>new Expression('frozen_price - '.$informal_frozen),'informal_frozen'=>new Expression('informal_frozen - '.$informal_frozen)],['member_id'=>$member_id]);
            }
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }

}
