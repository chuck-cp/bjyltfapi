<?php
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\Feedback;
use api\modules\v1\models\LogAccount;
use api\modules\v1\models\LogSystem;
use api\modules\v1\models\Member;
use api\modules\v1\models\MemberWeixin;
use api\modules\v1\models\Order;
use api\modules\v1\models\OrderThrowCount;
use api\modules\v1\models\OrderThrowRecord;
use api\modules\v1\models\Shop;
use api\modules\v1\models\ShopApply;
use api\modules\v1\models\SystemAcount;
use api\modules\v1\models\SystemAddress;
use api\modules\v1\models\SystemAddressLevel;
use api\modules\v1\models\SystemConfig;
use api\modules\v1\models\SystemPublicKey;
use api\modules\v1\models\SystemVerify;
use api\modules\v1\models\SystemVersion;
use api\modules\v1\models\SystemZonePrice;
use api\modules\v1\models\SystemStartup;
use api\modules\v1\models\User;
use common\libs\PublicClass;
use common\libs\Redis;
use common\libs\RedisClass;
use common\libs\ToolsClass;
use yii\base\Exception;
use yii\mongodb\Query;
use yii\web\NotFoundHttpException;

/**
 * 系统配置
 */
class SystemController extends ApiController
{

    public function behaviors()
    {
        //使用验证权限过滤器
        $behaviors = parent::behaviors();
        if(in_array($this->action->id,['verify','token','public-key','area','sign-video','brokerage','throw-count','log','token-material','verify-code', 'startup', 'version', 'open-advert'])){
            unset($behaviors['authenticator']);
        }
        return $behaviors;
    }

    public function actionLog(){
        $logModel = new LogSystem();
        if($result = $logModel->loadParams($this->params,'post')){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$logModel->saveLog());
    }

    /*
     * 获取公钥
     * */
    public function actionPublicKey($device_number,$type){
        return $this->returnData('SUCCESS',['public_key'=>SystemPublicKey::generatePublicKey($device_number,$type)]);
    }

    /**
     * 获取系统电话
     */
    public function actionTelephone(){
        return $this->returnData('SUCCESS',['telephone'=>SystemConfig::getConfig("service_phone")]);
    }

    /**
     * 获取系统版本
     */
    public function actionVersion(){
        $versionModel = new SystemVersion();
        if($result = $versionModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$versionModel->getVersion());
    }

    /**
     * 获取地区列表
     */
    public function actionArea(){
        $addressModel = new SystemAddress();
        $addressModel->loadParams($this->params,$this->action->id);
        return $this->returnData('SUCCESS',$addressModel->getArea());
    }

    /*
     * 获取验证码
     * */
    public function actionVerify(){
        //获取手机号
        $mobile = \Yii::$app->request->get('mobile');
        //验证手机号
        $mobileType = PublicClass::checkMobileFormat($mobile);
        if(!$mobileType){
            return $this->returnData('MOBILE_ERROR');
        }
        //获取短信类型
        $type = (int)\Yii::$app->request->get('type');
        //发送前的验证
        $validateResult = SystemVerify::afterSendVerify($mobile,$type);
        if($validateResult != 'SUCCESS'){
            return $this->returnData($validateResult);
        }
        $wechat_id = (int)\Yii::$app->request->get('wechat_id');
        //验证权限
        if($token = \Yii::$app->request->get('token')){
            if($wechat_id){
                if(!MemberWeixin::validateToken($wechat_id,$token)){
                    return $this->returnData('ERROR');
                }
                $redisKey = 'wechat_id:'.$wechat_id;
            }else{
                $memberModel = Member::findIdentityByAccessToken($token);
                if(!$memberModel){
                    return $this->returnData('ERROR');
                }
                $redisKey = 'member_id:'.$memberModel->id;
            }
        }else{
            $authResult = SystemPublicKey::validatePublicKey();
            if($authResult != 'SUCCESS'){
                return $this->returnData('ERROR');
            }
            $redisKey = 'public_key:'.$_SERVER['HTTP_PUBLIC_KEY'];
        }

        if(Redis::getInstance(3)->sismember('send_message_black_list',$redisKey)){
            return $this->returnData('TOKEN_IN_BLACK_LIST');
        }
        $configNumber = SystemConfig::getConfig('send_number_in_black');
        if(empty($configNumber)){
            $configNumber = 50;
        }
        $resultCurl = ToolsClass::curl(\Yii::$app->params['memberServerUrl'].'/Shortmessage/rest',[
            'mobile'=>$mobile,
            'type'=>$type,
            'ip'=>ToolsClass::getIp(),
            'wang'=>2
        ],'POST',1);

        $resultCurl = ToolsClass::checkCurlMemberResult($resultCurl,'verify',$type);
        //var_dump($resultCurl);exit;
        if($resultCurl['status'] == 'SUCCESS'){
            $sendNumber = Redis::getInstance(2)->INCR("message:".$redisKey);
            if($sendNumber >= $configNumber){
                Redis::getInstance(3)->sadd("send_message_black_list",$redisKey);
            }
        }
        return $this->returnData($resultCurl['status']);
    }



