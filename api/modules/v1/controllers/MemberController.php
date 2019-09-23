<?php
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\core\ApiQueryParamAuth;
use api\modules\v1\models\Member;
use api\modules\v1\models\MemberAccount;
use api\modules\v1\models\MemberAccountCount;
use api\modules\v1\models\MemberAccountMessage;
use api\modules\v1\models\MemberAreaCount;
use api\modules\v1\models\MemberEquipment;
use api\modules\v1\models\MemberInfo;
use api\modules\v1\models\MemberLower;
use api\modules\v1\models\MemberMessage;
use api\modules\v1\models\MemberPassword;
use api\modules\v1\models\Shop;
use api\modules\v1\models\ShopApply;
use api\modules\v1\models\SystemAddress;
use api\modules\v1\models\SystemConfig;
use common\libs\DataClass;
use common\libs\ToolsClass;
use Yii;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\QueryParamAuth;

/**
 * 用户
 */
class MemberController extends ApiController
{

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        if(in_array($this->action->id,['register','login','password','get-parent'])){
            unset($behaviors['authenticator']);
        }
        return $behaviors;
    }

    /**
     * 用户注册
     */
    public function actionRegister(){
        $memberModel = new Member();
        if($result = $memberModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $resultCurl = ToolsClass::curl(\Yii::$app->params['memberServerUrl'].'/Register/rest',[
            'name'=>$memberModel->name,
            'password'=>$memberModel->password,
            'mobile'=>$memberModel->mobile,
            'mobilecode'=>$memberModel->verify,
            'data'=>json_encode([
                'parent_id'=>$memberModel->parent_id,
            ])
        ],'POST',1);
        $resultCurl = ToolsClass::checkCurlMemberResult($resultCurl,'register');
        if($resultCurl['status'] != 'SUCCESS'){
            return $this->returnData($resultCurl['status']);
        }
        $memberModel->id = $resultCurl['data'];

        //注册成功
        if($result = $memberModel->register()){
            return $this->returnData('SUCCESS',$result);
        }
        return $this->returnData('ERROR');
    }

    public function actionLogout(){
        $memberModel = new Member();
        if($result = $memberModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if($memberModel->logout()){
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }
    /**
     * 用户登陆
     */
    public function actionLogin(){
        $memberModel = new Member();
        if($result = $memberModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $resultCurl = ToolsClass::curl(\Yii::$app->params['memberServerUrl'].'/Login/rest',[
            'type'=>2,
            'username'=>$memberModel->mobile,
            'password'=>$memberModel->password,
        ],'POST',1);
        $resultCurl = ToolsClass::checkCurlMemberResult($resultCurl,'login');
        if($resultCurl['status'] != 'SUCCESS'){
            return $this->returnData($resultCurl['status']);
        }
        $memberModel->id = $resultCurl['data']['id'];
        if(isset($resultCurl['data']['parent_id'])){
            $memberModel->parent_id = $resultCurl['data']['parent_id'];
        }
        $oldMobile = '';
        if(isset($resultCurl['data']['mobile'])){
            $oldMobile = $resultCurl['data']['mobile'];
        }
        //登陆成功
        if($resultLogin = $memberModel->login($oldMobile)){
            return $this->returnData('SUCCESS',$resultLogin);
        }
        return $this->returnData('ERROR');
    }

    /*
     * 修改支付密码
     * */
    public function actionPaymentPassword(){
        $memberModel = new MemberPassword();
        if($result = $memberModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if($memberModel->password != $memberModel->repeat_password){
            return $this->returnData('TOW_PASSWORD_ERROR');
        }
        if(!member::checkMobile($memberModel->mobile)){
            return $this->returnData('MEMBER_MOBILE_ERROR');
        }
        if(!ToolsClass::checkVerify($memberModel->mobile,$memberModel->verify)){
            return $this->returnData('VERIFY_ERROR');
        }
        if($memberModel->updatePaymentPassword()){
            return $this->returnData('SUCCESS');
        }
        return $this->returnData();
    }

    /*
     * 获取支付密码
     * */
    public function actionGetPaymentPassword(){
        return $this->returnData('SUCCESS',['payment_password'=>MemberPassword::checkPaymentPassword()]);
    }

    /**
     * 修改密码
     */
    public function actionPassword(){
        $memberModel = new Member();
        if($result = $memberModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }

        if($memberModel->password != $memberModel->repeat_password){
            return $this->returnData('TOW_PASSWORD_ERROR');
        }

        $resultCurl = ToolsClass::curl(\Yii::$app->params['memberServerUrl'].'/Modifyloginpwd/rest',[
            'mobile'=>$memberModel->mobile,
            'mobilecode'=>$memberModel->verify,
            'newpassword'=>$memberModel->password
        ],'POST',1);
        $resultCurl = ToolsClass::checkCurlMemberResult($resultCurl,'password');

        if($resultCurl['status'] == 'SUCCESS'){
            return $this->returnData('SUCCESS',$memberModel->updatePassword());
        }
        return $this->returnData($resultCurl['status']);
    }

    /**
     * 获取地区
     */
    public function actionArea(){
        $userModel = new Member();
        $resultArea['examine_status'] = 0;
        if($resultArea = $userModel->getMemberArea()){
            if($resultArea['member_type'] == 2){
                $resultArea['admin_area'] = SystemAddress::getAreaNameById($resultArea['admin_area']);
            }else{
                //判断该会员安装的店铺数是否大于或等于配置数
                $memNum = Shop::getMemNumber(\Yii::$app->user->id);
                $configNum = SystemConfig::getConfig('shop_number');
                if($memNum >= $configNum){
                    $memberModel = new MemberInfo();
                    $resultArea['examine_status'] = $memberModel->getExamineStatus();
                    $areaModel = new MemberAreaCount();
                    $resultArea['area_list'] = $areaModel->getMemberAreaList();
                }else{
                    $resultArea['area_list'] = null;
                }
            }
        }
        return $this->returnData('SUCCESS',$resultArea);
    }

    /**
     * 获取我的消息
     */
    public function actionMessage(){
        $messageModel = new MemberMessage();
        $messageModel->loadParams($this->params,$this->action->id);
        $result = $messageModel->getMemberMessage();
        return $this->returnData('SUCCESS',$result);
    }

    /**
     * 获取用户信息
     */
    public function actionView(){
        $memberModel = new Member();
        $result = $memberModel->getInfo();
        return $this->returnData('SUCCESS',$result);
    }

    /*
     * 获取联系人信息
     * */
    public function actionGetParent($mobile){
        $member_type = (int)\Yii::$app->request->get('member_type');
        $where['mobile'] = $mobile;
        if($member_type){
            $where['member_type'] = $member_type;
        }
        $memberModel = Member::find()->where($where)->select('name,mobile')->asArray()->one();
        if($memberModel){
            return $this->returnData('SUCCESS',$memberModel);
        }
        if($member_type){
            return $this->returnData('CREATE_SHOP_MEMBER_MOBILE_ERROR_1');
        }
        return $this->returnData('PARENT_NUMBER_ERROR');
    }

    /**
     * 修改用户信息
     */
    public function actionUpdate(){
        $memberModel = \Yii::$app->user->identity;
        if($result = $memberModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if($memberModel->avatar){
            $memberModel->avatar = ToolsClass::replaceCosUrl($memberModel->avatar);
        }
        if($memberModel->save()){
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }
    /*
      * 获取极光推送的状态
      * */
    public function actionGetStatus(){
        $equipmentModel = new MemberEquipment();
        return $this->returnData('SUCCESS',$equipmentModel->getPushStatus());
    }

    /*
     * 获取电工证证件信息
     * */
    public function actionGetCert(){
        $memberInfoModel = new MemberInfo();
        return $this->returnData('SUCCESS',$memberInfoModel->getElectricianCertificate());
    }


    /**
     * 修改电工证信息
     */
    public function actionUpdateCert(){
        $memberInfoModel = MemberInfo::findOne(['member_id'=>\Yii::$app->user->id,'examine_status'=>1]);
        if(empty($memberInfoModel)){
            return $this->returnData('MEMBER_NOT_CERTIFICATION');
        }
        if($result = $memberInfoModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData($memberInfoModel->updateMemberCert());
    }

    /**
     * 获取身份证信息
     */
    public function actionGetId(){
        $memberInfoModel = new MemberInfo();
        return $this->returnData('SUCCESS',$memberInfoModel->getInfo());
    }

    /**
     * 修改身份证信息
     */
    public function actionUpdateId(){
        $memberInfoModel = new MemberInfo();
        if($result = $memberInfoModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if($memberInfoModel->checkExamineStatus()){
            return $this->returnData('MEMBER_PUT_ID_ERROR');
        }
        if($memberInfoModel->updateMemberInfo()){
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }
    /*
     * 修改极光推送状态
     * */
    public function actionUpdatePush(){
        $equipmentModel = new MemberEquipment();
        if($result = $equipmentModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if($equipmentModel->updatePush()){
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }
    /*
     * 修改极光推送id
     */
    public function actionUpdatePid(){
        $equipmentModel = new MemberEquipment();
        if($result = $equipmentModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if($equipmentModel->updatePushId()){
            return $this->returnData('SUCCESS',['push_id'=>$equipmentModel->push_id]);
        }
        return $this->returnData('ERROR');
    }

    /**
     * 修改手机号
     */
    public function actionUpdateMobile($member_id){
        $memberModel = new Member();
        if($result = $memberModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $resultCurl = ToolsClass::curl(\Yii::$app->params['memberServerUrl'].'/Modifyphone/rest',[
            'password'=>$memberModel->password,
            'mobile'=>$memberModel->mobile,
            'mobilecode'=>$memberModel->verify,
            'id'=>Yii::$app->user->id,
        ],'POST',1);
        $resultCurl = ToolsClass::checkCurlMemberResult($resultCurl,'password');
        if($resultCurl['status'] != 'SUCCESS'){
            return $this->returnData($resultCurl['status']);
        }
        $memberModel->updateMobile(Yii::$app->user->identity->mobile);
        return $this->returnData('SUCCESS');
    }

    /**
     * 修改要管理的地区
     */
    public function actionUpdateArea(){
        $memberModel = new Member();
        if($result = $memberModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if($result = $memberModel->updateArea()){
            return $this->returnData($result);
        }
        return $this->returnData('ERROR');
    }
    /*
     *
     */
}
