<?php
namespace wap\controllers;
use api\modules\v1\models\MemberMessage;
use api\modules\v1\models\SystemVersion;
use common\libs\ToolsClass;
use wap\core\WapController;
use api\modules\v1\models\SystemConfig;
use yii\web\NotFoundHttpException;

/**
 * 用户协议
 */
class AgreementController extends WapController
{

    public function actionView($id){
        $this->layout = false;
        $configModel = new SystemConfig();
        $resultConfig = $configModel->getConfigById($id);
        $title = ToolsClass::getCommonStatus('agreementTitle',$id);
        if(empty($resultConfig) || empty($title)){
            throw new NotFoundHttpException;
        }
        return $this->render('index',[
            'title'=>$title,
            'content'=>$resultConfig['content'],
        ]);
    }
    public function actionAppview($id){
        $this->layout = false;
        $configModel = new SystemConfig();
        $resultConfig = $configModel->getConfigById($id);
        $title = ToolsClass::getCommonStatus('agreementTitle',$id);
        if(empty($resultConfig) || empty($title)){
            throw new NotFoundHttpException;
        }
        return $this->render('agreement',[
            'title'=>$title,
            'content'=>$resultConfig['content'],
        ]);
    }
}
