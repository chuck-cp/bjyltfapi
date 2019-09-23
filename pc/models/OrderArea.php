<?php

namespace pc\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * 用户设备
 */
class OrderArea extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%order_area}}';
    }
    /*
       * 获取订单下具体的街道list
       */
    public static function getStreetsByOrderId($oid){
        if($oid){
            $obj = self::find()->where(['order_id'=>$oid]);
            if(!$obj){
                return [];
            }
            return $obj->select('street_area')->asArray()->one();
        }
        return [];
    }
}