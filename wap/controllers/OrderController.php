<?php

namespace wap\controllers;
use api\modules\v1\models\OrderAreaCache;
use wap\models\SystemAddress;
use wap\models\Order;
use wap\models\OrderArea;
use wap\core\WapController;
use common\libs\ToolsClass;
use wap\models\MemberEquipment;
use wap\models\OrderDate;
use wap\models\OrderPlayPresentation;
use wap\models\OrderPlayView;
use wap\models\OrderPlayViewArea;
use yii\web\NotFoundHttpException;

class OrderController extends WapController
{
    public $exceptAction = ['agreement'];
    public function beforeAction($action){
        if(parent::beforeAction($action)){
            if (in_array($this->action->id,$this->exceptAction)) {
                return true;
            }
            if($this->authentication() < 1){
                print_r('TOKEN ERROR');
                return false;
            }
            return true;
        }

        return false;

    }

    /*
     * 订单协议
     * */
    public function actionAgreement()
    {
        $token = \Yii::$app->request->get('token');
        $member = MemberEquipment::find()->where(['token'=>$token])->select('member_id')->asArray()->one();
        if(empty($member) || empty($member['member_id'])){
            throw new NotFoundHttpException();
        }
        if ($order_id = (int)\Yii::$app->request->get('order_id')) {
            $orderModel = Order::find()->select('payment_type,order_code,advert_name,rate,advert_time,final_price')->where(['and',['id' => $order_id],['or',['salesman_id' => $member['member_id']],['member_id' => $member['member_id']]]])->asArray()->one();
            if (empty($orderModel)) {
                throw new NotFoundHttpException();
            }
            $orderDate = OrderDate::find()->select('start_at,end_at')->where(['order_id' => $order_id])->asArray()->one();
            $orderArea = OrderArea::find()->select('street_area')->where(['order_id' => $order_id])->asArray()->one();
            $areaData = [];
            if (!empty($orderArea) && !empty($orderArea['street_area'])) {
                $street_area = explode(",",$orderArea['street_area']);
                foreach ($street_area as $value) {
                    $reformArea[substr($value,0,7)] = 1;
                }
                foreach (array_keys($reformArea) as $value) {
                    $areaData[] = SystemAddress::getAreaNameById($value,'one');
                }
            }
            $orderModel['area'] = implode(",",$areaData);
            $orderModel['date'] = $orderDate['start_at'] . '至' . $orderDate['end_at'];
        } else {
            $orderArea = OrderAreaCache::find()->select('area_id')->where(['token' => $token])->asArray()->one();
            $areaData = [];
            if (!empty($orderArea) && !empty($orderArea['area_id'])) {
                $street_area = explode(",",$orderArea['area_id']);
                foreach ($street_area as $value) {
                    $reformArea[substr($value,0,7)] = 1;
                }
                foreach (array_keys($reformArea) as $value) {
                    $areaData[] = SystemAddress::getAreaNameById($value,'one');
                }
            }
            $orderModel = [
                'area' => implode(",",$areaData),
                'date' => \Yii::$app->request->get('start_at') . '至' . \Yii::$app->request->get('end_at'),
                'payment_type' => \Yii::$app->request->get('payment_type'),
                'order_code' => '　',
                'advert_name' => \Yii::$app->request->get('advert_name'),
                'rate' => (int)\Yii::$app->request->get('rate'),
                'advert_time' => \Yii::$app->request->get('advert_time'),
                'final_price' => (int)\Yii::$app->request->get('final_price')
            ];
        }

        return $this->render('agreement',[
           'order' => $orderModel,
           'order_id' => $order_id,
        ]);
    }

    /*
     * 下单时地区余量查询
     */
    public function actionIndex(){
        $params = \Yii::$app->request->get();
        //用户区分是购买处余量查询还是修改时间处的余量查询
        if(isset($params['request_types']) && $params['request_types'] == 'modify' && isset($params['order_id'])){
            if(!$params['order_id']){
                return false;
            }
            $orderInfo = Order::find()->where(['id'=>$params['order_id']])->select('advert_id,advert_time,rate')->asArray()->one();
            $orderArea = OrderArea::find()->where(['order_id'=>$params['order_id']])->select('area_id')->asArray()->one();
            if(empty($orderInfo) || empty($orderArea)){
                return false;
            }
            $params['advert_id'] = $orderInfo['advert_id'];
            $params['advert_time'] = $orderInfo['advert_time'];
            $params['rate'] = $orderInfo['rate'];
            $params['area_id'] = $orderArea['area_id'];
        }
        if(!isset($params['start_at']) || !isset($params['end_at']) || !isset($params['request_types']) || !isset($params['token'])){
            return false;
        }
        $member_id = MemberEquipment::find()->where(['token'=>$params['token']])->select('member_id')->asArray()->one();
        if(!isset($member_id['member_id'])){
            return false;
        }
        $params['page'] = 1;
        return $this->render('index', [
            'params' => json_encode($params),
            'total_days' => ToolsClass::timediff(($params['start_at']), ($params['end_at'])),
            'member_id' => $member_id['member_id'],
        ]);
    }
    /*
     * 订单播放详情
     */
    public function actionPlayDetail(){
        $params = \Yii::$app->request->get();
        $order = Order::find()->where(['id' => $params['order_id']])->select('id, salesman_name, custom_service_name, order_code, advert_name, rate, advert_time')->asArray()->one();
        $order_date = OrderDate::find()->where(['order_id'=>$params['order_id']])->select('start_at, end_at')->asArray()->one();
        $order_area = OrderArea::find()->where(['order_id'=>$params['order_id']])->select('order_id, area_type')->asArray()->one();
    }
    /*
     * 播放地区和播放概况
     */
    public function actionPlayAreaSituation(){
        $params = \Yii::$app->request->get();
        $play_info = OrderPlayView::find()->where(['order_id'=>$params['id']])->select('throw_province_number, throw_city_number, throw_area_number, throw_street_number, total_play_number, total_play_time, total_play_rate, total_watch_number')->asArray()->one();
    }
    /*
     * 覆盖理发店信息
     */
    public function actionCoverBarber(){
        $params = \Yii::$app->request->get();
        $play_info = OrderPlayView::find()->where(['order_id'=>$params['id']])->select('throw_shop_number, throw_screen_number, large_shop_rate, medium_shop_rate, small_shop_rate')->asArray()->one();
    }
    /*
     * 地区播放情况
     */
    public function actionAreaPlay(){
        $params = \Yii::$app->request->get();
        $area_info = OrderPlayViewArea::find()->where(['order_id'=>$params['id']])->asArray()->all();
    }

    /*
     * 日播放情况
     */
    public function actionPlayByDay(){
        $params = \Yii::$app->request->get();
        $day_info = OrderPlayPresentation::find()->where(['order_id'=>$params['id']])->asArray()->one();
    }
    /*
     * 新增屏幕数
     */
    public function actionAboutScreen(){
        $params = \Yii::$app->request->get();
        $play_info = OrderPlayView::find()->where(['order_id'=>$params['id']])->select('shop_number,screen_number,new_play_number,new_play_rate')->asArray()->one();
    }

    public function actionBuyContract(){
        $params = \Yii::$app->request->get();
        return $this->render('buy-contract',[

        ]);
    }

}

















