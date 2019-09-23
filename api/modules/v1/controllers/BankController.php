<?php
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\MemberBank;
use api\modules\v1\models\SystemAddress;
use api\modules\v1\models\SystemBank;
use common\libs\DataClass;
use common\libs\ToolsClass;

/**
 * 银行
 */
class BankController extends ApiController
{

    /**
     * 获取系统银行列表
     */
    public function actionSystem(){
        $resultBank = SystemBank::systemBanks();
        if($resultBank){
            sort($resultBank);
        }
        return $this->returnData('SUCCESS',$resultBank);
    }

    /*
     * 新增绑定
     * */
    public function actionCreate(){
        $bankModel = new MemberBank();
        if($result = $bankModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if(!$bankModel->loadBank()){
            return $this->returnData('BANK_ID_ERROR');
        }
//        if(!ToolsClass::checkVerify($bankModel->mobile,$bankModel->verify)){
//            return $this->returnData('VERIFY_ERROR');
//        }
        if($bankModel->save()){
            return $this->returnData();
        }
        return $this->returnData('ERROR');
    }

    /*
     * 解除绑定
     * */
    public function actionDelete($bank_id){
        $bankModel = new MemberBank();
        $resultBack = $bankModel->deleteMemberBank($bank_id);
        return $this->returnData('SUCCESS',$resultBack);
    }

    /*
     * 获取我的银行卡信息列表
     * */
    public function actionIndex(){
        $bankModel = new MemberBank();
        $resultBack = $bankModel->getMemberBank();
        return $this->returnData('SUCCESS',$resultBack);
    }
}
