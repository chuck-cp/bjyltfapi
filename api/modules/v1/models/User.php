<?php

namespace api\modules\v1\models;
use api\modules\v1\models\AuthAssignment;
use Yii;

/**
 * This is the model class for table "yl_user".
 *
 * @property integer $id
 * @property string $username
 * @property string $true_name
 * @property string $phone
 * @property string $email
 * @property string $password_hash
 * @property string $create_at
 * @property string $update_at
 * @property integer $status
 * @property integer $type
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'yl_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'phone', 'email', 'password_hash'], 'required'],
            [['create_at', 'update_at'], 'safe'],
            [['status', 'type'], 'integer'],
            [['username', 'true_name', 'email'], 'string', 'max' => 50],
            [['phone'], 'string', 'max' => 20],
            [['password_hash'], 'string', 'max' => 255],
            [['username'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'true_name' => 'True Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'password_hash' => 'Password Hash',
            'create_at' => 'Create At',
            'update_at' => 'Update At',
            'status' => 'Status',
            'type' => 'Type',
        ];
    }


}
