<?php

namespace pc\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * 订单状态改变消息
 */
class SystemConfig extends ActiveRecord
{
    public static function tableName()
    {
        return '{{%system_config}}';
    }

    public function getConfigById($id){
        return self::find()->where(['id'=>$id])->select('content')->asArray()->one();
    }
}