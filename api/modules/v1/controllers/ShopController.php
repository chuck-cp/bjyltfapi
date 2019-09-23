<?php
namespace api\modules\v1\controllers;
use api\core\ApiController;
use api\modules\v1\models\LogExamine;
use api\modules\v1\models\Member;
use api\modules\v1\models\MemberAreaCount;
use api\modules\v1\models\MemberFunction;
use api\modules\v1\models\MemberRewardAcount;
use api\modules\v1\models\MemberShopArea;
use api\modules\v1\models\MemberShopCount;
use api\modules\v1\models\MemberShopDate;
use api\modules\v1\models\Screen;
use api\modules\v1\models\Shop;
use api\modules\v1\models\ShopAdvertImage;
use api\modules\v1\models\ShopApply;
use api\modules\v1\models\ShopImage;
use api\modules\v1\models\SignMaintain;
use api\modules\v1\models\SystemAddress;
use api\modules\v1\models\SystemBanner;
use api\modules\v1\models\SystemConfig;
use api\modules\v1\models\SystemNotice;
use api\modules\v1\models\ActivityDetail;
use common\libs\QueueClass;
use common\libs\RedisClass;
use common\libs\ToolsClass;
use Yii;
use yii\db\Exception;

/**
 * 店铺
 */
class ShopController extends ApiController
{

//    public function behaviors()
//    {
//        //使用验证权限过滤器
//        $behaviors = parent::behaviors();
//        if($this->action->id == "create" or $this->action->id == "record" or $this->action->id == "recordinfo"){
//            unset($behaviors['authenticator']);
//        }
//        return $behaviors;
//    }

    /**
     * 获取店铺地区
     */
    public function actionArea(){
        $areaModel = new MemberShopArea();
        $areaModel->loadParams($this->params,$this->action->id);
        return $this->returnData('SUCCESS',$areaModel->getArea());
    }

