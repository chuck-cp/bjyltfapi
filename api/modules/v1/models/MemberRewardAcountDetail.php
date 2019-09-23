<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%member_reward_acount_detail}}".
 *
 * @property string $id
 * @property string $member_id
 * @property string $reward_price
 * @property string $create_at
 */
class MemberRewardAcountDetail extends ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_reward_acount_detail}}';
    }
    //总奖励金
    public static function getTotalAccount($arr){
        if(!$arr){
            return false;
        }
        $where = [];
        $where = ['member_id'=>Yii::$app->user->id];
        if(isset($arr['head_id'])){
            $where['shop_id'] = $arr['head_id'];
            $where['shop_type'] = 2;
        }elseif(isset($arr['shop_id'])){
            $where['shop_id'] = $arr['shop_id'];
            $where['shop_type'] = 1;
        }
        $account = MemberRewardAcount::find()->where($where)->asArray()->one();
        return empty($account) ? ['reward_price'=>0, 'account_id'=>0] : ['reward_price'=>$account['reward_price'],'account_id'=>$account['id']];
    }
    //获取昨日奖励金
    public static function getYestodayAccount($account_id){
        $where = [];
        $where['create_at'] = date("Y-m-d",strtotime("-1 day"));
        $where['account_id'] = $account_id;
        $res = self::find()->where($where)->select('reward_price')->asArray()->one();
        return empty($res) ? 0 : $res['reward_price'];
    }


}
