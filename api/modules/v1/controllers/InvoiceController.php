<?php

namespace api\modules\v1\controllers;

use api\core\ApiController;
use api\modules\v1\models\Order;
use api\modules\v1\models\MemberInvoice;
use yii\db\Exception;

class InvoiceController extends ApiController
{
    //待开发票的订单列表
    public function actionChooseOrder(){
        $orderModel = new Order();
        //$orderModel->loadParams($this->params,$this->action->id);
        $orderList = $orderModel->getOrderList();
        return $this->returnData('SUCCESS',$orderList);
    }

    //选择的订单开发票
    public function actionCreate(){
        $invoiceModel = new MemberInvoice();
        if($result = $invoiceModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData($invoiceModel->saveInvoice());
    }

    //开票历史
    public function actionInvoiceHistory(){
        $orderModel = new MemberInvoice();
        $orderList = $orderModel->getOrderHistoryList();
        return $this->returnData('SUCCESS',$orderList);
    }

    //获取发票详情
    public function actionDetail($id){
        $orderModel = new MemberInvoice();
        $invoice = $orderModel->getInvoiceDetail($id);
        return $this->returnData('SUCCESS',$invoice);
    }

}
