<?php
namespace pc\controllers;
use pc\models\LoginForm;
use pc\models\SystemConfig;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\captcha\Captcha;

/**
 * Site controller
 */
class IndexController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'error','captcha'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],

        ];
    }


    public function actionIndex()
    {
        $this->layout = false;
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goHome();
        } else {
            $configModel= new SystemConfig();
            $resultConfig = $configModel->getConfigById("service_phone");
            return $this->render('index', [
                'model' => $model,
                'service_phone' => $resultConfig['content'],
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }


//    public function actions() {
//        return [
//            'captcha' => [
//                'class' => 'yii\captcha\CaptchaAction',
//                'maxLength' => 4,
//                'minLength' => 4
//            ],
//        ];
//    }



}
