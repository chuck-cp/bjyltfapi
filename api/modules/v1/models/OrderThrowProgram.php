<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%order_throw_program}}".
 *
 * @property integer $id
 * @property integer $throw_id
 * @property string $date
 */
class OrderThrowProgram extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_throw_program}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['throw_id', 'date'], 'required'],
            [['throw_id'], 'integer'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'throw_id' => 'Throw ID',
            'date' => 'Date',
        ];
    }
}
