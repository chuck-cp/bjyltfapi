<?php
namespace api\modules\v1\controllers;

use api\modules\v1\models\BuildingCompany;
use api\modules\v1\models\BuildingPositionConfig;
use api\modules\v1\models\BuildingShop;
use api\modules\v1\models\BuildingShopPosition;
use api\modules\v1\models\BuildingShopPositionDifferent;
use api\modules\v1\models\BuildingShopPositionView;
use api\modules\v1\models\BuildingShopScreen;
use api\modules\v1\models\LogExamine;
use api\modules\v1\models\MemberInstallHistory;
use api\modules\v1\models\SystemDeviceFrame;
use MongoDB\Driver\Exception\AuthenticationException;
use Yii;
use yii\web\MethodNotAllowedHttpException;

class BuildingController extends \api\core\ApiController
{
    /*
     * 获取安装业务数据
     * */
    public function actionIndex()
    {
        $buildingShopModel = new BuildingShop(0,[]);
        return $this->returnData('SUCCESS',$buildingShopModel->getShopList());
    }

    /*
     * 获取场景店铺信息
     * @param shop_id int 场景店铺ID
     * @param shop_type int 场景店铺类型
     * */
    public function actionShopDetail($shop_id,$shop_type)
    {
        $buildingShopModel = new BuildingShop($shop_type,['id' => $shop_id]);
        if (!$buildingShopModel->isAuth()) {
            throw new AuthenticationException();
        }
        return $this->returnData('SUCCESS',$buildingShopModel->getShopDetail());
    }

    /*
     * 获取公司信息
     * @param id int 公司ID
     * */
    public function actionCompanyDetail($company_id)
    {
        $companyModel = new BuildingCompany();
        return $this->returnData('SUCCESS',$companyModel->getCompanyData(['id' => $company_id, 'member_id' => \Yii::$app->user->id],'apply_name,apply_mobile,company_name,area_id,address,street,area,city,province,registration_mark,description,agreement_name,identity_card_front,identity_card_back,business_licence,other_image'));
    }

    /* 安装业务 */

    /*
     * 申请信息列表
     * @param shop_id int 场景店铺ID
     * @param shop_type int 场景店铺类型
     * @param screen_type int 设备类型(1、LED 2、海报)
     * */
    public function actionViewList($shop_id,$shop_type,$screen_type)
    {
        $positionModel = new BuildingShopPosition();
        $buildingShopModel = new BuildingShop($shop_type,['id' => $shop_id]);
        if (!$buildingShopModel->isAuth()) {
            throw new AuthenticationException();
        }
        return $this->returnData('SUCCESS',[
            'position_list' => $positionModel->getPositionList($shop_id,$shop_type,$screen_type),
            'total_screen_number' => $screen_type == BuildingShop::SCREEN_LED ? $buildingShopModel->led_total_screen_number : $buildingShopModel->poster_total_screen_number,
            'company_id' => $buildingShopModel->company_id,
            'examine_status' => $buildingShopModel->getExamineStatus($screen_type),
            'examine_result' => LogExamine::getExamineResult($shop_id,$buildingShopModel->getExamineKey($screen_type)),
        ]);
    }

    /*
     * 申请信息详情
     * @param position_id int 位置ID
     * */
    public function actionViewDetail($position_id)
    {
        $positionModel = BuildingShopPosition::findOne(['id' => $position_id]);
        //p($positionModel->position_config_id);exit;
        if (!$positionModel->isAuth()) {
            throw new AuthenticationException();
        }
        return $this->returnData('SUCCESS', $positionModel->getPositionDetail());
    }

