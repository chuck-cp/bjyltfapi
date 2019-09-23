<?php

namespace wap\models;

use Yii;

/**
 * This is the model class for table "{{%order_area}}".
 *
 * @property string $id
 * @property string $order_id
 * @property string $street_area
 * @property string $street_screen_number
 * @property string $area_id
 * @property integer $area_type
 */
class OrderArea extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_area}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'area_id', 'area_type'], 'required'],
            [['order_id', 'area_type'], 'integer'],
            [['street_area', 'street_screen_number', 'area_id'], 'string'],
            [['order_id'], 'unique'],
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
            'street_area' => 'Street Area',
            'street_screen_number' => 'Street Screen Number',
            'area_id' => 'Area ID',
            'area_type' => 'Area Type',
        ];
    }
}
