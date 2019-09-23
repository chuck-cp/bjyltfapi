<?php

namespace api\modules\v1\models;

use Yii;
use yii\base\Exception;

/**
 * 用户设备
 */
class MemberEquipment extends \api\core\ApiActiveRecord
{
    public static function tableName()
    {
        return '{{%member_equipment}}';
    }

    public function updatePush(){
        if($this->push_status != 1){
            $this->push_status = 2;
        }
        return self::updateAll(['push_status'=>$this->push_status],['token'=>Yii::$app->request->post('token')]);
    }

    public function getPushStatus(){
        return self::find()->where(['token'=>Yii::$app->request->get('token')])->select('push_status')->asArray()->one();
    }

    /*
     * 新设备登录时写入极光id
     */
    public function updatePushId(){
        try{
            self::updateAll(['push_id'=>$this->push_id],['token'=>Yii::$app->request->post('token')]);
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }
    //验证token是否正确
    public static function checkTokenIsTrue($token){
        return self::find()->where(['token'=>$token,'status'=>1])->one();
    }
    public function scenes(){
        return [
            'update-push'=>[
                'push_status'=>[
                    'required'=>1,
                    'type'=>'int',
                    'result'=>'PUSH_STATUS_EMPTY',
                ],
            ],
            'update-pid'=>[
                'push_id'=>[
                    'required'=>1,
                    'result'=>'PUSH_ID_EMPTY',
                ],
            ],
        ];
    }

    /**
     * Returns static class instance, which can be used to obtain meta information.
     * @param bool $refresh whether to re-create static instance even, if it is already cached.
     * @return static class instance.
     */
    public static function instance($refresh = false)
    {
        // TODO: Implement instance() method.
    }
}
