<?php

namespace wap\models;

use common\libs\Redis;
use Yii;
use yii\data\Pagination;

class MemberMessage extends \yii\db\ActiveRecord
{
    const NOTICE = 1;
    public static function tableName()
    {
        return '{{%member_message}}';
    }

    /*
     * 设置公告的读取状态
     * */
    public function setNoticeStatus($id,$member_id){
        return Redis::getInstance()->sadd('system_notice_'.$id,$member_id);
    }

    /*
     * 获取消息详情
     * */
    public function getMessage($id,$type){
        if($type == 'notice'){
            $where = ['notice_id'=>$id];
        }else{
            $where = ['id'=>$id];
        }
        $messageModel = self::find()->where($where)->select('id,title,content,create_at,message_type')->asArray()->one();
        $token = Yii::$app->request->get('token');
        if($messageModel && $token){
            if($memberModel = MemberEquipment::find()->where(['token'=>$token])->select('member_id')->asArray()->one()){
                if($messageModel['message_type'] == self::NOTICE){
                    $this->setNoticeStatus($messageModel['id'],$memberModel['member_id']);
                }else{
                    self::updateAll(['status'=>1],['id'=>$id,'member_id'=>$memberModel['member_id']]);
                }
            }
        }
        return $messageModel;
    }
}
