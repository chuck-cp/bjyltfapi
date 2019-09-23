<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/13
 * Time: 9:29
 */
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\BuildingPositionConfig;
use api\modules\v1\models\BuildingShopPark;

class ParkWapController extends ApiController {

    /**
     * 创建公园
     * @return array
     */
    public function actionCreatePark(){
        $parkModel = new BuildingShopPark();
        if($result = $parkModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        if($parkModel->createPark()){
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }

    /**
     * 获取我的公园列表
     * @return array
     */
    public function actionChooseParks(){
        return $this->returnData('SUCCESS', BuildingShopPark::getMyParks());
    }
    /**
     *公园申请->海报申请->安装位置场景（目前只有卫生间）
     */
    public function actionParkBillScenes(){
        return $this->returnData('SUCCESS', BuildingPositionConfig::getParkBillScenes());
    }

}