<?php

namespace wap\core;

use common\libs\ToolsClass;
use wap\models\Member;
use wap\models\MemberEquipment;
use wap\models\MemberWeixin;
use Yii;
use yii\web\Controller;

class WapController extends Controller
{
    public $layout=false;
    // 获取微信的TOKEN
    public function getWxToken() {
        $code = Yii::$app->request->get('code');
        $app_id = Yii::$app->params['wxappid'];
        if (empty($code)) {
            $redirect_uri = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
            $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$app_id.'&redirect_uri='.urlencode($redirect_uri).'&response_type=code&scope=snsapi_base&state=wx#wechat_redirect';
            header("Location:".$url);
            exit;
        }
        $secret = Yii::$app->params['wxappsecret'];
        $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$app_id.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
        $resultCurl = ToolsClass::curl($get_token_url);
        $resultCurl = json_decode($resultCurl,true);
        if(empty($resultCurl) || !isset($resultCurl['openid'])){
            return false;
        }
        $token = md5($resultCurl['openid'].Yii::$app->params['systemSalt']);
        $wxModel = new MemberWeixin();
        $wxChatId = $wxModel->saveOpenid($resultCurl['openid']);
        return [$token,$wxChatId];
    }

    public function authentication(){
        $token = \Yii::$app->request->get('token');
        $wechat_id = (int)\Yii::$app->request->get('wechat_id');
        if($wechat_id){
            $weixinModel = MemberWeixin::find()->where(['id'=>$wechat_id])->select('open_id')->asArray()->one();
            if(empty($weixinModel)){
                return false;
            }
            return md5($weixinModel['open_id'].\Yii::$app->params['systemSalt'])  == $token;
        }else{
            $memberObj = MemberEquipment::find()->where(['token'=>$token])->one();
            if(!$memberObj){
                return false;
            }
            return Member::isInside($memberObj->member_id);
            //return true;
        }
    }
}
