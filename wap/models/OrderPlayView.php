<?php

namespace wap\models;

use Yii;

/**
 * This is the model class for table "{{%order_play_view}}".
 *
 * @property integer $order_id
 * @property string $order_code
 * @property string $salesman_name
 * @property string $custom_service_name
 * @property string $advert_name
 * @property integer $rate
 * @property string $advert_time
 * @property string $area_name
 * @property integer $throw_province_number
 * @property integer $throw_city_number
 * @property integer $throw_area_number
 * @property integer $throw_street_number
 * @property string $throw_shop_number
 * @property string $throw_screen_number
 * @property string $total_play_number
 * @property string $total_play_time
 * @property integer $total_play_rate
 * @property string $total_watch_number
 * @property integer $large_shop_rate
 * @property integer $medium_shop_rate
 * @property integer $small_shop_rate
 * @property string $start_at
 * @property string $end_at
 */
class OrderPlayView extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_play_view}}';
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
            [['order_id', 'order_code', 'salesman_name', 'custom_service_name', 'advert_name', 'advert_time', 'area_name', 'total_play_time', 'medium_shop_rate', 'start_at', 'end_at'], 'required'],
            [['order_id', 'rate', 'throw_province_number', 'throw_city_number', 'throw_area_number', 'throw_street_number', 'throw_shop_number', 'throw_screen_number', 'total_play_number', 'total_play_time', 'total_play_rate', 'total_watch_number', 'large_shop_rate', 'medium_shop_rate', 'small_shop_rate'], 'integer'],
            [['start_at', 'end_at'], 'safe'],
            [['order_code'], 'string', 'max' => 20],
            [['salesman_name', 'custom_service_name', 'advert_name'], 'string', 'max' => 50],
            [['advert_time'], 'string', 'max' => 5],
            [['area_name'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'order_code' => 'Order Code',
            'salesman_name' => 'Salesman Name',
            'custom_service_name' => 'Custom Service Name',
            'advert_name' => 'Advert Name',
            'rate' => 'Rate',
            'advert_time' => 'Advert Time',
            'area_name' => 'Area Name',
            'throw_province_number' => 'Throw Province Number',
            'throw_city_number' => 'Throw City Number',
            'throw_area_number' => 'Throw Area Number',
            'throw_street_number' => 'Throw Street Number',
            'throw_shop_number' => 'Throw Shop Number',
            'throw_screen_number' => 'Throw Screen Number',
            'total_play_number' => 'Total Play Number',
            'total_play_time' => 'Total Play Time',
            'total_play_rate' => 'Total Play Rate',
            'total_watch_number' => 'Total Watch Number',
            'large_shop_rate' => 'Large Shop Rate',
            'medium_shop_rate' => 'Medium Shop Rate',
            'small_shop_rate' => 'Small Shop Rate',
            'start_at' => 'Start At',
            'end_at' => 'End At',
        ];
    }
}
