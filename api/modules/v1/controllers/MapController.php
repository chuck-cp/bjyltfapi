<?php
/**
 * Created by gaojianbo.
 * User: Administrator
 * Date: 2019/1/18
 * Time: 13:30
 */

namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\Shop;
use api\modules\v1\models\SystemAddress;

class MapController extends ApiController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
//        if(in_array($this->action->id,['area'])){
//            unset($behaviors['authenticator']);
//        }
        return $behaviors;
    }

    //根据条件获取地区店铺信息借口
    public function actionGetShopsByConditions(){
        $shopModel = new Shop();
        if($result = $shopModel->loadParams($this->params, 'map')){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$shopModel->getMaps());
    }
    //北上广深
    public function actionArea(){
        $addressModel = new SystemAddress();
        $addressModel->loadParams($this->params,$this->action->id);
        return $this->returnData('SUCCESS',$addressModel->getAreaOnly());
    }
    //获取某个店铺详情
    public function actionMapDetail(){
        $shopModel = new Shop();
        if($result = $shopModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$shopModel->getMapDetail());
    }
}