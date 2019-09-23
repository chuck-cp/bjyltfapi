<?php
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\MemberFunction;
use api\modules\v1\models\SystemFunction;

/**
 * 模块管理
 */
class FunctionController extends ApiController
{
    /**
     * 获取所有系统模块
     */
    public function actionIndex(){
        $functionModel = new SystemFunction();
        $systemFunction = $functionModel->getSystemFunction();
        $functionModel = new MemberFunction();
        $memberFunction = $functionModel->getMemberFunction();
        if(!empty($memberFunction) && !empty($systemFunction)){
            $memberFunctionId = explode(",",$memberFunction);
            $newSystemFunction = [];
            foreach($systemFunction as $key=>$function){
                if(!in_array($function['id'],$memberFunctionId)){
                    $newSystemFunction[] = $function;
                }else{
                    $newMemberFunction[$function['id']] = $function;
                }
            }
            foreach($memberFunctionId as $function_id){
                if(!isset($newMemberFunction[$function_id])){
                    continue;
                }
                $resultMemberFunction[] = $newMemberFunction[$function_id];
            }
        }else{
            $resultMemberFunction = [];
            $newSystemFunction = $systemFunction;
        }
        return $this->returnData('SUCCESS',[
            'member_function'=>$resultMemberFunction,
            'system_function'=>$newSystemFunction
        ]);
    }

    /**
     * 修改系统模块
     */
    public function actionUpdate(){
        $functionModel = new MemberFunction();
        if($result = $functionModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if($functionModel->updateContent()){
            return $this->returnData();
        }
        return $this->returnData('ERROR');
    }
}
