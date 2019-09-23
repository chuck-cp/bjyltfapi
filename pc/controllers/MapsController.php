<?php
namespace pc\controllers;
use common\libs\ToolsClass;
use pc\models\LoginForm;
use pc\models\SystemConfig;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\captcha\Captcha;
use api\models\Shop;

/**
 * Site controller
 */
class MapsController extends Controller
{
    /**
     * @inheritdoc
     */
//    public function behaviors()
//    {
//        return [
//            'access' => [
//                'class' => AccessControl::className(),
//                'rules' => [
//                    [
//                        'actions' => ['index', 'error','map-login'],
//                        'allow' => true,
//                    ],
//                    [
//                        'actions' => ['logout'],
//                        'allow' => true,
//                        'roles' => ['@'],
//                    ],
//                ],
//            ],
//
//        ];
//    }

    //登陆
    public function actionMapLogin()
    {
        $this->layout = false;
        if (!\Yii::$app->user->isGuest) {
            $this->redirect(['maps/index','date'=>$_GET['date']]);
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->maplogin()) {
            $this->redirect(['maps/index','date'=>$_GET['date']]);
        } else {
            $configModel= new SystemConfig();
            $resultConfig = $configModel->getConfigById("service_phone");
            return $this->render('map-login', [
                'model' => $model,
                'service_phone' => $resultConfig['content'],
            ]);
        }
    }

    //发送验证码
    public function actionAjaxsend()
    {
        $mobile = Yii::$app->request->get('mobile');
        $mobilearray = ['13581948044','13691303382','13901359098'];
        if(in_array($mobile,$mobilearray)){
            $resultCurl = ToolsClass::curl(\Yii::$app->params['memberServerUrl'].'/Shortmessage/rest',[
                'mobile'=>$mobile,
                'type'=>3,
                'ip'=>ToolsClass::getIp(),
                'wang'=>2
            ],'POST',1);
            if($resultCurl) {
                if (json_decode($resultCurl)->status == 130) {
                    return json_encode(['code' => 1, 'msg' => '发送成功']);
                } else {
                    return json_encode(['code' => 2, 'msg' => '发送失败']);
                }
            }else{
                return json_encode(['code' => 2, 'msg' => '发送失败']);
            }
        }else{
            return json_encode(['code' => 2, 'msg' => '非法手机号']);
        }
    }

    public function actionIndex($date)
    {
        if (\Yii::$app->user->isGuest) {
            $this->redirect(['maps/map-login','date'=>$date]);
        }
        $this->layout = false;
        $date=$date.' 16:00:00';
        //安装完成的店铺
        $InstallFinish = Shop::find()->where(['and',['status'=>5],['<=','install_finish_at',$date],['>=','install_finish_at','00-00-00 00:00:00'],['not like','name','测试'],['<>','longitude',''],['<>','latitude','']])->select('id,name,area_name,address,screen_number,longitude,latitude,install_finish_at,status,mirror_account')->asArray()->all();
        $markerArr1 = [];
        foreach ($InstallFinish as $key1=>$value1){
            $markerArr[$key1]['title'] ='';
            $markerArr[$key1]['name'] = "<b>店铺编号:</b> ".$value1['id']."<div class='guanbi' onclick='feng()'>X</div> </br><b>店名:</b> ".$value1['name']."</br><b>地区:</b> ".$value1['area_name']." </br><b>详址:</b> ".$value1['address']." </br><b>安装台数:</b> ".$value1['screen_number']."</br><b>镜面数量:</b>".$value1['mirror_account'];
            $markerArr[$key1]['lnglat'][] = $value1['longitude'];
            $markerArr[$key1]['lnglat'][] = $value1['latitude'];
            $markerArr[$key1]['name2'] = $value1['address'];
            $markerArr[$key1]['style'] = 0;

        }
        //待安装的店铺
        $InstallStay = Shop::find()->where(['and',['status'=>[2,3,4]],['<=','shop_examine_at',$date],['>=','shop_examine_at','00-00-00 00:00:00'],['not like','name','测试'],['<>','longitude',''],['<>','latitude','']])->select('id,name,area_name,address,screen_number,longitude,latitude,install_finish_at,status,mirror_account')->asArray()->all();
        $markerArr2 = [];
        foreach ($InstallStay as $key2=>$value2){
            $markerArr2[$key2]['title'] ='';
            $markerArr2[$key2]['name'] = "<b>店铺编号:</b> ".$value2['id']." <div class='guanbi' onclick='feng()'>X</div></br><b>店名:</b> ".$value2['name']."</br><b>地区:</b> ".$value2['area_name']." </br><b>详址:</b> ".$value2['address']." </br><b>安装台数:</b> ".$value2['screen_number']."</br><b>镜面数量:</b>".$value2['mirror_account'];
            $markerArr2[$key2]['lnglat'][] = $value2['longitude'];
            $markerArr2[$key2]['lnglat'][] = $value2['latitude'];
            $markerArr2[$key2]['name2'] = $value2['address'];
            $markerArr2[$key2]['style'] = 1;
        }

        $citys = array_merge_recursive($markerArr,$markerArr2);
        return $this->render('index', [
            'citys' => json_encode($citys),
        ]);
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }


}