    /*
     * 修改安装位置信息
     * @param position_id int 位置ID
     * */
    public function actionUpdatePosition($position_id)
    {
        $positionModel = BuildingShopPosition::findOne(['id' => $position_id, 'member_id' => \Yii::$app->user->id]);
        if ($result = $positionModel->loadParams($this->params,'update')) {
            return $this->returnData($result);
        }
        if ($positionModel->updatePosition()) {
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }

    /*
     * 修改公司信息
     * @param id int 公司ID
     * */
    public function actionUpdateCompany($company_id)
    {
        $companyModel = BuildingCompany::findOne(['id' => $company_id, 'member_id' => \Yii::$app->user->id]);
        if ($result = $companyModel->loadParams($this->params,'update')) {
            return $this->returnData($result);
        }
        if ($companyModel->save()) {
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }

    /*
     * 修改场景店铺信息
     * @param shop_id int 场景店铺ID
     * @param shop_type int 店铺类型
     * */
    public function actionUpdateShop($shop_id,$shop_type)
    {
        $shopModel = (new BuildingShop($shop_type,['id' => $shop_id, 'member_id' => \Yii::$app->user->id],'object'))->getShopModel();
        if ($result = $shopModel->loadParams($this->params,'update')) {
            return $this->returnData($result);
        }
        if ($shopModel->save()) {
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }

    /*
     * 提交申请信息
     * @param shop_id int 场景店铺ID
     * @param shop_type int 店铺类型
     * @param screen_type int 设备类型
     * */
    public function actionSubmitExamine($shop_id,$shop_type,$screen_type)
    {
        $shopModel = (new BuildingShop($shop_type,['id' => $shop_id, 'member_id' => \Yii::$app->user->id],'one','id'));
        if ($shopModel->submitExamine($screen_type)) {
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }

    /* 业务历史 */

    /*
     * 业务历史和安装历史
     * */
    public function actionHistory()
    {
        $historyModel = new MemberInstallHistory();
        return $this->returnData('SUCCESS',$historyModel->getInstallHistory());
    }

    /* 设备安装 */
    /*
     * 获取位置和设备信息
     * */
    public function actionInstallViewDetail($position_id,$floor_number = 0,$screen_status = null)
    {
        $differentModel = new BuildingShopPositionDifferent();
        return $this->returnData('SUCCESS',$differentModel->getInstallViewDetailData($position_id,$floor_number,$screen_status));
    }

    /*
     * 提交设备信息
     * */
    public function actionInstallUpdateScreen($position_id)
    {
        $positionModel = BuildingShopPosition::findOne($position_id);
        if ($result = $positionModel->loadParams($this->params,$this->action->id)) {
            return $this->returnData($result);
        }
        if (!$positionModel->isAuth()) {
            throw new AuthenticationException();
        }
        $differentModel = new BuildingShopPositionDifferent();
        if ($result = $differentModel->loadParams($this->params,'update')) {
            return $this->returnData($result);
        }
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            list($status,$message) = $differentModel->updateScreenData($positionModel['shop_id'],$position_id);
            $positionModel->save();
            if ($status == 'SUCCESS') {
                $dbTrans->commit();
            } else {
                $dbTrans->rollBack();
            }
        } catch (\Exception $e) {
            $dbTrans->rollBack();
            return $this->returnData('ERROR');
        }
        return $this->returnData($status,$message);
    }

    /*
     * 获取未激活的设备编号
     * */
    public function actionNoActivationScreen($position_different_id)
    {
        $screenModel = new BuildingShopScreen();
        return $this->returnData('SUCCESS',[
            'screen_list' => $screenModel->getNoActivationScreen($position_different_id)
        ]);
    }

    /*
     * 提交安装信息审核
     * @param shop_id int 场景店铺ID
     * @param shop_type int 店铺类型
     * @param screen_type int 设备类型
     * */
    public function actionInstallSubmitExamine($shop_id,$shop_type,$screen_type)
    {
        $shopModel = (new BuildingShop($shop_type,['id' => $shop_id],'one','id'));
        if ($shopModel->installSubmitExamine($screen_type)) {
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }

    /*
     * 检查设备编号是否可以安装
     * */
    public function actionCheckScreenNumber($screen_number)
    {
        if (BuildingShopScreen::verifyScreenNumber($screen_number) || BuildingShopScreen::isInstall($screen_number)) {
            return $this->returnData('ERROR','设备未出库或已被激活');
        }
        return $this->returnData('SUCCESS');
    }
}