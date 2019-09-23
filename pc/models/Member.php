<?php

namespace pc\models;
use Yii;
use yii\base\Exception;
use yii\web\IdentityInterface;
use common\libs\ToolsClass;

/**
 * member表的model
 */
class Member extends \api\core\ApiActiveRecord implements IdentityInterface
{
    /**
     * 表名
     */
    public static function tableName()
    {
        return '{{%member}}';
    }

    public static function findIdentity($id){
        return static::findOne(['id' => $id]);
    }

    public static function findIdentityByMobile($mobile){
        return static::findOne(['mobile' => $mobile]);
    }

    public static function findIdentityByAccessToken($token, $type = null){
        return static::findOne(['token' => $token,'status'=>1]);
    }

    public function getId(){
        return $this->getPrimaryKey();
    }

    public function getAuthKey(){

    }

    public function validatePassword($mobile,$password){
        $resultCurl = ToolsClass::curl(\Yii::$app->params['memberServerUrl'].'/Login/rest',[
            'type'=>2,
            'username'=>$mobile,
            'password'=>$password
        ],'POST',1);
        $arr = json_decode($resultCurl,true);
        return true;
		//var_dump($arr);die;
        if($arr['msg'] == '登录成功'){
           return true;
        }else{
           return false;
        }
    }

    public function validateAuthKey($authKey){
        return $this->getAuthKey() === $authKey;
    }


    /*
    * 验证手机号是否存在
    * */
    public static function checkMobile($mobile){
        if(empty($mobile)){
            return false;
        }
        return Member::find()->where(['mobile'=>$mobile])->count();
    }

    /*
    * 获取用户名称
    * */
    public function check($member_id){
        if($memberModel = self::find()->where(['id'=>$member_id])->select('name')->asArray()->one()){
            return $memberModel['name'];
        }
    }

    public function getOneData($uid){
        if($memberModel = Member::find()->where(['id'=>$uid])->asArray()->one()){
            return $memberModel;
        }
    }
}
