<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/7
 * Time: 13:39
 */

namespace wap\controllers;


use wap\core\WapController;
use wap\models\OrderPlayView;
use yii\web\NotFoundHttpException;
class PlayReportController extends WapController{
    public function beforeAction($action){
        if(parent::beforeAction($action)){
            if(!$this->authentication()){
                throw new NotFoundHttpException('TOKEN ERROR');
            }
            return true;
        }
        return false;
    }
    //新版监播报告
    public function actionIndex(){
        $token = \Yii::$app->request->get('token') ?? '';
        $order_id = \Yii::$app->request->get('order_id') ?? '';
        $incrScreen = OrderPlayView::find()->where(['order_id'=>$order_id])->select('give_shop_number')->asArray()->one();
        return $this->render('index',[
            'token' => $token,
            'order_id' => $order_id,
            'incrNum' => isset($incrScreen['give_shop_number']) ? $incrScreen['give_shop_number'] : 0,
        ]);
    }
}