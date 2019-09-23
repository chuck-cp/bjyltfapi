<?php

namespace wap\models;

use Yii;
use yii\base\Exception;
use yii\web\IdentityInterface;

/**
 * member表的model
 */
class Member extends \yii\db\ActiveRecord implements IdentityInterface
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

    public static function findIdentityByAccessToken($token, $type = null){
        return MemberEquipment::findOne(['token' => $token,'status'=>1]);
    }

    public function getId(){
        return $this->getPrimaryKey();
    }

    public function getAuthKey(){

    }

    public function validateAuthKey($authKey){
        return $this->getAuthKey() === $authKey;
    }
    //是否是内部人员
    public static function isInside($member_id){
        if(!$member_id) {return false;}
        $memberObj = self::findOne($member_id);
        if(!$memberObj){ return false; }
        return $memberObj->inside;
    }
}