    /*
     * 我的店铺接口
     * */
    public function actionMyShop(){
        $shopModel = new Shop();
        $shop_id = (int)Yii::$app->request->get('shop_id');
        $resultShop['item'] = $shopModel->getMemberShopByKeeper($shop_id);
        if(!empty($resultShop['item'])){
            $screenModel = new Screen();
            $defaultShop = $resultShop['item'][0];
            $shopInfo = $shopModel->getShopInfo($defaultShop['id']);
            $shopInfo['name'] = $defaultShop['name'];
            $shopInfo['id'] = $defaultShop['id'];
            $shopInfo['screens'] = $screenModel->getShopScreens($defaultShop['id']);
            if($shopInfo['panorama_image']){
                $resultShop['shop_images'][] = ['image_url'=>$shopInfo['panorama_image']];
            }
            if($shopInfo['shop_image']){
                $resultShop['shop_images'][] = ['image_url'=>$shopInfo['shop_image']];
            }
            $shopInfo['mobile'] = $shopInfo['member_mobile'];
            if(!isset($shopInfo['member_name'])){
                $shopInfo['member_name'] = $shopInfo['member_mobile'];
            }
            unset($shopInfo['member_mobile']);
            unset($shopInfo['panorama_image']);
            $memberModel = new Member();
            if($shopInfo['status'] == 5){
                if(empty($shopInfo['admin_member_id'])){
                    $shopInfo['member_name'] = "玉龙传媒";
                    $shopInfo['mobile'] = SystemConfig::getConfig("service_phone");
                }else{
                    $memberModel = $memberModel->getMemberByShop($shopInfo['admin_member_id']);
                    $shopInfo['member_name'] = $memberModel['name'];
                    $shopInfo['mobile'] = $memberModel['mobile'];
                }
            }elseif($shopInfo['member_id']){
                $memberModel = $memberModel->getMemberByShop($shopInfo['member_id']);
                $shopInfo['member_name'] = $memberModel['name'];
                $shopInfo['mobile'] = $memberModel['mobile'];
            }
            $shopInfo['image_num'] = ShopAdvertImage::find()->where(['shop_id'=>$defaultShop['id'],'shop_type'=>1])->count().'张';
            $shopInfo['loop_time'] = '15分钟';
            $shopInfo['maintain'] = SignMaintain::getNewComment($shop_id);
            $resultShop['shop'] = $shopInfo;
            //$result['reward_agreementg'] = Yii::$app->params['baseWapUrl'].'shop';
        }
        return $this->returnData('SUCCESS',$resultShop);
    }
    //店主是否同意奖励金协议
    public function actionAgreeReward(){
        $shopObj = new MemberRewardAcount();
        if($result = $shopObj->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        return $this->returnData($shopObj->updateRewardAgreed());

    }
    /**
     * 获取我的业务、我的屏幕
     */
    public function actionView($shop_id){
        $shopModel = new Shop();
        $shopModel->loadParams($this->params,$this->action->id);
        if(!$shopModel->verifyAuth($shop_id)){
            return $this->returnData('SHOP_EXISTENT');
        }
        $shopInfo = $shopModel->getShopInfo($shop_id);
        if(empty($shopInfo)){
            return $this->returnData('SHOP_EXISTENT');
        }
        if($shopInfo['panorama_image']){
            $result['shop_images'][] = ['image_url'=>$shopInfo['panorama_image']];
        }
        if($shopInfo['shop_image']){
            $result['shop_images'][] = ['image_url'=>$shopInfo['shop_image']];
        }
        $shopInfo['mobile'] = $shopInfo['member_mobile'];
        unset($shopInfo['member_mobile']);
        unset($shopInfo['shop_image']);
        unset($shopInfo['panorama_image']);
        $memberModel = new Member();
        if($shopModel->shop_type){
            $member_id = $shopInfo['admin_member_id'];
            $screenModel = new Screen();
            $result['screens'] = $screenModel->getShopScreens($shop_id);
        }else{
            $member_id = $shopInfo['member_id'];
        }
        if($adminModel = $memberModel->getMemberByShop($member_id)){
            $shopInfo['member_name'] = $adminModel['name'];
            $shopInfo['mobile'] = $adminModel['mobile'];
        }
        $result['shop'] = $shopInfo;
        return $this->returnData('SUCCESS',$result);
    }

    /*
     * 创建店铺
     * */
    public function actionCreate(){
        $shopModel = new Shop();
        if($result = $shopModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $applyModel = new ShopApply();
        if($result = $applyModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $shopModel->shop_member_id = (int)$applyModel->shop_member_id;
        $dbTrans = \Yii::$app->db->beginTransaction();
        try{
            $shopModel->checkRepeatMobileAndName('create',$applyModel->apply_mobile,$applyModel->company_name);
            if(!$shopModel->createShop()){
                throw new Exception("店铺表数据创建失败");
            }
            $applyModel->id = $shopModel->id;
            if(!$applyModel->save()){
                throw new Exception("店铺申请表数据创建失败");
            }
            $dbTrans->commit();
            return $this->returnData('SUCCESS');
        }catch (Exception $e){
            \Yii::error($e->getMessage(),'db');
            $dbTrans->rollBack();
            return $this->returnData('ERROR');
        }
    }
    /*
     * 内部人员创建店铺
     */
    /*
     * 创建店铺
     * */
    public function actionInnerCreate(){
        $shopModel = new Shop();
        if($result = $shopModel->loadParams($this->params,'create')){
            return $this->returnData($result);
        }
        $applyModel = new ShopApply();
        if($result = $applyModel->loadParams($this->params,'create')){
            return $this->returnData($result);
        }
        if(!$applyModel->checkAuthorizeImage($shopModel->shop_operate_type)){
            return $this->returnData('AUTHORIZE_IMAGE_EMPTY');
        }
        if($shopModel->shop_operate_type == 3 && !$applyModel->checkBranchShopInfo($shopModel->headquarters_id)){
            //分店安装验证数据是否正确
            return $this->returnData('ERROR');
        }
        $shopModel->shop_member_id = (int)$applyModel->shop_member_id;
        $dbTrans = \Yii::$app->db->beginTransaction();
        try{
            $shopModel->checkRepeatMobileAndName('create',$applyModel->apply_mobile,$applyModel->company_name);
            if(!$shopModel->createShop(true)){
                throw new Exception("店铺表数据创建失败");
            }
            $applyModel->id = $shopModel->id;
            if(!$applyModel->tests()){
                throw new Exception("店铺申请表数据创建失败");
            }
            if($shopModel->activity_detail_id){
                ActivityDetail::updateAll(['is_apply'=>1,'status'=>0], ['id'=>$shopModel->activity_detail_id,'status'=>[0,2]]);
            }
            $dbTrans->commit();
            return $this->returnData('SUCCESS',['shop_id'=>$shopModel->id,'activity_detail_id'=>$shopModel->activity_detail_id]);
        }catch (Exception $e){
            //var_dump($e->getMessage());exit;
            \Yii::error($e->getMessage(),'db');
            $dbTrans->rollBack();
            return $this->returnData('ERROR');
        }
    }
    /*
     * 修改店铺
     */
    public function actionModify($shop_id){
        $wechat_id = (int)Yii::$app->request->get('wechat_id');
        if($wechat_id){
            $keyName = 'wx_member_id';
        }else{
            $keyName = 'member_id';
        }
        $shopModel = Shop::findOne(['id'=>$shop_id,$keyName=>Yii::$app->user->id,'status'=>1]);
        if(!$shopModel){
            return $this->returnData('SHOP_STATUS_NOT_REJECT');
        }
        //如果是连锁店铺
        if($shopModel['shop_operate_type'] == 3){
            $action['shop'] = 'shop_modify';
            $action['shop_apply'] = 'shop_apply_modify';
        }else{
            $action['shop'] = 'modify';
            $action['shop_apply'] = 'modify';
        }
        if($result = $shopModel->loadParams($this->params,$action['shop'])){
            return $this->returnData($result);
        }
        $applyModel = ShopApply::findOne($shop_id);

        if($result = $applyModel->loadParams($this->params,$action['shop_apply'])){
            return $this->returnData($result);
        }
        if(!$applyModel->checkAuthorizeImage($shopModel->shop_operate_type)){
            return $this->returnData('AUTHORIZE_IMAGE_EMPTY');
        }
        //$shopModel->shop_member_id = (int)$applyModel->shop_member_id;
        $dbTrans = \Yii::$app->db->beginTransaction();
        try{
            $shopModel->checkRepeatMobileAndName('modify',$applyModel->apply_mobile,$applyModel->company_name);
            if(!$shopModel->modifyShop($shop_id)){
                throw new Exception('id为 '.$shop_id.' 的店铺修改失败！');
            }
            if(!$applyModel->modifyShop($shop_id)){
                throw new Exception('id为 '.$shop_id.' 的店铺申请信息修改失败！');
            }
            $dbTrans->commit();
            return $this->returnData('SUCCESS',['shop_id'=>$shop_id]);
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            $dbTrans->rollBack();
            return $this->returnData('ERROR');
        }
    }
    /**
     * 获取我的店铺列表(屏幕管理和业务查询)
     */
    public function actionIndex(){
        $shopModel = new Shop();
        $shopModel->loadParams($this->params,$this->action->id);
        $result['shop_list'] = $shopModel->getMemberShop();
        if($shopModel->shop_type){
            $result['member_type'] = Member::getMemberType();
        }
        $page = (int)\Yii::$app->request->get('page');
        if($page < 2){
            $shop_type = $shopModel->shop_type == 1 ? 3 : 1;
            $result['date_list'] = MemberShopDate::getShopDate($shop_type);
        }
        return $this->returnData('SUCCESS',$result);
    }

    /**
     * 获取微信申请记录
     */
    public function actionRecord(){
        $shopModel = new Shop();
        if($result = $shopModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
       // $shopModel->loadParams($this->params,$this->action->id);
        return $this->returnData('SUCCESS',$shopModel->getRecord());
    }
    /**
     * 获取微信申请记录详情
     */
    public function actionRecordinfo(){
        $shopModel = new Shop();
        if($result = $shopModel->loadParams($this->params,$this->action->id)){
            return $this->returnData($result);
        }
        $shopData=$shopModel->getRecordinfo($shopModel->id);
        if(empty($shopData)){
            return $this->returnData('ERROR');
        }
        return $this->returnData('SUCCESS',$shopData);
    }

    /**
     * 获取我的安装业务
     */
    public function actionLower(){
        $shopModel = new Shop();
        $shopModel->loadParams($this->params,$this->action->id);
        $result['shop_list'] = $shopModel->getMemberLowerShop();
        $page = (int)\Yii::$app->request->get('page');
        if($page < 2){
            $result['date_list'] = MemberShopDate::getShopDate(2);
        }
        return $this->returnData('SUCCESS',$result);
    }

    /*
     * 签约店铺
     * */
    public function actionContract() {
        $detailModel = new ActivityDetail();
        $result = $detailModel->getContractShop();
        return $this->returnData('SUCCESS',$result);
    }

    /*
     * 签约失败
     * */
    public function actionContractFailed($id) {
        $detailModel = new ActivityDetail();
        if ($result = $detailModel->loadParams($this->params,'update')) {
            return $this->returnData($result);
        }
        if ($detailModel->contractFailed($id)) {
            return $this->returnData('SUCCESS');
        }
        return $this->returnData('ERROR');
    }
    //待申请店铺详情
    public function actionActiveDetail($id){
        return $this->returnData('SUCCESS',ActivityDetail::getShopInfo($id));
    }

    /**
     * @return array
     */
    public function actionGetApplyNames()
    {
        $shopModel = new Shop();
        return $this->returnData('SUCCESS',$shopModel->getAppliers());

    }
}
