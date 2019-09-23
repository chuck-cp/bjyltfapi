<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/6/12
 * Time: 19:38
 */
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\BuildingCompany;
use api\modules\v1\models\BuildingPositionConfig;
use api\modules\v1\models\BuildingShopFloor;
use api\modules\v1\models\BuildingShopPark;
use api\modules\v1\models\BuildingShopPosition;
use api\modules\v1\models\BuildingShopPositionView;
use yii\base\Exception;

class BuildWapController extends ApiController{

    /**
     * 創建公司
     * @return array
     */
    public function actionCreateCompany(){
        $companyModel = new BuildingCompany();
        if($result = $companyModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        if(!$companyModel->judgeIsRepeatPost()){
            return $this->returnData('REPEAT_COMMIT');
        }
        if($companyModel->createCompany()){
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }

    /**
     * 獲得我的物业公司列表
     * @return array
     */
    public function actionChooseCompany(){
        $app = $this->params['app'] ?? 0;
        return $this->returnData('SUCCESS',BuildingCompany::getMyCompanyList($app));
    }

    /**
     * 创建楼宇
     * @return array
     */
    public function actionCreateBuild(){
        $buildModel = new BuildingShopFloor();
        if($result = $buildModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        if(!$buildModel->judgeIsRepeatPost()){
            return $this->returnData('REPEAT_COMMIT');
        }
        if($buildModel->createBuild()){
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }

    /**
     * 获取我的楼宇列表
     * @return array
     */
    public function actionChooseBuildings(){
        return $this->returnData('SUCCESS', BuildingShopFloor::getMyBuildings());
    }

    /**
     *楼宇申请->海报申请->安装位置场景
     */
    public function actionBuildBillScenes(){
        $configModel = new BuildingPositionConfig();
        if($result = $configModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS', $configModel->getBuildBillScenes());
    }

    /**
     *楼宇申请->LED申请->安装位置场景
     */
    public function actionBuildLedScenes(){
        return $this->returnData('SUCCESS', BuildingPositionConfig::getBuildLedScenes());
    }

    /**
     * 安装场景的下一页的具体表单（例如：大堂等候区、大堂以上等候区）提交页面数据
     * @return array
     */
    public function actionGetDetailSceneByConfigId(){
        $postionConfigModel = new BuildingPositionConfig();
        $positionModel = new BuildingShopPosition();
        if($result = $postionConfigModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        if($re = $positionModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($re);
        }
        $already = $positionModel->getAlredyInfo();
        $field = $this->params['screen_type'] == 1 ? 'led_examine_status' : 'poster_examine_status';
        $findModel = $this->params['shop_type'] == 2 ? BuildingShopPark::findOne($this->params['shop_id']) : BuildingShopFloor::findOne($this->params['shop_id']);
        $floorStatus = $findModel->getAttribute($field);
        return $this->returnData('SUCCESS', ['newData' => $postionConfigModel->getInputs(), 'oldData'=>$already, 'floor_status'=>$floorStatus]);
    }
    /**
     * 各种场景公用此方法
     * 楼宇->海报申请->安装位置场景->大堂等候区提交
     */
    public function actionBuildScene(){
        //位置
        $positionModel = new BuildingShopPosition();
        if($result = $positionModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        //位置详情
        $positionViewModel = new BuildingShopPositionView();
        if($result = $positionViewModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        $dbTrans = \Yii::$app->db->beginTransaction();
        try{
            if(!$position_id = $positionModel->buildBillHallWaitCreate()){
                throw new \yii\db\Exception('创建postion表数据失败');
            }
            $positionViewModel->shop_position_id = $position_id;
            if(!$positionViewModel->buildBillHallWaitCreate()){
                throw new \yii\db\Exception('创建postion_view表数据失败');
            }
            $dbTrans->commit();
            return $this->returnData('SUCCESS');
        }catch (Exception $e){
            \Yii::error($e->getMessage().' at line: '.$e->getLine());
            $dbTrans->rollBack();
            return $this->returnData('ERROR');
        }
    }

    /**
     *楼宇安装位置总提交
     */
    public function actionBuildDevicePost(){
        if(!$id = $this->params['id']){
            return $this->returnData('BUILD_SHOP_FLOOR_ID_EMPTY');
        }
        $re = (new BuildingShopFloor())->devicePost($id, $this->params['screen_type'], $this->params['shop_type']);
        if($re){
            if($re === 'RECORD_STATUS_ERROR'){
                return $this->returnData('RECORD_STATUS_ERROR');
            }
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');

    }

    /**
     * 获取楼宇地上和地下层数
     * @return array
     */
    public function actionGetBuildFloors(){
        $floorData = BuildingShopFloor::getFloors($this->params['id']);
        if(empty($floorData)){
            return $this->returnData('BUILD_FLOORS_EMPTY');
        }
        return $this->returnData('SUCCESS', $floorData);
    }






}