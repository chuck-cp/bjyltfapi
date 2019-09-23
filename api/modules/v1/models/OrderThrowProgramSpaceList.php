<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%order_throw_program_space_list}}".
 *
 * @property string $id
 * @property integer $spece_id
 * @property integer $order_id
 */
class OrderThrowProgramSpaceList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_throw_program_space_list}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['spece_id', 'order_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'spece_id' => 'Spece ID',
            'order_id' => 'Order ID',
        ];
    }
}
