<?php

namespace wap\models;

use Yii;

/**
 * This is the model class for table "{{%order_play_presentation}}".
 *
 * @property string $id
 * @property integer $order_id
 * @property string $data_list
 * @property integer $play_total
 * @property integer $should_total
 * @property integer $percentage
 */
class OrderPlayPresentation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_play_presentation}}';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('throw_db');
    }

}
