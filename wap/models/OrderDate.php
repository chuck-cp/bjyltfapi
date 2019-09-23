<?php

namespace wap\models;

use Yii;

/**
 * This is the model class for table "{{%order_date}}".
 *
 * @property string $id
 * @property string $order_id
 * @property string $start_at
 * @property string $end_at
 * @property integer $is_update
 */
class OrderDate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_date}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'start_at', 'end_at'], 'required'],
            [['order_id', 'is_update'], 'integer'],
            [['start_at', 'end_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'is_update' => 'Is Update',
        ];
    }
}
