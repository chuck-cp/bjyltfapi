<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/2
 * Time: 13:26
 * wap 楼宇控制器
 */
namespace wap\controllers;
use wap\core\WapController;
use yii\web\NotFoundHttpException;
class BuildController extends WapController
{
    public function beforeAction($action){
        if(parent::beforeAction($action)){
            if(!$this->authentication()){
                throw new NotFoundHttpException('TOKEN ERROR');
            }
            return true;
        }
        return false;
    }

    /**
     * @return string
     * 楼宇申请
     */
    public function actionIndex(){
        $params =  \Yii::$app->request->get();
        return $this->render($this->action->id,[
            'token' => $params['token'],
        ]);
    }
    /**
     * @return string
     * 公司创建
     */
    public function actionCompanyCreate(){
        $params =  \Yii::$app->request->get();
        return $this->render($this->action->id,[
            'token' => $params['token'],
        ]);
    }

    /**
     * @return string
     * 楼宇创建
     */
    public function actionBuildCreate(){
        $params =  \Yii::$app->request->get();
        return $this->render($this->action->id,[
            'token' => $params['token'],
        ]);
    }

    /**
     * @return string
     * 安装位置场景led
     */
    public function actionSceneLed(){
        $params =  \Yii::$app->request->get();
        return $this->render($this->action->id,[
            'token' => $params['token'],
            'build_id' => $params['build_id'],
            'screen_type' => $params['screen_type'],
            'shop_type' => $params['shop_type'],
        ]);
    }
    /**
     * @return string
     * 安装位置场景led
     */
    public function actionScenePost(){
        $params =  \Yii::$app->request->get();
        return $this->render($this->action->id,[
            'token' => $params['token'],
            'build_id' => $params['build_id'],
            'screen_type' => $params['screen_type'],
            'shop_type' => $params['shop_type'],
        ]);
    }

    /**
     * @return string
     */
    public function actionDetailScene(){
        $params =  \Yii::$app->request->get();
        return $this->render($this->action->id,[
            'token' => $params['token'],
            'id' => $params['id'],//上一页的config_id
            'screen_type' => $params['screen_type'],
            'shop_type' => $params['shop_type'],
            'shop_id' => $params['shop_id'],
        ]);
    }





}