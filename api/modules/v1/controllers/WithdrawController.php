<?php
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\LogAccount;
use api\modules\v1\models\MemberAccount;
use api\modules\v1\models\MemberBank;
use api\modules\v1\models\MemberPassword;
use api\modules\v1\models\MemberWithdraw;
use api\modules\v1\models\SystemAddress;
use common\libs\ToolsClass;

/**
 * 提现
 */
class WithdrawController extends ApiController
{

    /**
     * 提现
     */
    public function actionCreate(){
        $withdrawModel = new MemberWithdraw();
        if($result = $withdrawModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        /***********是否身份认证************/
        if(!$withdrawModel->checkMemberId()){
            return $this->returnData('WITHDRAW_PRICE_EXCEED_MAX');
        }
        if(!$withdrawModel->loadBank()){
            return $this->returnData('BANK_ID_ERROR');
        }
        if(!MemberPassword::checkPaymentPassword($withdrawModel->payment_password)){
            return $this->returnData('PAYMENT_PASSWORD_ERROR');
        }
        if($resultStatus = $withdrawModel->withdraw()){
            return $this->returnData($resultStatus);
        }
        return $this->returnData('ERROR');
    }

    /**
     * 获取提现信息
     */
    public function actionView(){
        $withdrawModel = new MemberBank();
        $resultBank = $withdrawModel->getDefaultBank();
        $resultBank['balance'] = (string)ToolsClass::priceConvert(MemberAccount::getMemberPrice());
        return $this->returnData('SUCCESS',$resultBank);
    }
}
