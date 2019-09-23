<?php
// 用于和外部应用交互的接口类
namespace api\controllers;
use api\core\ApiController;
use api\models\Screen;
use common\libs\RedisClass;
use common\libs\ToolsClass;
use yii\web\NotFoundHttpException;


class InterfaceController extends ApiController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        return $behaviors;
    }

    public function actionValidate()
    {
        $echoStr = $_GET["echostr"];
        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];
        $tmpArr = array('wwvpaldo123', $timestamp, $nonce);
        sort($tmpArr);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            echo $echoStr;
        }
    }

    // 根据设备编号获得店铺ID和店铺名称
    public function actionGetShopInfo($software_number){
        $token = \Yii::$app->request->get('token');
        if ($token != md5("httpwww1818laocom{$software_number}httpbjyltfcom")) {
            throw new NotFoundHttpException();
        }
        $shopModel = Screen::find()->joinWith('shop',false)->select('shop_id,yl_shop.name as shop_name')->where(['software_number'=>$software_number])->asArray()->limit(1)->one();
        return $this->returnData('SUCCESS',$shopModel);
    }
}
