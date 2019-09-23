<?php
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\LogPayment;
use api\modules\v1\models\Order;
use common\libs\ToolsClass;
use yii\base\Exception;


/**
 * 回调
 */
class CallbackController extends ApiController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        return $behaviors;
    }

    /*
     * 支付宝测试环境回调
     * */
    public function actionAlipayTest(){
        $resultNotify = \Yii::$app->alipay->notify();
        $result = false;
        if($resultNotify) {
            $notifyData = \Yii::$app->request->post();
            $notifyData['body'] = explode(",",$notifyData['body'])[0];
            $result = (new Order())->paymentCallBack($notifyData['body'],$notifyData['total_amount'],$notifyData['gmt_payment'],$notifyData['seller_email'],$notifyData['trade_no']);
        }
        if($result){
            echo 'SUCCESS';
        }else{
            echo 'ERROR';
        }
    }

    /*
     * 支付宝回调
     * */
    public function actionAlipay(){
        $resultNotify = \Yii::$app->alipay->notify();
        $result = false;
        if($resultNotify){
            $notifyData = \Yii::$app->request->post();
            if(!strstr($notifyData['body'], ',')){
                $result = (new Order())->paymentCallBack($notifyData['body'],$notifyData['total_amount'],$notifyData['gmt_payment'],$notifyData['seller_email'],$notifyData['trade_no']);
            }else{
                $result = ToolsClass::curl('http://tpi.bjyltf.com/v1/callback/alipay-test',$notifyData,'POST');
            }
        }
        if($result){
            echo 'SUCCESS';
        }else{
            echo 'ERROR';
        }
    }

    /*
     * 微信支付回调
     * */
    public function actionWechat(){
        try{
            #$xml = file_get_contents('php://input');
            $result = false;
            if($notifyData = \Yii::$app->wxpay->notify()){
                $result = (new Order())->paymentCallBack($notifyData['attach'],ToolsClass::priceConvert($notifyData['total_fee']),date('Y-m-d H:i:s'),$notifyData['appid'],$notifyData['transaction_id']);
            }
            if($result){
                echo '<xml><return_code><![CDATA[SUCCESS]]></return_code></xml>';
            }else{
                echo '<xml><return_code><![CDATA[FAIL]]></return_code></xml>';
            }
        }catch (Exception $e){
            \Yii::error($e->getMessage(),'payment');
        }
    }
}
