<?php
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\AdvertPosition;
use api\modules\v1\models\AdvertPrice;
use api\modules\v1\models\AdvertSpace;
use api\modules\v1\models\LogExamine;
use api\modules\v1\models\Member;
use api\modules\v1\models\MemberAreaCount;
use api\modules\v1\models\MemberFunction;
use api\modules\v1\models\OrderArea;
use api\modules\v1\models\OrderAreaCache;
use api\modules\v1\models\OrderCannel;
use api\modules\v1\models\OrderCount;
use api\modules\v1\models\OrderDate;
use api\modules\v1\models\OrderThrowProgram;
use api\modules\v1\models\Screen;
use api\modules\v1\models\Order;
use api\modules\v1\models\OrderComplain;
use api\modules\v1\models\ShopApply;
use api\modules\v1\models\ShopImage;
use api\modules\v1\models\SystemAddress;
use api\modules\v1\models\SystemBanner;
use api\modules\v1\models\SystemNotice;
use common\libs\QueueClass;
use common\libs\RedisClass;
use common\libs\ToolsClass;
use api\modules\v1\models\SystemConfig;
use api\modules\v1\models\OrderThrowProgramSpace;
use Redis;
use Yii;
use yii\db\Exception;

/**
 * 订单
 */
class OrderController extends ApiController
{

