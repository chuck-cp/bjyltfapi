<?php

namespace pc\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * 广告位
 */
class AdvertPosition extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%advert_position}}';
    }
    public function type($advert_id)
    {
    	return self::find()->where(['id'=>$advert_id])->select('type')->asArray()->one()['type'];
    	 
    }
    public function advertname()
    {
    	return self::find()->select('name,id')->asArray()->all();
    }
}