<?php
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\LogAccount;
use api\modules\v1\models\MemberAccount;
use api\modules\v1\models\MemberAccountCount;
use api\modules\v1\models\MemberAccountMessage;
use api\modules\v1\models\MemberInfo;
use api\modules\v1\models\MemberPassword;
use api\modules\v1\models\SystemAddress;
use common\libs\ToolsClass;

/**
 * 资金流水明细
 */
class AccountController extends ApiController
{

    /**
     * 获取流水列表
     */
    public function actionIndex(){
        $accountModel = new LogAccount();
        $accountModel->loadParams($this->params,$this->action->id);
        $accountList = $accountModel->getMemberAccountList();
        list($income,$pay) = $accountModel->getPriceCount();
        $result = [
            'item'=>$accountList,
            'income'=>$income,
            'date_list'=>$accountModel->getDateList(),
            'pay'=>$pay,
        ];
        return $this->returnData('SUCCESS',$result);
    }

    /**
     * 获取我的业绩详情
     */
    public function actionView(){
        $accountCountModel = new MemberAccountCount();
        $accountCountModel->loadParams($this->params,$this->action->id);
        if($accountCountModel->create_at){
            $result = $accountCountModel->getMemberAccount();
        }else{
            $accountModel = new MemberAccount();
            $messageModel = new MemberAccountMessage();
            $result = $accountModel->getMemberAccount();
            $result['date_list'] = $accountCountModel->getMemberAccountDayList();
            $result['message'] = $messageModel->getMemberMessage();
            array_unshift($result['date_list'],['create_at'=>'总计']);
        }
        $res = (new MemberInfo())->getMemberStatus();
        $result['id_card'] = $res['number'];
        $result['memberName'] = $res['memberName'];
        $result["payment_password"] = MemberPassword::checkPaymentPassword();
        return $this->returnData('SUCCESS',$result);
    }
}
