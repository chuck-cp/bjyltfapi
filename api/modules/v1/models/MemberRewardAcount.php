<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "{{%member_reward_acount}}".
 *
 * @property string $member_id
 * @property string $reward_price
 */
class MemberRewardAcount extends ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_reward_acount}}';
    }

    public static function getTotalAccount(){
        $obj = self::find()->where(['member_id'=>Yii::$app->user->id])->one();
        return $obj ? $obj->reward_price : 0;
   }
    public function updateRewardAgreed(){
       $where  = [];
       //冻结奖励金
       $freezeMoney = 0;
       $where['member_id'] = Yii::$app->user->id;
       $where['shop_id'] = $this->shop_id;
       $where['shop_type'] = $this->shop_type;
       $currentObj = self::find()->where($where)->one();
        if(!$currentObj){
            $currentObj = new self();
            $currentObj->member_id = Yii::$app->user->id;
            $currentObj->shop_id = $this->shop_id;
            $currentObj->shop_type = $this->shop_type;
        }else{
            $freezeMoney = $currentObj->reward_price;
        }
        $trans = Yii::$app->db->beginTransaction();
        try{
            $currentObj->agreed = $this->agreed;
            $currentObj->save();
            //释放冻结奖励金
            if($freezeMoney > 0){
                MemberAccount::updateAll(
                    [
                        'balance' => new Expression('balance+'.$freezeMoney),
                        'reward_frozen_price' => new Expression('reward_frozen_price-'.$freezeMoney)
                    ],
                    ['member_id'=>Yii::$app->user->id]
                );
            }
            $trans->commit();
            return 'SUCCESS';
        }catch (Exception $e){
            Yii::error($e->getMessage(),'error');
            $trans->rollBack();
            return 'ERROR';
        }
    }

    public function scenes(){
        return [
            //agreed 奖励金协议
            'agree-reward'=>[
                'agreed' => [
                    'required' => '1',
                    'result' => 'REWARD_AGREE_EMPTY',
                ],
                'shop_id' => [
                    'required' => '1',
                    'result' => 'REWARD_SHOP_ID_EMPTY',
                ],
                'shop_type' => [
                    'required' => '1',
                    'result' => 'REWARD_SHOP_TYPE_EMPTY',
                ],
            ],
        ];
    }
}
