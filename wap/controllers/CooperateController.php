<?php
namespace wap\controllers;
use common\libs\ToolsClass;
use wap\core\WapController;
use wap\models\SystemConfig;
use yii\web\NotFoundHttpException;


/**
 * 合作方式
 */
class CooperateController extends WapController
{

    /*
     * 合作政策
     * */
    public function actionPolicy(){
        throw new NotFoundHttpException();
        $configModel= new SystemConfig();
        $result_phone = $configModel->getConfigById("service_phone");
        $result_email = $configModel->getConfigById("e_mail");

        return $this->render('policy',[
            'service_email' => $result_email['content'],
            'service_phone' => $result_phone['content'],
        ]);
    }

    /*
     * 合作详情
     * */
    public function actionView(){
        throw new NotFoundHttpException();
        $action = \Yii::$app->request->get('action');
        if(empty($action)){
            $action = 1;
        }
        $configModel= new SystemConfig();
        $result_phone = $configModel->getConfigById("service_phone");
        $result_email = $configModel->getConfigById("e_mail");
        return $this->render('view',[
            'action'=>$action,
            'service_email' => $result_email['content'],
            'service_phone' => $result_phone['content'],
        ]);
    }

    /*
     * 购买流程
     * */
    public function actionProcedure(){
        throw new NotFoundHttpException();
        $configModel= new SystemConfig();
        $result_phone = $configModel->getConfigById("service_phone");
        $result_email = $configModel->getConfigById("e_mail");
        return $this->render('procedure',[
            'service_email' => $result_email['content'],
            'service_phone' => $result_phone['content'],
        ]);
    }

    /*
     * 常见问题
     * */
    public function actionQuestion(){
        throw new NotFoundHttpException();
        $configModel= new SystemConfig();
        $config = $configModel->getAllConfig(['service_phone','e_mail','system_order_price']);
        $config['system_order_price'] = ToolsClass::priceConvert($config['system_order_price']);
        return $this->render('question',[
            'config' => $config,
        ]);
    }
}
