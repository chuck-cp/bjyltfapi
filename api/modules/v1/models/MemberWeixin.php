<?php

namespace api\modules\v1\models;

use Yii;
use yii\db\Exception;

/**
 * 用户微信信息
 */
class MemberWeixin extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_weixin}}';
    }

    public static function getOrCreate($id){
        if(empty($id)){
            return false;
        }
        if($model = MemberWeixin::find()->where(['id'=>$id])->asArray()->one()){
            return $model;
        }
        try{
            $model = new MemberWeixin();
            $model->id = $id;
            $model->save();
            return ['id'=>$id,'member_id'=>0];
        }catch (Exception $e){
            Yii::error("[create_member_weixin]".$e->getMessage(),'db');
            return false;
        }
    }

    public static function validateToken($id,$token){
        $weixinModel = MemberWeixin::find()->where(['id'=>$id])->one();
        if(empty($weixinModel)){
            return false;
        }
        return md5($weixinModel['open_id'].Yii::$app->params['systemSalt']) == $token;
    }
}
