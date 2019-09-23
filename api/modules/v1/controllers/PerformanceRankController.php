<?php

namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\MemberShopApplyRank;
class PerformanceRankController extends ApiController
{
    //获取安装排行榜
    public function actionIndex($time){
        if(!in_array($time,[1,2,3,4,5])){
            return $this->returnData('ERROR');
        }
        return $this->returnData('SUCCESS',(new MemberShopApplyRank())->getRankByTime($time));
    }

}
