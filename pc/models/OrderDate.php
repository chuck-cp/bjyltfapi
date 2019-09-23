<?php

namespace pc\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * 用户设备
 */
class OrderDate extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%order_date}}';
    }
    /*
  * 获取某广告连续投放日期
  */
    public static function getOrderDateSeries($Oid){
        $dateObj=self::find()->select('id,start_at,end_at')->where(['order_id'=>$Oid])->asArray()->one();
        $begintime = strtotime($dateObj['start_at']);
        $endtime = strtotime($dateObj['end_at']);
        for ($start = $begintime; $start <= $endtime; $start += 24 * 3600) {
            $datelist[] = date("Y-m-d", $start);
        }
        return $datelist;
    }
}