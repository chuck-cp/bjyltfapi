<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/7/2
 * Time: 13:27
 * wap 公园控制器
 */

namespace wap\controllers;
use wap\core\WapController;
use yii\web\NotFoundHttpException;
class ParkController extends WapController
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
     * 公园申请
     */
    public function actionIndex(){
        $params =  \Yii::$app->request->get();
        return $this->render($this->action->id,[
            'token' => $params['token'],
        ]);
    }
    /**
     * @return string
     * 公园创建
     */
    public function actionParkCreate(){
        $params =  \Yii::$app->request->get();
        return $this->render($this->action->id,[
            'token' => $params['token'],
        ]);
    }


    /**
     * @return string
     * 安装位置场景poster
     */
    public function actionScenePost(){
        $params =  \Yii::$app->request->get();
        return $this->render($this->action->id,[
            'token' => $params['token'],
            'id' => $params['id'],//上一页的config_id
            'shop_id' => $params['park_id'],
            'screen_type' => $params['screen_type'],
            'shop_type' => $params['shop_type'],
        ]);
    }


}