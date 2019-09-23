<?php
namespace wap\controllers;
use wap\core\WapController;
use wap\models\SystemConfig;
use wap\models\SystemVersion;
use yii\web\Controller;
use wap\models\MemberWeixin;
use Yii;
use common\libs\ToolsClass;

/**
 * 微信wap页
 */
class WechatController extends WapController
{

    /**
     * 安装单验证动态码
     */
    public function actionIndex(){
        $cookies = \Yii::$app->request->cookies;//注意此处是request
        $openid = $cookies->get('openid');//设置默认值
        $token=$cookies->get('token');//openid 对应的token
        if(empty($openid) or empty($token)){
            header("Content-type: text/html; charset=utf-8");
            if(!isset($_GET['code'])){
                $APPID=Yii::$app->params['wxappid'];//'wx6514604eddcd7c6c';
                //$REDIRECT_URI='http://123.207.141.131/index/';
                $REDIRECT_URI='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
                $scope='snsapi_base';
                $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$APPID.'&redirect_uri='.urlencode($REDIRECT_URI).'&response_type=code&scope='.$scope.'&state=wx'.'#wechat_redirect';
                header("Location:".$url);
            }else{
                $appid = Yii::$app->params['wxappid'];
                $secret = Yii::$app->params['wxappsecret'];
                $code = $_GET["code"];
                $openidB='';
                $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
                $res=ToolsClass::curl($get_token_url);
                $json_obj = json_decode($res,true);
                if(is_array($json_obj) && !empty($json_obj)){
                    if(isset($json_obj['openid'])){
                        $openidB=$json_obj['openid'];
                    }
                }
                $token = md5($openidB.Yii::$app->params['systemSalt']);
                $meberweixin=new MemberWeixin();
                $openid=$meberweixin->saveOpenid($openidB);
                $cookies = \Yii::$app->response->cookies;
                $cookies->add(new \yii\web\Cookie([
                    'name' => 'openid',
                    'value' => $openid,
                    'expire'=>time()+60*60*24*30
                ]));
                $cookies->add(new \yii\web\Cookie([
                    'name' => 'token',
                    'value' => $token,
                    'expire'=>time()+60*60*24*30
                ]));
                $this->redirect(array('/shop/create?wechat_id='.$openid.'&token='.$token));
            }
        }else{
            $this->redirect(array('/shop/create?wechat_id='.$openid.'&token='.$token));
        }
    }

    public function actionWechatintroduce(){
        $cookies = \Yii::$app->request->cookies;//注意此处是request
        $openid = $cookies->get('openid');//设置默认值
        $token=$cookies->get('token');//openid 对应的token
        if(empty($openid) or empty($token)){
            header("Content-type: text/html; charset=utf-8");
            if(!isset($_GET['code'])){
                $APPID=Yii::$app->params['wxappid'];//$APPID='wx6514604eddcd7c6c';
                //$REDIRECT_URI='http://123.207.141.131/index/';
                $REDIRECT_URI='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
                $scope='snsapi_base';
                $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$APPID.'&redirect_uri='.urlencode($REDIRECT_URI).'&response_type=code&scope='.$scope.'&state=wx'.'#wechat_redirect';
                header("Location:".$url);
            }else{
                $appid = Yii::$app->params['wxappid'];//"wx6514604eddcd7c6c";
                $secret = Yii::$app->params['wxappsecret'];
                $code = $_GET["code"];
                $openidB='';
                $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
                $res=ToolsClass::curl($get_token_url);
                $json_obj = json_decode($res,true);
                if(is_array($json_obj) && !empty($json_obj)){
                    if(isset($json_obj['openid'])){
                        $openidB=$json_obj['openid'];
                    }
                }
                $token = md5($openidB.Yii::$app->params['systemSalt']);
                $meberweixin=new MemberWeixin();
                $openid=$meberweixin->saveOpenid($openidB);
                $cookies = \Yii::$app->response->cookies;
                $cookies->add(new \yii\web\Cookie([
                    'name' => 'openid',
                    'value' => $openid,
                    'expire'=>time()+60*60*24*30
                ]));
                $cookies->add(new \yii\web\Cookie([
                    'name' => 'token',
                    'value' => $token,
                    'expire'=>time()+60*60*24*30
                ]));
                $this->redirect(array('/wechat/introduce?wechat_id='.$openid.'&token='.$token));
            }
        }else{
            $this->redirect(array('/wechat/introduce?wechat_id='.$openid.'&token='.$token));
        }
    }

