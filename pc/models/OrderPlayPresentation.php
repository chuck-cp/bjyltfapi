<?php

namespace pc\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * 用户设备
 */
class OrderPlayPresentation extends ActiveRecord
{
    public static function getDb()
    {
        return Yii::$app->get('throw_db');
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_play_presentation}}';
    }
}