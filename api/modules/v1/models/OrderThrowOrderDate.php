<?php

namespace api\modules\v1\models;

use Yii;
use yii\db\ActiveRecord;


class OrderThrowOrderDate extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->get('throw_db');
    }
    private static $partitionIndex_ = null;

    private static function resetPartitionIndex($order_id = null) {
        self::$partitionIndex_ = ceil($order_id/2000);
    }

    public static function tableName()
    {
        return '{{%order_throw_order_date_'.self::$partitionIndex_.'}}';
    }

    public static function getOrderData($order_id)
    {
        self::resetPartitionIndex($order_id);
        $orderData = OrderThrowOrderDate::find()->where(['order_id' => $order_id])->select('area_id,date')->asArray()->all();
        if (empty($orderData)) {
            return [];
        }
        $result = [];
        foreach ($orderData as $key => $value) {
             $result[] = str_replace('-','',$value['date']).'_'.$value['area_id'];
        }
        return $result;
    }

}