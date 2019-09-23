<?php
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\Feedback;
use api\modules\v1\models\MemberInfo;
use api\modules\v1\models\MemberFunction;
use api\modules\v1\models\MemberTeam;
use api\modules\v1\models\MemberTeamList;
use api\modules\v1\models\Shop;
use api\modules\v1\models\SignTeamMember;
use api\modules\v1\models\SystemBanner;
use api\modules\v1\models\SystemNotice;
/**
 * 首页
 */
class IndexController extends ApiController
{
    /*
     * 获取店铺首页数据
     * */
    public function actionIndex(){
        $systemBanner = new SystemBanner();
        $systemNotice = new SystemNotice();
        $memberFunction = new MemberFunction();
        $shopModel = new Shop();
        $team_id = MemberTeam::getTeamId();
        $result = [
            'create_team_status'=> $team_id,
            'join_team_status'=> $team_id == 0 ? MemberTeamList::getJoinStatus() : "0",
            'member_examine_status'=> MemberInfo::getMemberExamineStatusByIndex(),
            'sign_team_type'=>SignTeamMember::getTeamType(),
            'banner'=>$systemBanner->getIndexBanner(),
            'notice'=>$systemNotice->getIndexNotice(),
            'function'=>$memberFunction->getIndexFunction(),
        ];
        return $this->returnData('SUCCESS',$result);
    }
}
