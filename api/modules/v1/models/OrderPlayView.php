<?php

namespace api\modules\v1\models;

use common\libs\ToolsClass;
use Yii;
use api\modules\v1\models\Order;
/**
 * This is the model class for table "{{%order_play_view}}".
 *
 * @property integer $order_id
 * @property string $order_code
 * @property string $salesman_name
 * @property string $custom_service_name
 * @property string $advert_name
 * @property integer $advert_rate
 * @property string $advert_time
 * @property string $throw_area
 * @property string $throw_province_number
 * @property string $throw_city_number
 * @property string $throw_area_number
 * @property string $throw_street_number
 * @property string $throw_shop_number
 * @property string $throw_screen_number
 * @property string $throw_mirror_number
 * @property string $screen_run_time
 * @property string $total_play_number
 * @property string $total_play_time
 * @property integer $total_play_rate
 * @property string $total_watch_number
 * @property string $total_no_repeat_watch_number
 * @property string $total_people_watch_number
 * @property string $total_radiation_number
 * @property integer $total_arrival_rate
 * @property string $total_order_play_number
 * @property string $start_at
 * @property string $end_at
 * @property string $give_shop_number
 * @property string $give_screen_number
 * @property string $give_play_number
 * @property string $give_watch_number
 * @property string $give_radiation_number
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
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'order_code' => 'Order Code',
            'salesman_name' => 'Salesman Name',
            'custom_service_name' => 'Custom Service Name',
            'advert_name' => 'Advert Name',
            'advert_rate' => 'Advert Rate',
            'advert_time' => 'Advert Time',
            'throw_area' => 'Throw Area',
            'throw_province_number' => 'Throw Province Number',
            'throw_city_number' => 'Throw City Number',
            'throw_area_number' => 'Throw Area Number',
            'throw_street_number' => 'Throw Street Number',
            'throw_shop_number' => 'Throw Shop Number',
            'throw_screen_number' => 'Throw Screen Number',
            'throw_mirror_number' => 'Throw Mirror Number',
            'screen_run_time' => 'Screen Run Time',
            'total_play_number' => 'Total Play Number',
            'total_play_time' => 'Total Play Time',
            'total_play_rate' => 'Total Play Rate',
            'total_watch_number' => 'Total Watch Number',
            'total_no_repeat_watch_number' => 'Total No Repeat Watch Number',
            'total_people_watch_number' => 'Total People Watch Number',
            'total_radiation_number' => 'Total Radiation Number',
            'total_arrival_rate' => 'Total Arrival Rate',
            'total_order_play_number' => 'Total Order Play Number',
            'start_at' => 'Start At',
            'end_at' => 'End At',
            'give_shop_number' => 'Give Shop Number',
            'give_screen_number' => 'Give Screen Number',
            'give_play_number' => 'Give Play Number',
            'give_watch_number' => 'Give Watch Number',
            'give_radiation_number' => 'Give Radiation Number',
        ];
    }


    /**
     *根据传入字段获取数据
     */
    public static function getFields($order_id,$fields){
        $obj = OrderPlayView::find()->where(['order_id'=>$order_id]);
        if(!$obj){ return []; }
        $select = '';
        if(!$fields){
            $select = '*';
        }elseif (is_array($fields)){
            $attributes = (new self())->getAttributes();
            $finally = [];
            foreach (array_filter($fields) as $k => $v){
                if(array_key_exists($v,$attributes)){
                   $finally[] = $v;
                }
            }
            if(!empty($finally)){
                $select = implode(',',$finally);
            }else{
                $select = '*';
            }
        }else{
            $select = $fields;
        }
        return is_array($order_id) ? $obj->select($select)->asArray()->all() : $obj->select($select)->asArray()->one();
    }

    /**
     *判断是否可以查看该订单
     */
    public static function getOrderEndAt($order_id){
        $order = Order::find()->where(['member_id'=>Yii::$app->user->id, 'id'=>$order_id])->count();
        if(!$order){
            return false;
        }
        $endAt = self::getFields($order_id,'end_at');
        $dayNum = ToolsClass::diffTimeByToDay($endAt['end_at']);
        return intval($dayNum) > 6;
    }
}
