<?php

namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\Shop;
use api\modules\v1\models\MemberInstallHistory;
class InstallHistoryController extends ApiController
{
    //安装历史
    public function actionInstall(){
        if (\Yii::$app->user->identity->quit_status == 1) {
            // 该用户已离职
            return $this->returnData('MEMBER_QUIT');
        }
        $historyModel = new MemberInstallHistory();
        $re = $historyModel->loadParams($this->params,$this->action->id);
        if($re){
            return $this->returnData($re);
        }
        $history = $historyModel->getMemberOrder();
        return $this->returnData('SUCCESS',$history);
    }
    //安装店铺详情
    public function actionShopDetail($id){
        if (\Yii::$app->user->identity->quit_status == 1) {
            // 该用户已离职
            return $this->returnData('MEMBER_QUIT');
        }
        $shopModel = new Shop();
        $res=$shopModel->loadParams($this->params,$this->action->id);
        if($res){
            return $this->returnData($res);
        }
        return $this->returnData('SUCCESS', $shopModel->getShopDetailById($id,$shopModel->type));

    }

}
