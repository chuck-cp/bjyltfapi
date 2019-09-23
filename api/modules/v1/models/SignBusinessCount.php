<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%sign_business_count}}".
 *
 * @property string $id
 * @property integer $total_sign_member_number
 * @property integer $overtime_sign_member_number
 * @property integer $no_sign_member_number
 * @property integer $unqualified_member_number
 * @property integer $total_sign_shop_number
 * @property integer $repeat_sign_number
 * @property double $repeat_sign_rate
 * @property integer $repeat_shop_number
 * @property string $create_at
 */
class SignBusinessCount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sign_business_count}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['total_sign_member_number', 'overtime_sign_member_number', 'no_sign_member_number', 'unqualified_member_number', 'total_sign_shop_number', 'repeat_sign_number', 'repeat_shop_number'], 'integer'],
            [['repeat_sign_rate'], 'number'],
            [['create_at'], 'required'],
            [['create_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'total_sign_member_number' => 'Total Sign Member Number',
            'overtime_sign_member_number' => 'Overtime Sign Member Number',
            'no_sign_member_number' => 'No Sign Member Number',
            'unqualified_member_number' => 'Unqualified Member Number',
            'total_sign_shop_number' => 'Total Sign Shop Number',
            'repeat_sign_number' => 'Repeat Sign Number',
            'repeat_sign_rate' => 'Repeat Sign Rate',
            'repeat_shop_number' => 'Repeat Shop Number',
            'create_at' => 'Create At',
        ];
    }
}
