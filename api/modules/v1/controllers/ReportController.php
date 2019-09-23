<?php

namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\OrderPlayView;
use api\modules\v1\models\OrderPlayViewArea;
use api\modules\v1\models\OrderPlayViewDate;

class ReportController extends ApiController{
    public function beforeAction($action){
        parent::beforeAction($action);
        $order_id = \Yii::$app->request->get('order_id');
        $re = OrderPlayView::getOrderEndAt($order_id);
        if(!$re){
            return false;
        }
        return true;
    }
    //监播报告新改版
    public function actionAll(){
        $order_id = \Yii::$app->request->get('order_id');
        //基本信息
        $base = OrderPlayView::getFields($order_id,'order_code,salesman_name,custom_service_name,start_at,end_at,advert_name,advert_time,advert_rate,throw_area,total_order_play_number,total_play_number,total_play_time,total_arrival_rate,total_play_rate,total_watch_number,total_people_watch_number,total_no_repeat_watch_number,people_watch_number,total_radiation_number,throw_shop_number,throw_screen_number,throw_mirror_number,screen_run_time,throw_city_number,throw_area_number,throw_street_number,give_shop_number,give_screen_number,give_play_number,give_watch_number,give_radiation_number');
        $rand = OrderPlayViewDate::getRank($order_id);
        $area = OrderPlayViewArea::getArea($order_id);
        return $this->returnData('SUCCESS',compact('base','rand','area'));
    }
}
