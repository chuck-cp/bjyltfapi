<?php
// 用于和外部应用交互的回调处理类
namespace api\controllers;
use api\core\ApiController;
use api\modules\v1\models\LogPayment;
use api\modules\v1\models\Order;
use common\libs\Redis;
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
                $result = ToolsClass::curl('https://tpi.bjyltf.com/callback/alipay-test',$notifyData,'POST');
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


    /*
    * 视频转换回调
    * */
    public function actionVideoConvertBack(){
        try{
            $fieldJson = file_get_contents('php://input');
            // $fildjson='{"version":"4.0","eventType":"TranscodeComplete","data":{"vodTaskId":"1252719796-transcode-47e70c46c4c58ebc822953f926bbc7c1","status":0,"message":"","fileId":"5285890781020084748","fileName":"测试5s","duration":5,"coverUrl":"http://1252719796.vod2.myqcloud.com/9bffb8b8vodtransgzp1252719796/1841ae8b5285890781020084748/snapshot/1533865659_3147104656.100_0.jpg","playSet":[{"url":"http://1252719796.vod2.myqcloud.com/e7d81610vodgzp1252719796/1841ae8b5285890781020084748/zyY6yWGvVdEA.mp4","definition":0,"vbitrate":16973605,"vheight":810,"vwidth":1560},{"url":"http://1252719796.vod2.myqcloud.com/9bffb8b8vodtransgzp1252719796/1841ae8b5285890781020084748/v.f19630.mp4","definition":19630,"vbitrate":696816,"vheight":810,"vwidth":1560}]}}';
            \Yii::info("[视频回调]".date('Y-m-d H:i:s')."===".$fieldJson);
            $result = ToolsClass::curl("https://api.bjyltf.com/callback/video-convert",$fieldJson);
            $result = json_decode($result,true);
            if ($result['status'] == 200) {
                \Yii::info("[视频回调]正式环境回调成功");
            } else {
                \Yii::info("[视频回调]正式环境回调失败");
            }
            $result = ToolsClass::curl("https://tpi.bjyltf.com/callback/video-convert",$fieldJson);
            $result = json_decode($result,true);
            if ($result['status'] == 200) {
                \Yii::info("[视频回调]测试环境回调成功");
            } else {
                \Yii::info("[视频回调]测试环境回调失败");
            }

            $result = ToolsClass::curl("https://hapi.bjyltf.com/callback/video-convert",$fieldJson);
            $result = json_decode($result,true);
            if ($result['status'] == 200) {
                \Yii::info("[视频回调]测试环境回调成功");
            } else {
                \Yii::info("[视频回调]测试环境回调失败");
            }
        }catch (\Exception $e) {
            \Yii::error($e->getMessage().' '.$e->getLine());
        }

    }

    // 视频转换回调测试环境
    public function actionVideoConvert(){
        $fileJson = file_get_contents('php://input');
        $fileData = json_decode($fileJson)->data;
        $fileId = $fileData->fileId;
        foreach($fileData->playSet as $k=>$v){
            if($v->definition=='19630'){
                $url=$v->url;
            }
        }
        if(Redis::getInstance(2)->set("transcoding:".$fileId,$url,3600)){
            \Yii::info("[视频回调写入redischenggong]".date('Y-m-d H:i:s')."===".$fileJson);
            return $this->returnData();
        }
        return $this->returnData('ERROR');
    }

}
