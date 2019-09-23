<?php

namespace api\modules\v1\models;

use pms\modules\count\count;
use Yii;

/**
 * This is the model class for table "{{%order_play_view_area}}".
 *
 * @property string $order_id
 * @property string $area_name
 * @property string $throw_number
 */
class OrderPlayViewArea extends \yii\db\ActiveRecord
{
    const LIMIT_NUMBER = 3;
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
    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'area_name' => 'Area Name',
            'throw_number' => 'Throw Number',
        ];
    }

    public static function getArea($order_id){
        $rank = self::find()->where(['order_id'=>$order_id])->orderBy('throw_number DESC')->asArray()->limit(self::LIMIT_NUMBER)->all();
        $data =  self::find()->where(['order_id'=>$order_id])->orderBy('throw_number DESC')->asArray()->all();
        if(empty($data)){ return $data; }
        $total = array_sum(array_column($data,'throw_number'));
        $re = [];
        $four = 0;
        foreach ($data as $k => $v){
            if($k < 4){
                $re[$k]['area'] = $v['area_name'];
                $re[$k]['throw_number'] = $v['throw_number'];
                $re[$k]['rate'] = floatval(number_format(($v['throw_number']/$total)*100,2));
                $four += $v['throw_number'];
            }
        }
        if(count($data) > 4){
            array_push($re,[
                'area' => '其他',
                'throw_number' => $total - $four,
                'rate' => floatval(number_format((($total - $four)/$total)*100,2)),
            ]);
        }
        return compact('re','rank');
    }




}
