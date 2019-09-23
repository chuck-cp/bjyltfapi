<?php

namespace api\modules\v1\models;

use Yii;

/**
 * This is the model class for table "{{%sign_maintain_count}}".
 *
 * @property string $id
 * @property integer $total_sign_member_number
 * @property integer $overtime_sign_member_number
 * @property integer $no_sign_member_number
 * @property integer $unqualified_member_number
 * @property integer $total_evaluate_number
 * @property integer $good_evaluate_number
 * @property integer $middle_evaluate_number
 * @property integer $bad_evaluate_number
 * @property double $bad_evaluate_rate
 * @property string $create_at
 */
class SignMaintainCount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sign_maintain_count}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['total_sign_member_number', 'overtime_sign_member_number', 'no_sign_member_number', 'unqualified_member_number', 'total_evaluate_number', 'good_evaluate_number', 'middle_evaluate_number', 'bad_evaluate_number'], 'integer'],
            [['bad_evaluate_rate'], 'number'],
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
            'total_evaluate_number' => 'Total Evaluate Number',
            'good_evaluate_number' => 'Good Evaluate Number',
            'middle_evaluate_number' => 'Middle Evaluate Number',
            'bad_evaluate_number' => 'Bad Evaluate Number',
            'bad_evaluate_rate' => 'Bad Evaluate Rate',
            'create_at' => 'Create At',
        ];
    }
}
