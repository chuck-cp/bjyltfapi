<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%member_reward_detail}}".
 *
 * @property string $id
 * @property string $reward_member_id
 * @property string $member_id
 * @property string $reward_price
 * @property string $order_price
 * @property string $create_at
 */
class MemberRewardDetail extends ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_reward_detail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reward_member_id', 'member_id', 'reward_price', 'order_price'], 'integer'],
            [['create_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reward_member_id' => 'Reward Member ID',
            'member_id' => 'Member ID',
            'reward_price' => 'Reward Price',
            'order_price' => 'Order Price',
            'create_at' => 'Create At',
        ];
    }
}
