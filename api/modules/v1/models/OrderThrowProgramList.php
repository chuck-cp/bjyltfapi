<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%order_throw_program_list}}".
 *
 * @property string $id
 * @property integer $throw_id
 * @property integer $order_id
 * @property integer $time
 */
class OrderThrowProgramList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_throw_program_list}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['throw_id', 'order_id', 'time'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'throw_id' => 'Throw ID',
            'order_id' => 'Order ID',
            'time' => 'Time',
        ];
    }
}
