<?php

namespace api\modules\v1\models;

use common\libs\Redis;
use Yii;
use yii\data\Pagination;

class MemberMessage extends \api\core\ApiActiveRecord
{
    public $keyword;
    const NOTICE = 1;

    /*
     * 获取我的消息
     * */
    public function getMemberMessage(){
        $messageModel = self::find()->where(['or',['member_id'=>Yii::$app->user->id],['member_id'=>0]])->andFilterWhere(['like','title',$this->keyword])->select('id,message_type,title,status,date(create_at) as create_at')->orderBy('id desc');
        $pagination = new Pagination(['totalCount'=>$messageModel->count()]);
        $pagination->validatePage = false;
        $messageResult = $messageModel->limit($pagination->limit)->offset($pagination->offset)->asArray()->all();
        if($messageResult){
            foreach($messageResult as $key=>$message){
                if($message['message_type'] == self::NOTICE){
                    $messageResult[$key]['status'] = (string)$this->getNoticeReadStatus($message['id']);
                }
            }
        }
        return $messageResult;
    }


    /*
     * 获取公告的读取状态
     * */
    public function getNoticeReadStatus($id){
        return (int)Redis::getInstance()->SISMEMBER($this->getNoticeKey($id),Yii::$app->user->id);
    }

    /*
     * 获取公告状态的key
     * */
    public function getNoticeKey($id){
        return 'system_notice_'.$id;
    }


    /*
     * 场景
     * */
    public function scenes(){
        return [
            'message'=>[
                'keyword'=>[],
            ]
        ];
    }
}
