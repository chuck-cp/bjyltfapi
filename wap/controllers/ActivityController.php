<?php
namespace wap\controllers;



use common\libs\ArrayClass;
use common\libs\CookieClass;
use common\libs\ToolsClass;
use wap\core\WapController;
use Yii;

class ActivityController extends WapController
{
    public function beforeAction($action)
    {
        $activity_token = CookieClass::get('activity_token');
        // 如果现在已经登陆,直接返回
        if ($activity_token) {
            // 已登录
            return parent::beforeAction($action);
        }
        $token = Yii::$app->request->get('token');
        if (in_array($action->id,['logout','shop','detail'])) {
            // 判断是否登陆
            Yii::$app->response->redirect('/activity/index?token='.$token);
            return false;
        }
        if($token) {
            // 通过APP进入
            CookieClass::set('from','app');
            CookieClass::set('token', $token);
            if (CookieClass::get('logout') == 1) { // 已退出
                return parent::beforeAction($action);
            }
            $url = Yii::$app->params['baseApiUrl'].'/activity/init?token='.$token;
            $resultCurl = ToolsClass::curl($url,[],'get');
            $resultCurl = json_decode($resultCurl,true);
            CookieClass::set('activity_token',$resultCurl['data']['activity_token']);
        } else {
            // 通过微信进入
            list($token,$wechatId) = $this->getWxToken();
            CookieClass::set('wechatId',$wechatId);
            CookieClass::set('wechatToken',$token);
        }

        return parent::beforeAction($action);
    }
    //  退出
    public function actionLogout() {
        CookieClass::del('activity_token');
        if (CookieClass::get('from') == 'app') {
            CookieClass::set('logout',1);
            Yii::$app->response->redirect('/activity?token='.CookieClass::get('token'));
        } else {
            Yii::$app->response->redirect('/activity');
        }
    }

    //  活动页面首页
    public function actionIndex() {
        $full_url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        if(strpos($full_url,"&from")>0){
            $full_url=substr($full_url,0,strpos($full_url,"&from"));
            //print_r($full_url);exit;
            Header("Location: $full_url");
            exit();
        }
        if(strpos($full_url,"?from")>0) {
            $full_rul=substr($full_url,0,strpos($full_url,"?from"));
            Header("Location: $full_url");
            exit();
        }
        return $this->render('index');
    }

    //  活动规则页面
    public function actionRule() {
        return $this->render('rule');
    }

    //  创建店铺页面
    public function actionShop() {
        return $this->render('shop',['from' => CookieClass::get('from')]);
    }

    //  奖励金明细页面
    public function actionDetail() {
        return $this->render('detail');
    }
}
