<?php

namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\Member;
use api\modules\v1\models\MemberTeam;
use api\modules\v1\models\Shop;
use api\modules\v1\models\ShopApply;
use api\modules\v1\models\Sign;
use api\modules\v1\models\SignBusiness;
use api\modules\v1\models\SignBusinessCount;
use api\modules\v1\models\SignImage;
use api\modules\v1\models\SignMaintain;
use api\modules\v1\models\SignMemberCount;
use api\modules\v1\models\SignTeam;
use api\modules\v1\models\SignTeamBusinessCount;
use api\modules\v1\models\SignTeamCountMemberDetail;
use api\modules\v1\models\SignTeamCountShopDetail;
use api\modules\v1\models\SignTeamMember;
use common\libs\Redis;
use yii\base\Exception;

class SignController extends ApiController
{
    //删除Mongo中多余的店铺数据 deleteRepeatMongo
    public function actionDeleteMongo(){
        $signModel = new SignBusiness();
        //var_dump($signModel->deleteRepeatMongo());exit;
        return $signModel->deleteRepeatMongo();
    }

    // 签到
    public function actionCreate() {
        $signModel = new Sign();
        if($result = $signModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if ($result = $signModel->loadSignData()){
            return $this->returnData($result);
        }
        $mongo_id = '';
        if ($signModel->team_type == 1) {
            // 业务签到
            $signDetailModel = new SignBusiness();
            if($result = $signDetailModel->loadParams($this->params,$this->action->id)){
                return $this->returnData($result);
            }
            // 获取此次签到店铺在MONGO中的ID,用于判断是否重复签到
            $mongo_id = $signDetailModel->setSignMongoId($signModel->shop_name,$signModel->shop_address);
        } else {
            // 维护签到
            $signDetailModel = new SignMaintain();
            if($result = $signDetailModel->loadParams($this->params,$this->action->id)){
                return $this->returnData($result);
            }
        }
        $signImageModel = new SignImage();
        if($result = $signImageModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $dbTrans = \Yii::$app->db->beginTransaction();
        try {
            if (!$signModel->createSign()) {
                throw new Exception("签到主表数据创建失败");
            }
            $signDetailModel->sign_id = $signModel->id;
            $re = $signDetailModel->save();
            if($re === 'START_END_TIME_ERROR'){
                return $this->returnData($re);
            }
            if (!$re) {
                throw new Exception("签到副表数据创建失败");
            }
            $signImageModel->sign_id = $signModel->id;
            if(!$signImageModel->saveImage()){
                throw new Exception("业务打卡图片表数据创建失败");
            }
            if (!$signDetailModel->afterCreateSign()) {
                throw new Exception("签到附表后续操作执行失败");
            }
            $dbTrans->commit();
            return $this->returnData('SUCCESS');
        } catch (\Exception $e) {
            if (isset($mongo_id)) {
                $signDetailModel->delteMongodbDocument(['_id'=>$mongo_id]);
            }
            $dbTrans->rollBack();
            \Yii::error($e->getMessage());
            return $this->returnData('SIGN_ERROR');
        }
    }

    //业务签到界面接口
    public function actionSign(){
        return $this->returnData('SUCCESS',SignTeamMember::getSignViewData());
    }

    //签到表单页面(业务)
    public function actionBusinessSign($team_id){
        return $this->returnData('SUCCESS', [
            'now_time' => date('Y').'年'.date('m').'月'.date('d').'日'.' '.date('H:i:s'),
            'team_name' => SignTeam::getField($team_id, 'team_name'),
        ]);
    }

    //签到表单页面(维护)
    public function actionMaintainSign($team_id,$shop_id){
        return $this->returnData('SUCCESS', [
            'now_time' => date('Y').'年'.date('m').'月'.date('d').'日'.' '.date('H:i:s'),
            'team_name' => SignTeam::getField($team_id, 'team_name'),
            'shopInfo' => (new Shop())->signGetInfo($shop_id),
        ]);
    }

    //维护签到时选择店铺列表
    public function actionShopList($jd,$wd){
        return $this->returnData('SUCCESS',(new SignMaintain())->getShops($jd,$wd));
    }

    //维护签到时选择店铺搜索
    public function actionShopSearch(){
        $signMaintainModel = new SignMaintain();
        if($result = $signMaintainModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS', $signMaintainModel->getSearchShops());
    }

    //团队管理列表展示
    public function actionTeam(){
        $teamModel = new SignTeam();
        //获取团队类型和用户身份
        $memberType = SignTeamMember::getMemberType(\Yii::$app->user->id);
        if($memberType == 3){
            //业务组
            $business = $teamModel->getTeamMembers();
            //维护组
            $maintain = $teamModel->getTeamMembers(2);
            return $this->returnData('SUCCESS',[
                'member_type' => $memberType,
                'items' => [
                    [
                        'title' => '业务组',
                        'team_number' => count($business),
                        'team' => $business,
                    ],
                    [
                        'title' => '维护组',
                        'team_number' => count($maintain),
                        'team' => $maintain,
                    ],
                ]
            ]);
        }else{
            $team_id = SignTeamMember::getTeamId(\Yii::$app->user->id);
            $teamType = SignTeam::getTeamType($team_id);
            $title = $teamType == 1 ? '业务组' : '维护组';
            return $this->returnData('SUCCESS',[
                'member_type' => $memberType,
                'items' => [
                    [
                        'title' => $title,
                        'team_number' => 1,
                        'team' => $teamModel->getTeamById($team_id),
                    ]
                ]
            ]);
        }

    }
    //团队详情展示
    public function actionTeamDetail($id){
        $teamModel = new SignTeam();
        return $this->returnData('SUCCESS',$teamModel->getTeamDetail($id));
    }
    //创建团队
    public function actionCreateTeam(){
        $teamModel = new SignTeam();
        if($result = $teamModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData($teamModel->teamSave(), $teamModel->id);

    }
    //修改团队名称
    public function actionUpdateTeamName(){
        $teamModel = new SignTeam();
        if($result = $teamModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
            if($teamModel->updateTeamName()){
                return $this->returnData('SUCCESS');
            }else{
                return $this->returnData('ERROR');
            }
    }
    //设置或取消负责人
    public function actionSetPrincipal(){
        $teamMemberModel = new SignTeamMember();
        if($result = $teamMemberModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if($teamMemberModel->setPrincipal()){
            return $this->returnData('SUCCESS');
        }else{
            return $this->returnData('ERROR');
        }
    }
    //可添加为团队成员的内部人员展示
    public function actionInsideMembers(){
        return $this->returnData('SUCCESS',Member::getNotAddSignTeamMembers());
    }
    //给团队添加成员
    public function actionAddMembers(){
        $teamMemberModel = new SignTeamMember();
        if($result = $teamMemberModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData($teamMemberModel->addMember());
    }
    //获取某团队可移除的成员
    public function actionWaitDeleteMembers($id){
        return $this->returnData('SUCCESS',SignTeamMember::getDeleteMembers($id));
    }
    //团队中移除成员
    public function actionDeleteMembers(){
        $teamMemberModel = new SignTeamMember();
        if($result = $teamMemberModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData($teamMemberModel->deleteMembers());
    }
    //团队足迹
    public function actionTeamFootmark(){
        //获取团队类型
        $model = new Sign();
        if($result = $model->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$model->getFootmark());
    }

    //足迹未签到人员列表
    public function actionNotSignMembers(){
        $signTeamCountModel = new SignMemberCount();
        if($result = $signTeamCountModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS', $signTeamCountModel->getNotSignMembers());
    }
    //足迹选择团队
    public function actionChooseTeam(){
        $model = new SignTeam();
        if($result = $model->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$model->getAllTeams());
    }
    //按团队查看签到数据
    public function actionTeamSignView(){
        $params = $this->params;
        switch ($params['team_id']){
            case 'business':
                $team_type = 1;
                break;
            case 'maintain':
                $team_type = 2;
                break;
            default:
                $team_type = SignTeam::getTeamType($params['team_id']);
        }
        $obj = new SignBusiness();
        if($result = $obj->loadParams($params, $this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$obj->getTeamData($team_type,$params['team_id']));
    }
    //团队足迹某团队详情(全部)
    public function actionTeamAllData(){
        $obj = new Sign();
        if($result = $obj->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        $re = $obj->getTeamAllData();
        if($re === 'CAN_NOT_LOOK_OTHER'){
            return $this->returnData($re);
        }
        return $this->returnData('SUCCESS',$re);
    }
    //个人签到(列表)
    public function actionSingleSignView(){
        $signModel = new Sign();
        if($result = $signModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$signModel->getSignData());
    }
    //个人单次签到详情页面
    public function actionSingleDetail(){
        $signModel =new Sign();
        if($result = $signModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$signModel->singleDetail());
    }
    //签到数据
    public function actionSignDatas(){
        $dataModel = new SignTeamBusinessCount();
        if($result = $dataModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$dataModel->getDatas());
    }
    //签到数据查看各种人数详情(人员签到、店铺签到)
    public function actionGetView(){
        $model = new Sign();
        if($result = $model->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS', $model->getMembersView());
    }
    //签到数据查看各种人数详情(查看重复签到店铺) yl_sign_team_count_shop_detail
    public function actionRepeatShops(){
        $model = new SignTeamCountShopDetail();
        if($result = $model->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$model->getRepeatShops());

    }
    //维护内容
    public function actionMaintainContent(){
        return $this->returnData('SUCCESS',SignMaintain::getMainTainContent());
    }
    //店铺维护历史
    public function actionMaintainHistory(){
        $maintainModel = new SignMaintain();
        if($result = $maintainModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$maintainModel->getMaintainHistory());
    }

    //服务评价详情
    public function actionCommentDetail($id){
        return $this->returnData('SUCCESS',SignMaintain::getCommemt($id));
    }
    //提交评价
    public function actionEvaluate(){
        $maintainModel = new SignMaintain();
        if($result = $maintainModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData($maintainModel->evaluate());
    }
    //团队内成员之间互相查看按钮开关
    public function actionOnOff(){
        $signTeamModel = new SignTeam();
        if($result = $signTeamModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData($signTeamModel->turnOnOff());
    }
    //默认评价时间
    public function actionCloseEvaluate(){
        $mainModel = new SignMaintain();
        if($result = $mainModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData($mainModel->updateEvaluateTime());
    }

}