    /**
     * 获取我的订单列表(type 1 )
     */
    public function actionIndex(){
        $orderModel = new Order();
        $res=$orderModel->loadParams($this->params,$this->action->id);
        if($res){
            return $this->returnData($res);
        }
        $result['order_list'] = $orderModel->getMemberOrder();
        if($result){
            return $this->returnData('SUCCESS',$result);
        }
        return $this->returnData('ERROR');
    }
    /*
     * 支付完成后排期情况页面
     */
    public function actionPrevView(){
        set_time_limit(0);
        $orderModel = new Order();
        $res=$orderModel->loadParams($this->params,$this->action->id);
        if($res){
            return $this->returnData($res);
        }
        while(true){
            $orderStatus = $orderModel->getAppointFields('schedule_status');
            if($orderStatus['schedule_status'] == 1){
                break;
            }
            sleep(1);
        }
        $orderInfo = $orderModel->getAppointFields('deal_price,order_price');
        $orderInfo['deal_price'] = ToolsClass::priceConvert($orderInfo['deal_price']);
        $orderInfo['order_price'] = ToolsClass::priceConvert($orderInfo['order_price']);
        $orderInfo['service_phone'] = SystemConfig::getConfig('service_phone');
        if($orderInfo){
            return $this->returnData('SUCCESS',$orderInfo);
        }
        return $this->returnData('ERROR');
    }
    /*
     * 获取订单详情
     * */
    public function actionView($order_id){
        $orderModel = new Order();
        $res=$orderModel->loadParams($this->params,$this->action->id);
        if($res){
            return $this->returnData($res);
        }
        $result['order'] = $orderModel->getOneOrder($order_id);
        if($result){
            return $this->returnData('SUCCESS',$result);
        }
        return $this->returnData('ERROR');
    }
    /*
     * 汇款信息
     * */
    public function actionRemittance($member_id,$order_id){
        $orderModel = new Order();
        $result = $orderModel->getRemittance($order_id);
        return $this->returnData('SUCCESS',$result);
    }
    /*
     * 放弃订单
     * */
    public function actionOrdercannel(){
        $orderModel=new OrderCannel();
        if($result = $orderModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if($orderModel->OrderCannel()){
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }

    /*
     * 修改广告投放时间
     * */
    public function actionOrderdate($member_id,$order_id){
        set_time_limit(0);
        $orderModel = new OrderDate();
        $res=$orderModel->loadParams($this->params,$this->action->id);
        if($res)$this->returnData($res);
        list($result_status,$number)=$orderModel->checkDateTime($order_id);
        if($result_status != 'SUCCESS'){
            return $this->returnData($result_status);
        }
        while (true){
            $result = RedisClass::get('result_update_order:'.$order_id,4);
            if($result == 1 && $orderModel->updateOrderDate($order_id,$number)){
                RedisClass::del('result_update_order:'.$order_id,4);
                #计算出剩余的可修改次数
                return $this->returnData($result_status,array('number'=>(string)3-$number));
            }elseif($result == 2){
                RedisClass::del('result_update_order:'.$order_id,4);
                return $this->returnData('UPDATE_NO_BUY_SPACE_TIME');
            }
            sleep(1);
        }
    }
    /*
     * 修改订单确认之前的余量查询
     */
    public function actionOrderModifyView($member_id,$order_id){
        $orderModel = new OrderArea();
        if($result = $orderModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        list($status_code,$result_data) = $orderModel->getModifyView($order_id);
        return $this->returnData($status_code,$result_data);
    }
    /*
     * 广告业务-投诉广告对接人
     * */
    public function actionFcomplaint(){
        $orderModel=new OrderComplain();
        $res=$orderModel->loadParams($this->params,$this->action->id);
        if($res)$this->returnData($res);
        $result=$orderModel->Fcomplaint();
        if($result){
            switch($result){
                case 1:
                    return $this->returnData('FCOMPLAINT_ERROE_NOE');
                    break;
                case 2:
                    return $this->returnData('FCOMPLAINT_ERROE_TWO');
                    break;
            }
            return $this->returnData('ERROR');
        }else{
            return $this->returnData('SUCCESS');
        }
    }
    /*
  * 广告业务-投诉广告对接人
  * */
    public function actionFcomplaint2(){
        $orderModel=new OrderComplain();
        $res=$orderModel->loadParams($this->params,$this->action->id);
        if($res)$this->returnData($res);
        $result=$orderModel->Fcomplaint2(1);
        if($result){
            switch($result){
                case 1:
                    return $this->returnData('FCOMPLAINT_ERROE_NOE');
                    break;
                case 2:
                    return $this->returnData('FCOMPLAINT_ERROE_TWO');
                    break;
            }
            return $this->returnData('ERROR');
        }else{
            return $this->returnData('SUCCESS');
        }
    }
    /*
    * 广告业务-投诉业务合作人
    * */
    public function actionFcomplaint3(){
        $orderModel=new OrderComplain();
        $res=$orderModel->loadParams($this->params,$this->action->id);
        if($res)$this->returnData($res);
        $result=$orderModel->Fcomplaint2(2);
        if($result){
            switch($result){
                case 1:
                    return $this->returnData('FCOMPLAINT_ERROE_NOE');
                    break;
                case 2:
                    return $this->returnData('FCOMPLAINT_ERROE_TWO');
                    break;
            }
            return $this->returnData('ERROR');
        }else{
            return $this->returnData('SUCCESS');
        }
    }

    /*
     * 获取订单投诉人信息
     * */
    public function actionOrdercomplain(){
        $orderModel = new Order();
        if($result = $orderModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $orderlist=$orderModel->ordercomplain();
        if($orderlist){
            return $this->returnData('SUCCESS',$orderlist);
        }else{
            return $this->returnData('ERROR');
        }
    }

    /*
     * 投诉申请
     * */
    public function actionOrdercomplain2(){
        $orderModel = new OrderComplain();
        $orderModel->loadParams($this->params,$this->action->id);
        $res=$orderModel->ordercomplain2();
        if($res){
            return $this->returnData('SUCCESS');
        }else{
            return $this->returnData('ERROR');
        }
    }

    /*
     * 查看已购买的地区
     * */
    public function actionSelectArea($member_id,$order_id){

        $orderModel = new Order();
        $orderModel->loadParams($this->params,$this->action->id);
        $orderArea = $orderModel->getOrderArea($order_id);
        return $this->returnData('SUCCESS',$orderArea);
    }

    /*
     * 已购买地区详情
     * */
    public function actionConfirmAreaView(){
        $areaModel = new OrderAreaCache();
        if($result = $areaModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$areaModel->getConfirmAreaView());
    }

    /*
     * 确认已购买地区
     * */
    public function actionConfirmArea(){
        $orderModel = new OrderAreaCache();
        if($result = $orderModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        list($status_code,$result_data) = $orderModel->getConfirmArea();
        return $this->returnData($status_code,$result_data);
    }

    /*
     * 获取地图数据
     * */
    public function actionMap(){
        $orderModel = new OrderAreaCache();
        if($result = $orderModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $result = $orderModel->getMapData();
        return $this->returnData('SUCCESS',$result);
    }


    /*
     * 创建订单地区缓存
     * */
    public function actionCreateArea(){
        $areaModel = new OrderAreaCache();
        if($result = $areaModel->loadParams($this->params,'create')){
            return $this->returnData($result);
        }
        if($result = $areaModel->createAreaCache()){
            return $this->returnData('SUCCESS',$result);
        }
        return $this->returnData('ERROR');
    }

    /*
     * 创建订单
     * */
    public function actionCreate(){
        $orderModel = new Order();
        if($result = $orderModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $orderDateModel = new OrderDate();
        if($result = $orderDateModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $dbTrans = Yii::$app->db->beginTransaction();
        try{
            if($result = $orderModel->createOrder()){
                throw new \yii\base\Exception($result);
            }
            if($result = $orderDateModel->createOrderDate($orderModel->id,$orderModel->advert_id)){
                throw new \yii\base\Exception($result);
            }
            $orderAreaModel = new OrderArea();
            if($result = $orderAreaModel->createOrderArea($orderModel->id, $orderModel->advert_id, $orderModel->number,$orderModel->advert_time, $orderDateModel->start_at, $orderDateModel->end_at)){
                throw new \yii\base\Exception($result);
            }
            foreach (explode(",",$orderAreaModel['street_area']) as $street_id) {
                $streetData[] = substr($street_id,0,7);
            }
            $addressModel = SystemAddress::find()->where(['id' => $streetData])->select('name')->asArray()->all();
            \common\libs\Redis::getInstance(4)->rpush('system_member_agreement_list',json_encode([
                'type' => 'order',
                'agreement_name' => $orderModel['order_code'].str_replace(["-"," ",":"],"",$orderModel['create_at']).$orderModel['member_id'].'.pdf',
                'order_code' => $orderModel['order_code'],
                'date' => $orderDateModel['start_at'].'至'.$orderDateModel['end_at'],
                'advert_name' => $orderModel['advert_name'],
                'advert_time' => $orderModel['advert_time'],
                'rate' => $orderModel['rate'],
                'final_price' => ToolsClass::priceConvert($orderModel['final_price']),
                'payment_type' => $orderModel['payment_type'],
                'area' => implode(",",array_column($addressModel,'name'))
            ]));
            $dbTrans->commit();
            return $this->returnData('SUCCESS',['order_code'=>$orderModel->order_code,'order_id'=>$orderModel->id]);
        }catch (\yii\base\Exception $e){
            $dbTrans->rollBack();
            return $this->returnData($e->getMessage());
        }
    }
    /*
     * 店铺同意广告购买协议
     */
    public function actionAgreeBuyContract(){
        $orderModel = new Order();
        if($result = $orderModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $res = $orderModel->updateBuyAgree();
        if($res !== 'SUCCESS'){
            return $this->returnData($res);
        }
        return $this->returnData('SUCCESS');
    }
}
