<?php

namespace api\modules\v1\controllers;

use api\core\ApiController;
use api\modules\v1\models\MemberRewardMember;

class RewardController extends ApiController
{
    public function actionUpdateName(){
        $obj = new MemberRewardMember();
        if($result = $obj->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData($obj->updateName());
    }
    //扫码订单奖励金(上部)
    public function actionRewardList(){
        $obj = new MemberRewardMember();
        if($result = $obj->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$obj->getRewardList());
    }
    //某一订单列表
    public function actionOrderList(){
        $obj = new MemberRewardMember();
        if($result = $obj->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$obj->getOrderList());
    }
    //获取全部奖励金明细
    public function actionAllOrdersList(){
        $obj = new MemberRewardMember();
        if($result = $obj->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$obj->getAllOrder());
    }
    //自营定或租赁店搜索订单
    public function actionShopSearch(){
        //print_r($this->params);exit;
        $obj = new MemberRewardMember();
        if($result = $obj->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$obj->shopSearch());
    }

}