    /*
     * 根据地区获取店主佣金
     * */
    public function actionBrokerage(){
        $priceModel = new SystemAddressLevel();
        if($result = $priceModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $price = [];
        if($priceModel->screen_number == 2){
            $price['price'] = SystemConfig::getConfig('small_shop_price_first_install_apply');
            $price['month_price'] = SystemConfig::getConfig('small_shop_subsidy_price');
        }else{
            $price = LogAccount::getKeeperPrice($priceModel->area_id);
        }
        return $this->returnData('SUCCESS',['price'=>$price['price'],'month_price'=>$price['month_price'], 'token'=>ToolsClass::getKeeperBrokerageToken($price['price'],$price['month_price'])]);
    }

    //获取腾讯云token
    public function actionToken(){
        return [
            'token'=>\Yii::$app->cos->createReusableSignature()
        ];
    }

    //获取腾讯云token(bucket yulong)
    public function actionTokenMaterial(){
        return [
            'token'=>\Yii::$app->cos_gg->createReusableSignature()
        ];
    }

    //获取腾讯云上传视频的sign
    public function actionSignVideo(){
        return [
            'token'=>\Yii::$app->cos_gg->createSignVideo()
        ];
    }
    /*
     * 广告投放统计
     * */
    public function actionThrowCount(){
        $countModel = new OrderThrowCount();
        $play_number = \Yii::$app->request->post('play_number');
        if (empty($play_number)) {
            return $this->returnData("PLAY_NUMBER_EMPTY");
        }
        return $this->returnData('SUCCESS',['success_number'=>$countModel->createRecord($play_number)]);
    }

    /**
     * 提交反馈
     */
    public function actionFeedback(){
        $feedbackModel = new Feedback();
        if($result = $feedbackModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if($feedbackModel->save()){
            return $this->returnData();
        }
        return $this->returnData('ERROR');
    }

    //提交申请时验证手机验证码
    public function actionVerifyCode(){
        return true;
        $params = \Yii::$app->request->get();
        $info[0] = $params['mobile'];
        $info[1] = $params['verifyCode'];
        return PublicClass::authVerify($info) ? ToolsClass::getKeeperBrokerageToken($info[0]) : false;
    }

    //获取启动页
    public function actionStartup(){
        $startupModel = new SystemStartup();
        if($result = $startupModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $res = $startupModel->getStartup();
        return $this->returnData('SUCCESS', $res);
    }

    //是否开启广告配置
    public function actionOpenAdvert(){
        return $this->returnData('SUCCESS',['is_open'=>SystemConfig::getConfig('bottom_menu_advert')]);
    }

    //获取当前时间
    public function actionGetDateTime($word, $hour){
        if(isset($this->params['minute']) && $this->params['minute'] == 1){
            return date('H:i');
        }
        return $this->returnData('SUCCESS',ToolsClass::getDate($word, $hour));
    }

    //微信分享配置
    public function actionShare(){
        $price = SystemConfig::getConfig('shop_contact_price_outside_self')/100;
        return $this->returnData('SUCCESS',[
            'title' => '玉龙传媒'.$price.'元现金奖励等您来拿',
            'content' => '每成功推荐一家理发店安装LED屏幕，得'.$price.'元现金奖励',
            'image' => 'https://i1.bjyltf.com/back-stage/20190520/78571558334959.png',
        ]);
    }

    public function actionGetLedOrPosterSpec(){
        $systemModel = new SystemConfig();
        if($result = $systemModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS', $systemModel->getLedOrPosterSpec());
    }

}
