<?php
namespace pc\controllers;
use common\libs\ToolsClass;
use pc\models\LoginForm;
use pc\models\SystemConfig;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\captcha\Captcha;

/**
 * Site controller
 */
class SiteController extends Controller
{
    public function actionIndex()
    {
        //检查是否是移动端访问
        if(ToolsClass::isMobile()){
            Yii::$app->response->redirect("https://wap.bjyltf.com");
        }
        $configModel= new SystemConfig();
        $result_phone = $configModel->getConfigById("service_phone");
        $result_email = $configModel->getConfigById("e_mail");
        $this->layout = false;
        return $this->render('index',[
        'service_email' => $result_email['content'],
            'service_phone' => $result_phone['content'],
        ]);
    }

    public function actionView()
    {
        $configModel= new SystemConfig();
        $result_phone = $configModel->getConfigById("service_phone");
        $result_email = $configModel->getConfigById("e_mail");
        $this->layout = false;
        return $this->render('view',[
            'service_email' => $result_email['content'],
            'service_phone' => $result_phone['content'],
        ]);
    }

    public function actionPolicy()
    {
        $configModel= new SystemConfig();
        $result_phone = $configModel->getConfigById("service_phone");
        $result_email = $configModel->getConfigById("e_mail");

        $this->layout = false;
        return $this->render('policy',[
            'service_email' => $result_email['content'],
            'service_phone' => $result_phone['content'],
        ]);
    }

    public function actionCondition(){

        $configModel= new SystemConfig();
        $result_phone = $configModel->getConfigById("service_phone");
        $result_email = $configModel->getConfigById("e_mail");

        $this->layout = false;
        return $this->render('condition',[
            'service_email' => $result_email['content'],
            'service_phone' => $result_phone['content'],
        ]);
    }

    //隐私声明
    public function actionPrivacy(){
        $configModel= new SystemConfig();
        $this->layout = false;
        return $this->render('privacy');
    }
}
