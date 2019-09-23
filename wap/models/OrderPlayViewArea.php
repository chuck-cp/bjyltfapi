<?php

namespace wap\models;

use Yii;

/**
 * This is the model class for table "{{%order_play_view_area}}".
 *
 * @property integer $order_id
 * @property string $area_name
 * @property string $throw_number
 * @property integer $throw_rate
 */
class OrderPlayViewArea extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_play_view_area}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('throw_db');
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['order_id', 'area_name'], 'required'],
            [['order_id', 'throw_number', 'throw_rate'], 'integer'],
            [['area_name'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'area_name' => 'Area Name',
            'throw_number' => 'Throw Number',
            'throw_rate' => 'Throw Rate',
        ];
    }
}
