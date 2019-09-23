<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/30
 * Time: 9:20
 */

namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\ShopScreenAdvertMaintain;

class ScreenAdvertMaintainController extends ApiController {

    /**
     * 获取维护列表
     * @return array
     */
    public function actionGetMaintainInfo(){
        $maintainModel =new ShopScreenAdvertMaintain();
        if($result = $maintainModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS', $maintainModel->getMainInfoByID());
    }

    /**
     * 文档中获取详情
     * @return array
     */
    public function actionGetMongoInfo(){
        $maintainModel =new ShopScreenAdvertMaintain();
        if($result = $maintainModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS', $maintainModel->getMongoByID());
    }

    /**
     * 临时保存进度
     * @return array
     */
    public function actionSaveTempInfo(){
        $maintainModel =new ShopScreenAdvertMaintain();
        if($result = $maintainModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if($maintainModel->saveTempInfo()){
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }

    /**
     * 维护确认提交
     * @return array
     */
    public function actionSaveInfo(){
        $maintainModel =new ShopScreenAdvertMaintain();
        if($result = $maintainModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        if($re = $maintainModel->saveInfo()){
            if(is_array($re)){
                return $this->returnData('SUCCESS',$re);
            }
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }
}