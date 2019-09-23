<?php

namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\Member;
use api\modules\v1\models\Shop;
use api\modules\v1\models\ShopAdvertImage;
use api\modules\v1\models\ShopHeadquarters;
use api\modules\v1\models\ShopHeadquartersList;
use common\libs\Redis;
use yii\base\Exception;

class MyShopController extends ApiController
{
    //我的店铺列表
    public function actionList(){
        return $this->returnData('SUCCESS', (new Shop())->getMyShop(true));
    }
    //获取我的店铺详情(总店)
    public function actionDetail($id){
        $headObj = new ShopHeadquarters();
        if($result = $headObj->loadParams($this->params,'headoffice')){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$headObj->getHeadAndList());

    }
    //获取某总店下的分店信息
    public function actionGetBranches(){
        $branchObj = new ShopHeadquartersList();
        if($result = $branchObj->loadParams($this->params,'get-branch')){
            return $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$branchObj->getMyBranches());
    }
    //为总店添加分店
    public function actionAddBranch(){
        $branchObj = new ShopHeadquartersList();
        if($result = $branchObj->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $re = $branchObj->shopListSave();
        $str = $re == true ? 'SUCCESS' : 'ERROR';
        return $this->returnData($str);
    }
    //单张保存图片
    public function actionSaveShopImage(){
        $imageObj = new ShopAdvertImage();
        if($result = $imageObj->loadParams($this->params,$this->action->id)){
            $this->returnData($result);
        }
        try{
            $re = $imageObj->save();
            if($re === 'IMAGE_MAX_THIRTY'){
                return $this->returnData($re);
            }
            $redis = Redis::getInstance(1);
            $redis->lpush('shop_advert_sha_check',json_encode(['shop_type'=>$imageObj->shop_type,'id'=>$imageObj->id,'image_url'=>$imageObj->image_url,'image_sha'=>$imageObj->image_sha]));
            return $this->returnData('SUCCESS');
        }catch (Exception $e){
            \Yii::error($e->getMessage(),'db');
            return $this->returnData('ERROR');
        }

    }
    //店铺上传图片展示
    public function actionShopImageShow($shop_id){
        $imageObj = new ShopAdvertImage();
        if($result = $imageObj->loadParams($this->params,$this->action->id)){
            $this->returnData($result);
        }
        return $this->returnData('SUCCESS',$imageObj->getMyImages($shop_id));
    }
    //店铺发布图片
    public function actionRelease($shop_id){
        $imageObj = new ShopAdvertImage();
        if($result = $imageObj->loadParams($this->params,$this->action->id)){
            $this->returnData($result);
        }
        return $this->returnData($imageObj->releaseImages($shop_id));
    }
    //店铺图片排序删除修改
    public function actionImageSort($shop_id){
        $imageObj = new ShopAdvertImage();
        if($result = $imageObj->loadParams($this->params,$this->action->id)){
            $this->returnData($result);
        }
        return $this->returnData($imageObj->sortMyIamges($shop_id));
    }
    //同意协议
    public function actionAgree(){
        $type = $this->params['type'];
        if(!$type){ return $this->returnData('ERROR'); }
        $shopObj = $type == 1 ? new Shop() : new ShopHeadquarters();
        if($result = $shopObj->loadParams($this->params,$this->action->id)){
            $this->returnData($result);
        }
        return $this->returnData($shopObj->agree());
    }
    //合作推广填写邀请人
    public function actionInviteCode(){
        $memberModel = new Member();
        if($result = $memberModel->loadParams($this->params, $this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData($memberModel->addInviteCode(),['mobile'=>$memberModel->mobile, 'name'=>$memberModel->name, 'avatar'=>$memberModel->avatar]);
    }
    //我和我的邀请人信息
    public function actionMyAndInviter(){
        return $this->returnData('SUCCESS',Member::getInivter());
    }

    public function actionQuery($mobile){
        return $this->returnData('SUCCESS',Member::getInivterName($mobile));
    }
}