    /*
     * 安装好处
     * */
    public function actionIntroduce(){
        $wechat_id = (int)\Yii::$app->request->get('wechat_id');
        $token = \Yii::$app->request->get('token');
        return $this->render('introduce',[
            'wechat_id'=>$wechat_id,
            'token'=>$token,
        ]);
    }

    public function actionWechatapply(){
        $cookies = \Yii::$app->request->cookies;//注意此处是request
        $openid = $cookies->get('openid');//设置默认值
        $token=$cookies->get('token');//openid 对应的token
        if(empty($openid) or empty($token)){
            header("Content-type: text/html; charset=utf-8");
            if(!isset($_GET['code'])){
                $APPID = Yii::$app->params['wxappid'];
                //$REDIRECT_URI='http://123.207.141.131/index/';
                $REDIRECT_URI='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
                $scope='snsapi_base';
                $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$APPID.'&redirect_uri='.urlencode($REDIRECT_URI).'&response_type=code&scope='.$scope.'&state=wx'.'#wechat_redirect';
                header("Location:".$url);
            }else{
                $appid = Yii::$app->params['wxappid'];//"wx6514604eddcd7c6c";
                $secret = Yii::$app->params['wxappsecret'];
                $code = $_GET["code"];
                $openidB='';
                $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
                $res=ToolsClass::curl($get_token_url);
                $json_obj = json_decode($res,true);
                if(is_array($json_obj) && !empty($json_obj)){
                    if(isset($json_obj['openid'])){
                        $openidB=$json_obj['openid'];
                    }
                }
                $meberweixin=new MemberWeixin();
                $token = md5($openidB.Yii::$app->params['systemSalt']);
                $openid=$meberweixin->saveOpenid($openidB);
                $cookies = \Yii::$app->response->cookies;
                $cookies->add(new \yii\web\Cookie([
                    'name' => 'openid',
                    'value' => $openid,
                    'expire'=>time()+60*60*24*30
                ]));
                $cookies->add(new \yii\web\Cookie([
                    'name' => 'token',
                    'value' => $token,
                    'expire'=>time()+60*60*24*30
                ]));
                $this->redirect(array('/wechat/apply?wechat_id='.$openid.'&token='.$token));
            }
        }else{
            $this->redirect(array('/wechat/apply?wechat_id='.$openid.'&token='.$token));
        }
    }

    /*
     * 申请安装
     * */
    public function actionApply(){
        $wechat_id = (int)\Yii::$app->request->get('wechat_id');
        $token = \Yii::$app->request->get('token');
        return $this->render('apply',[
            'wechat_id'=>$wechat_id,
            'token'=>$token,
        ]);
    }

    /*
     * 关于我们
     * */
    public function actionAbout(){
        $versionModel = new SystemVersion();
        $iosVersion = $versionModel->getVersionUrl(2);
        $androidVersion = $versionModel->getVersionUrl(1);
        return $this->render('about',[
            'ios'=>$iosVersion,
            'android'=>$androidVersion
        ]);
    }

    /*
     * 推荐安装LED屏
     * */
    public function actionInstallFirst(){
        return $this->render('install-first');
    }
    /*
     * 推荐安装LED屏详情
     * */
    public function actionInstallFirstView(){
        $versionModel = new SystemVersion();
        $iosVersion = $versionModel->getVersionUrl(2);
        $androidVersion = $versionModel->getVersionUrl(1);
        return $this->render('install-first-view',[
            'ios'=>$iosVersion,
            'android'=>$androidVersion
        ]);
    }

    /*
     * 成为业务合作人
     * */
    public function actionInstallSecond(){
        return $this->render('install-second');
    }
    /*
     * 成为业务合作人详情
     * */
    public function actionInstallSecondView(){
        $versionModel = new SystemVersion();
        $iosVersion = $versionModel->getVersionUrl(2);
        $androidVersion = $versionModel->getVersionUrl(1);
        return $this->render('install-second-view',[
            'ios'=>$iosVersion,
            'android'=>$androidVersion
        ]);
    }

