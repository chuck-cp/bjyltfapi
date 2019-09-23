<?php

namespace pc\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * 用户设备
 */
class OrderThrowOrderDate extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->get('throw_db');
    }
    private static $partitionIndex_ = null; // 分表ID
    /**
     * 重置分区id
     * @param unknown $order_id
     */
    private static function resetPartitionIndex($order_id = null) {
        // $partitionCount = 2000;
        self::$partitionIndex_ = ceil($order_id/2000);
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_throw_order_date_'.self::$partitionIndex_.'}}';
    }

    public static function findtrue($order_id,$area,$datelist){
        $redisobj = Yii::$app->redis;
        $redisobj->select(4);
        $total_day = count($datelist);
        if(!empty($datelist)){
            foreach($area as $key=>$value){
                foreach ($datelist as $kd=>$vd){
                    $newdate[$value][$vd] = $redisobj->getbit('order_buy_result:'.$order_id,($key * $total_day) + $kd + 1);
                }
            }
            return $newdate;
        }else{
            return array();
        }
    }
}