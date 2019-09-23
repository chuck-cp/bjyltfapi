<?php

namespace wap\controllers;
use wap\core\WapController;
use wap\models\SystemTrain;
use yii\base\Exception;
use yii\web\NotFoundHttpException;

class BusinessTrainningController extends WapController
{
    public function beforeAction($action){
        if(parent::beforeAction($action)){
            if($this->authentication() < 1){
                throw new NotFoundHttpException('TOKEN ERROR');
                return false;
            }
            return true;
        }
        return false;
    }
    //查看图文培训资料详情
    public function actionIndex(){
        $params = \Yii::$app->request->get();
        if(!$params['id']) {return false;}
        return $this->render('index',[
            'title' => (new SystemTrain())->getTrainContent($params['id'],'name'),
            'content' => (new SystemTrain())->getTrainContent($params['id'],'content'),
        ]);
    }

}