    /*
     * 发展合作人下线
     * */
    public function actionInstallThird(){
        return $this->render('install-third');
    }

    /*
     * 发展合作人下线详情
     * */
    public function actionInstallThirdView(){
        $versionModel = new SystemVersion();
        $iosVersion = $versionModel->getVersionUrl(2);
        $androidVersion = $versionModel->getVersionUrl(1);
        return $this->render('install-third-view',[
            'ios'=>$iosVersion,
            'android'=>$androidVersion
        ]);
    }
    /*
    * 玉龙传媒合作政策
    * */
    public function actionCooperationPolicy(){
        $versionModel = new SystemVersion();
        $iosVersion = $versionModel->getVersionUrl(2);
        $androidVersion = $versionModel->getVersionUrl(1);
        $configModel = new SystemConfig();
        $result_phone = $configModel->getConfigById("service_phone");
        $result_email = $configModel->getConfigById("e_mail");

        return $this->render('cooperation-policy',[
            'service_email' => $result_email['content'],
            'service_phone' => $result_phone['content'],
            'ios'=>$iosVersion,
            'android'=>$androidVersion
        ]);
    }
//申请记录列表
    public function actionRecord(){
//         $openid=7;
//         $token = md5('o7kGnv1iiwgTMFCvLjOQwGAWeLdo'.Yii::$app->params['systemSalt']);
        //    $wechat_id = \Yii::$app->request->get('wechat_id');
        $cookies = \Yii::$app->request->cookies;//注意此处是request
        $openid = $cookies->get('openid');//设置默认值
        $token=$cookies->get('token');//openid 对应的token
        if(empty($openid) or empty($token)){
            header("Content-type: text/html; charset=utf-8");
            if(!isset($_GET['code'])){
                $APPID = Yii::$app->params['wxappid'];//  $APPID='wx6514604eddcd7c6c';
                //$REDIRECT_URI='http://123.207.141.131/index/';
                $REDIRECT_URI='http://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"];
                $scope='snsapi_base';
                $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.$APPID.'&redirect_uri='.urlencode($REDIRECT_URI).'&response_type=code&scope='.$scope.'&state=wx'.'#wechat_redirect';
                header("Location:".$url);
            }else{
                $appid = Yii::$app->params['wxappid'];//"wx6514604eddcd7c6c";
                $secret = Yii::$app->params['wxappsecret'];
                $code = $_GET["code"];
                $openidB='';
                $get_token_url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$appid.'&secret='.$secret.'&code='.$code.'&grant_type=authorization_code';
                $res=ToolsClass::curl($get_token_url);
                $json_obj = json_decode($res,true);
                if(is_array($json_obj) && !empty($json_obj)){
                    if(isset($json_obj['openid'])){
                        $openidB=$json_obj['openid'];
                    }
                }
                $token = md5($openidB.Yii::$app->params['systemSalt']);
                $meberweixin=new MemberWeixin();
                $openid=$meberweixin->saveOpenid($openidB);
                $cookies = \Yii::$app->response->cookies;
                $cookies->add(new \yii\web\Cookie([
                    'name' => 'openid',
                    'value' => $openid,
                    'expire'=>time()+60*60*24*30
                ]));
                $cookies->add(new \yii\web\Cookie([
                    'name' => 'token',
                    'value' => $token,
                    'expire'=>time()+60*60*24*30
                ]));
            }
        }
        $meberweixin=new MemberWeixin();
        $openidModel=$meberweixin->getOpenid($openid);
        if($openidModel){
            $member_id=$openidModel['member_id'];
            $wx_member_id=$openidModel['id'];
        }
        else{
            $member_id=0;
            $wx_member_id=$openid;
        }
        return $this->render('record',[
            'member_id'=>$member_id,
            'wx_member_id'=>$wx_member_id,
            'token' => $token,
            'wechat_id' => $wx_member_id,
        ]);
    }
//申请记录详情
    public function actionRecordinfo(){
        $shopid = (int)\Yii::$app->request->get('shopid');
        $token = \Yii::$app->request->get('token');
        $wechat_id=\Yii::$app->request->get('wechat_id');
        return $this->render('recordinfo',[
            'shopid'=>$shopid,
            'token'=>$token,
            'wechat_id'=>$wechat_id,
        ]);
    }

}
