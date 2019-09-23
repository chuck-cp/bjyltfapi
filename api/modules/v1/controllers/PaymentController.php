<?php
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\LogPayment;
use api\modules\v1\models\Order;
use api\modules\v1\models\SystemConfig;
use common\libs\ToolsClass;
use Yii;
use yii\base\Exception;

/**
 * 付款
 */
class PaymentController extends ApiController
{
    public function actionAlipay(){
        $oderModel = new Order();
        if($result = $oderModel->loadParams($this->params,'payment')){
            return $this->returnData($result);
        }
        list($resultStatus,$resultData) = $oderModel->writePaymentLog(LogPayment::PAY_TYPE_ALIPAY);
        if($resultStatus != 'SUCCESS'){
            return $this->returnData($resultStatus);
        }
        try{
            //判断支付环境
            $environment = substr(Yii::$app->params['baseApiUrl'],0,22) == 'https://tpi.bjyltf.com' ? ',offline' : '';
            //判断支付金额
            $config_pay = SystemConfig::getConfig('config_pay');
            $pay_number = $config_pay == 0 ? 0.01 : ToolsClass::priceConvert($resultData['final_price']);
            $token = \Yii::$app->alipay->sdkExecute([
                'body'=>$resultData['serial_number'].$environment,
                'subject'=>$resultData['advert_name'],
                'out_trade_no'=>$resultData['order_code'],
                'timeout_express'=>'1h',
                'total_amount'=>$pay_number,
                'product_code'=>'QUICK_MSECURITY_PAY',
            ]);
            return $this->returnData('SUCCESS',['token'=>$token]);
        }catch (Exception $e){
            \Yii::error($e->getMessage(),'db');
            return $this->returnData('ERROR');
        }
    }

    public function actionWechat(){
        $oderModel = new Order();
        if($result = $oderModel->loadParams($this->params,'payment')){
            return $this->returnData($result);
        }
        list($resultStatus,$resultData) = $oderModel->writePaymentLog(LogPayment::PAY_TYPE_WECHAT);
        if($resultStatus != 'SUCCESS'){
            return $this->returnData($resultStatus);
        }
        try{
            //判断支付金额
            $config_pay = SystemConfig::getConfig('config_pay');
            $pay_number = $config_pay == 0 ? 1 : $resultData['final_price'];
            if(!$result = \Yii::$app->wxpay->unifiedOrder([
                'attach'=>$resultData['serial_number'],
                'out_trade_no'=>$resultData['order_code'],
                'price'=>$pay_number,
                'body'=>$resultData['advert_name'],
                'tag'=>'',
                'id'=>$resultData['advert_id'],
            ])){
                throw new Exception("支付失败");
            }
            return $this->returnData('SUCCESS',$result);
        }catch (Exception $e){
            \Yii::error($e->getMessage(),'db');
            return $this->returnData('ERROR');
        }
    }

    /*
     * 线下支付
     * */
    public function actionLine(){
        $oderModel = new Order();
        if($result = $oderModel->loadParams($this->params,'payment')){
            return $this->returnData($result);
        }

        list($resultStatus,$orderModel) = $oderModel->writePaymentLog(LogPayment::PAY_TYPE_LINE);
        if($resultStatus == 'SUCCESS'){
            $resultData = SystemConfig::getAllConfigById(['system_receiver_address','system_receiver_bank_name','system_receiver_bank_number','system_receiver_name']);
            $resultData['payment_code'] = $orderModel['payment_code'];
            $resultData['order_price'] = (string)ToolsClass::priceConvert($orderModel['final_price']);
            return $this->returnData($resultStatus,$resultData);
        }
        return $this->returnData($resultStatus);
    }
}
