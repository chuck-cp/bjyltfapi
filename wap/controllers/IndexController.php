<?php
namespace wap\controllers;
use wap\core\WapController;
use wap\models\SystemConfig;


/**
 * 首页
 */
class IndexController extends WapController
{

    public function actionError(){
        $this->layout = false;
        return $this->render('404');
    }
    public function actionIndex(){
        $configModel= new SystemConfig();
        $result_phone = $configModel->getConfigById("service_phone");
        $result_email = $configModel->getConfigById("e_mail");
        $this->layout = false;
        return $this->render('index',[
            'service_email' => $result_email['content'],
            'service_phone' => $result_phone['content'],
        ]);
    }

    public function actionMap($action,$area_id){
        /*if(parent::beforeAction($action)){
            if($this->authentication() < 1){
                print_r('TOKEN ERROR');
                return false;
            }
            return true;
        }*/
        /*switch (strlen($area_id)){
            case '3':

                break;
            case '5':
                break;
            case '7':
                break;
            case '9':
                break;
            case '12':
                break;
        }*/
        return $this->render('map');
    }
}
