<?php

namespace api\modules\v1\models;

use pms\modules\count\count;
use Yii;
use api\modules\v1\models\User;
/**
 * This is the model class for table "yl_auth_assignment".
 *
 * @property string $item_name
 * @property string $user_id
 * @property string $created_at
 */
class AuthAssignment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'yl_auth_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['item_name', 'user_id'], 'required'],
            [['created_at'], 'safe'],
            [['item_name', 'user_id'], 'string', 'max' => 64],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'item_name' => 'Item Name',
            'user_id' => 'User ID',
            'created_at' => 'Created At',
        ];
    }

    public function getUser(){
        return self::hasOne(User::className(),['id'=>'user_id']);
    }
    //获取用户信息
    public static function getUserInfo($where){
        $re = self::find()->joinWith('user',false)->where($where)->select('yl_user.member_group,yl_user.username')->orderBy('yl_user.member_group')->limit(2)->asArray()->all();
//        if(empty($re)){ return []; }
//        if(count($re) == 1){
//
//        }
        return $re;

    }

}
