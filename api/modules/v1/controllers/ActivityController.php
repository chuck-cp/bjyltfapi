<?php
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\Shop;
use api\modules\v1\models\SystemConfig;
use api\modules\v1\models\Activity;
use api\modules\v1\models\ActivityDetail;
use common\libs\DataClass;
use common\libs\ToolsClass;
use yii\web\NotFoundHttpException;


/**
 * 安装活动
 */
class ActivityController extends ApiController
{
    public $activity;
    public function behaviors()
    {
        //使用验证权限过滤器
        $behaviors = parent::behaviors();
        unset($behaviors['authenticator']);
        return $behaviors;
    }

    public function beforeAction($action)
    {
        if (in_array($action->id,['detail','member','shop'])) {
            $activity_token = \Yii::$app->request->get('activity_token');
            $this->activity = Activity::find()->where(['activity_token' => $activity_token])->one();
            if (empty($this->activity)) {
                throw new NotFoundHttpException();
            }
        }
        return parent::beforeAction($action);
    }

    // 活动页登陆
    public function actionLogin() {
        $activityModel = new Activity();
        if ($result = $activityModel->loadParams($this->params,'create')) {
            return $this->returnData($result);
        }
        // 验证验证码
        if (!ToolsClass::checkVerify($activityModel->member_mobile,$activityModel->verify)) {
            return $this->returnData('VERIFY_ERROR');
        }
        // 登陆
        if ($result = $activityModel->createActivity()) {
            return $this->returnData('SUCCESS',$result);
        }
        return $this->returnData('ERROR');
    }

    // 获取活动页token
    public function actionInit($token) {
        $activityModel = new Activity();
        if ($result = $activityModel->getMemberIdByToken($token)) {
            return $this->returnData('SUCCESS',$result);
        }
        return $this->returnData('ERROR');
    }

    // 获取用户信息
    public function actionMember($activity_token) {
        return $this->returnData('SUCCESS',['price' => ToolsClass::priceConvert($this->activity['price']),'member_mobile' => $this->activity['member_mobile'], 'member_name' => $this->activity['member_name']]);
    }

    // 获取收益明细
    public function actionDetail($activity_token) {
        $detailModel = new ActivityDetail();
        $result = $detailModel->getIncomeDetail($this->activity['id']);
        $result['price'] = ToolsClass::priceConvert($this->activity['price']);
        return $this->returnData('SUCCESS', $result);
    }

    // 签约店铺
    public function actionShop() {
        $detailModel = new ActivityDetail();
        if ($result = $detailModel->loadParams($this->params,'create')) {
            return $this->returnData($result);
        }
        $result = $detailModel->createActivityDetail($this->activity['id'],$this->activity['member_mobile']);
        return $this->returnData($result);
    }

    // 获取首页的消息
    public function actionNotice() {
        $result['configPrice'] = ToolsClass::priceConvert(SystemConfig::getConfig('shop_contact_price_outside_self'));
        $activityModel = Activity::find()->where(['>','price',0])->select('member_mobile,price')->orderBy('id desc')->limit(20)->asArray()->all();
        if (empty($activityModel)) {
            $result['noticeList'] = DataClass::activityDefaultNotice();
        } else {
            foreach ($activityModel as $key => $value) {
                $activityModel[$key] = [
                    'mobile' => ToolsClass::encryptMobile($value['member_mobile']),
                    'price' => ToolsClass::priceConvert($value['price'])
                ];
            }
            $activityLength = count($activityModel);
            if ($activityLength < 20) {
                $activityModel = array_merge($activityModel,array_slice(DataClass::activityDefaultNotice(),0, 20 - $activityLength));
            }
            $result['noticeList'] = $activityModel;
        }
        return $this->returnData('SUCCESS',$result);
    }
}
