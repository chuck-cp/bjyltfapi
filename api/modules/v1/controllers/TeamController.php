<?php
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\Member;
use api\modules\v1\models\LogAccount;
use api\modules\v1\models\MemberAccount;
use api\modules\v1\models\MemberAccountCount;
use api\modules\v1\models\MemberAccountMessage;
use api\modules\v1\models\MemberInfo;
use api\modules\v1\models\MemberPassword;
use api\modules\v1\models\MemberTeam;
use api\modules\v1\models\MemberTeamList;
use api\modules\v1\models\SystemAddress;
use common\libs\ToolsClass;

/**
 * 团队
 */
class TeamController extends ApiController
{
    /*
     * 创建团队
     * */
    public function actionCreate(){
        $teamModel=new MemberTeam();
        if($result = $teamModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $team_id=$teamModel->createTeam();
        if($team_id=='TEAM_ALREADY_EXISTED'){
            return $this->returnData("TEAM_ALREADY_EXISTED");
        }
        if($team_id=='TEAM_NAME_ALREADY_EXISTED'){
            return $this->returnData("TEAM_NAME_ALREADY_EXISTED");
        }
        if($team_id){
            return $this->returnData("SUCCESS",array('team_id'=>(string)$team_id));
        }else{
            return $this->returnData("ERROR");
        }

    }

    /*
     * 团队信息
     * */
    public function actionView($team_id){
        $teamModel=new MemberTeam();
        if($result = $teamModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$teamModel->getTeam($team_id));
    }

    /*
     * 修改团队信息
     * */
    public function actionUpdate($team_id){
        $teamModel=MemberTeam::find()->where(['id'=>$team_id])->select('id,team_name,live_area_id,live_area_name,live_address,company_name,company_area_name,company_area_id,company_address')->one();
        if($result = $teamModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData($teamModel->updateTeam());
    }

    /*
    * 验证团队名称是否存在
    * */
    public function actionIsteamname($team_name){
        $teamModel=new MemberTeam();
        if($result = $teamModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData($teamModel->isTeamName($team_name));
    }

    /*
  * 获取团队成员列表
  * */
    public function actionTeamlsit($team_id){
        $teamlistModel=new MemberTeamList();
        if($result = $teamlistModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$teamlistModel->getTeamlist($team_id));
    }


    /*
     * 退出团队
     * */
    public function actionExit($team_id){
        $teamModel = new MemberTeam();
        return $this->returnData($teamModel->exitTeam($team_id));
    }

    /*
     * 解散团队
     * */
    public function actionDismiss(){
        $teamModel = new MemberTeam();
        if($result = $teamModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData( $teamModel->dissolutionTeam());
    }

    /*
     * 加入团队
     * */
    public function actionJoin(){
        $teamModel = new MemberTeam();
        if($result = $teamModel->loadParams($this->params,'join')){
            return $this->returnData($result);
        }
        return $this->returnData($teamModel->joinTeam());
    }

    /*
     * 指派店铺
     * */
    public function actionCancel($team_id,$shop_id){
        $teamModel = new MemberTeam();
        return $this->returnData($teamModel->cancelAssignShop($team_id,$shop_id));
    }

    /*
     * 指派店铺
     * */
    public function actionAssign($team_id,$shop_id){
        $teamModel = new MemberTeam();
        if($result = $teamModel->loadParams($this->params,'assign')){
            return $this->returnData($result);
        }
        return $this->returnData($teamModel->assignShop($team_id,$shop_id));
    }

    /*
     * 店铺列表
     * */
    public function actionShop($team_id){
        $teamModel = new MemberTeam();
        if($result = $teamModel->loadParams($this->params,'shop')){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$teamModel->shopList($team_id));
    }

}
