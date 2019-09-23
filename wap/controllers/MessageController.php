<?php
namespace wap\controllers;
use wap\core\WapController;
use wap\core\WapQueryParamAuth;
use wap\models\MemberMessage;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * 我的消息
 */
class MessageController extends WapController
{

    /**
     * 获取系统消息、公告详情
     */
    public function actionView($id){
        $type = \Yii::$app->request->get('type');
        $messageModel = new MemberMessage();
        $messageModel = $messageModel->getMessage($id,$type);
        if(empty($messageModel)){
            throw new NotFoundHttpException;
        }
        return $this->render('index',[
            'date'=>$messageModel['create_at'],
            'title'=>$messageModel['message_type'] == 1 ? '公告' : '系统消息',
            'bt' => $messageModel['title'],
            'content'=>$messageModel['content'],
        ]);
    }
}
