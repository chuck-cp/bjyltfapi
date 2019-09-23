<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%order_play_view_date}}".
 *
 * @property string $order_id
 * @property string $date
 * @property string $throw_number
 */
class OrderPlayViewDate extends \yii\db\ActiveRecord
{
    const LIMIT_NUMBER = 3;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_play_view_date}}';
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
            'date' => 'Date',
            'throw_number' => 'Throw Number',
        ];
    }

    public static function getRank($order_id){
        return self::find()->where(['order_id'=>$order_id])->orderBy(['throw_number'=>SORT_DESC,'date'=>SORT_DESC])->limit(self::LIMIT_NUMBER)->asArray()->all();
    }
}
