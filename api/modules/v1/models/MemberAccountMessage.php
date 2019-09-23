<?php

namespace api\modules\v1\models;

use common\libs\ToolsClass;
use Yii;

/**
 * 流水动态
 */
class MemberAccountMessage extends \api\core\ApiActiveRecord
{

    public static function tableName()
    {
        return '{{%member_account_message}}';
    }

    public function getMemberMessage(){
        $messageModel = self::find()->where(['member_id'=>Yii::$app->user->id])->select('title,create_at')->limit(5)->orderBy('id desc')->asArray()->all();
        if($messageModel){
            foreach($messageModel as $key=>$message){
                $messageModel[$key]['create_at'] = ToolsClass::timeConvert($message['create_at']);
            }
        }
        return $messageModel;
    }


}
