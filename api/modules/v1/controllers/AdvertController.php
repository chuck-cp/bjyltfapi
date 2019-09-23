<?php
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\AdvertPosition;
use api\modules\v1\models\AdvertPrice;
use api\modules\v1\models\MemberFunction;
use api\modules\v1\models\OrderAreaCache;
use api\modules\v1\models\Shop;
use api\modules\v1\models\SystemAddress;
use api\modules\v1\models\SystemBanner;
use api\modules\v1\models\SystemConfig;
use api\modules\v1\models\SystemNotice;
use common\libs\ToolsClass;

/**
 * 首页
 */
class AdvertController extends ApiController
{

    /*
     * 获取广告购买页数据
     * */
    public function actionIndex($type = 0){
        $positionModel = new AdvertPosition();
        if($type == 0){
            $systemBanner = new SystemBanner();
            $result = [
                'banner'=>$systemBanner->getIndexBanner(2),
                'advert_position'=>$positionModel->getAdvertPosition(),
            ];
        }else{
            $result = [
                'advert_position'=>$positionModel->getAdvertPosition(),
            ];
        }
        return $this->returnData('SUCCESS',$result);
    }

    /*
     * 获取广告位信息
     * */
    public function actionView($id){
        $positionModel = new AdvertPosition();
        return $this->returnData('SUCCESS',$positionModel->getAdvertPositionById($id));
    }

    /*
     * 查询广告价格
     * */
    public function actionSelect(){
        $positionModel = new AdvertPrice();
        if($result = $positionModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        list($resultStatus,$resultPrice,$position,$rate,$screenNumber) = $positionModel->selectAdvertPrice();
        if($rate > 1){
            $totalTime = ToolsClass::minuteCoverSecond($positionModel->time);
        }else{
            $totalTime = $positionModel->time;
        }
        if($resultStatus != 'SUCCESS'){
            return $this->returnData($resultStatus);
        }
        //配置中心设置的大于多少才能选择定金支付 system_order_price
        $system_order_price = SystemConfig::getConfig('system_order_price');
        $result = ['order_maximum_discount' => SystemConfig::getConfig('order_maximum_discount',10),'screen_number' => (string)$screenNumber, 'total_time'=>$totalTime,'order_price'=>(string)sprintf("%.2f",$resultPrice),'prepayment_ratio'=>SystemConfig::getConfig('prepayment_ratio'),'area_name'=>OrderAreaCache::getAreaAndDefaultAreaName(),'system_order_price'=>$system_order_price];
        return $this->returnData($resultStatus,array_merge($result,$position));
    }

    /**
     *
     */
    public function actionAdvert(){
        $model = new AdvertPosition();
        return $this->returnData('SUCCESS',$model->getAllAdvert());
    }
}
