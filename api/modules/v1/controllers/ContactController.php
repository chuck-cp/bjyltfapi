<?php

namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\Order;
class ContactController extends ApiController
{
    //获取待申请合同订单列表
    public function actionIndex(){
        $orderList = (new Order())->getOrderList('contact');
        return $this->returnData('SUCCESS',$orderList);
    }
    //获取合同历史
    public function actionContactHistory(){
        $orderList = (new Order())->getOrderList('history');
        return $this->returnData('SUCCESS',$orderList);
    }
    //生成合同
    public function actionContact(){
        $orderModel = new Order();
        if($result = $orderModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if(!$orderModel->saveContact()){
            return $this->returnData('ERROR');
        }
        return $this->returnData('SUCCESS');
    }

}
