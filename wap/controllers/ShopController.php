<?php
namespace wap\controllers;
use common\libs\PublicClass;
use common\libs\ToolsClass;
use wap\core\WapController;
use wap\models\Member;
use wap\models\MemberEquipment;
use wap\models\MemberWeixin;
use wap\models\Shop;
use wap\models\ShopApply;
use wap\models\SystemVersion;
use yii\web\Controller;
use Yii;
use yii\web\NotFoundHttpException;

/**
 * 下载页面
 */
class ShopController extends WapController
{
    public function beforeAction($action){
        if(parent::beforeAction($action)){
            if(!$this->authentication()){
                throw new NotFoundHttpException('TOKEN ERROR');
            }
            return true;
        }
        return false;
    }

    public function actionChooseShopType(){
        $token = \Yii::$app->request->get('token');
        $dev = \Yii::$app->request->get('dev');
        //活动
        $active_id = \Yii::$app->request->get('active_id') ?? null;
        return $this->render('choose-shop-type',[
            'token'=>$token,
            'dev'=>$dev,
            'active_id'=>$active_id,
        ]);
    }

    public function actionCreate(){
        $type = \Yii::$app->request->get('type');
        if(empty($type)){
            $type = 'weixin';
        }
        $wechat_id = (int)\Yii::$app->request->get('wechat_id');
        $token = \Yii::$app->request->get('token');
        $mobile = '';
        $member_id = 0;
        if($token && $wechat_id){
            $memberEq = MemberEquipment::find()->where(['token'=>$token])->select('member_id')->asArray()->one();
            if($memberEq && $member = Member::find()->where(['id'=>$memberEq['member_id']])->select('id,mobile')->asArray()->one()){
                $mobile = $member['mobile'];
                $member_id = $member['id'];
            }
        }
        return $this->render('create',[
            'member_id'=>$member_id,
            'wechat_id'=>$wechat_id,
            'type'=>$type,
            'mobile'=>$mobile,
            'token'=>$token,
        ]);
    }
    //内部工作人员创建店铺页面
    public function actionInnerCreate(){
        $type = \Yii::$app->request->get('type');
        //1、租赁店 2、自营店
        $shop_operate_type = (int)\Yii::$app->request->get('shop_operate_type');
        if($shop_operate_type != 2){
            $shop_operate_type = 1;
        }
        $dev = \Yii::$app->request->get('dev') ?? '';
        $wechat_id = (int)\Yii::$app->request->get('wechat_id');
        $token = \Yii::$app->request->get('token');
        $mobile = '';
        $member_id = 0;
        $active_id = \Yii::$app->request->get('active_id') ?? '';
        if($token && $wechat_id){
            $memberEq = MemberEquipment::find()->where(['token'=>$token])->select('member_id')->asArray()->one();
            if($memberEq && $member = Member::find()->where(['id'=>$memberEq['member_id']])->select('id,mobile')->asArray()->one()){
                $mobile = $member['mobile'];
                $member_id = $member['id'];
            }
        }
        return $this->render('inner-create',[
            'shop_operate_type'=>$shop_operate_type,
            'member_id'=>$member_id,
            'wechat_id'=>$wechat_id,
            'type'=>$type,
            'mobile'=>$mobile,
            'token'=>$token,
            'dev' => $dev,
            'active_id' => $active_id,
        ]);
    }
    /*
     * 店铺申请审核未通过的修改页面
     */
    public function actionModifyShop()
    {
        $token = Yii::$app->request->get('token');
        $shop_id = (int)Yii::$app->request->get('shop_id');
        if(!$shop_id){
            throw new NotFoundHttpException('店铺错误');
        }
        if($wechat_id = (int)\Yii::$app->request->get('wechat_id')){
            $shopObj = Shop::find()->where(['id'=>$shop_id,'wx_member_id'=>$wechat_id,'shop_operate_type'=>[1,2]])->one();
        }else{
            if(!$memberEq = MemberEquipment::find()->where(['token'=>$token])->select('member_id')->asArray()->one()){
                throw new NotFoundHttpException('店铺错误');
            }
            $shopObj = Shop::find()->where(['id'=>$shop_id,'member_id'=>$memberEq['member_id'],'shop_operate_type'=>[1,2]])->one();
        }
        $shopApplyObj = ShopApply::findOne($shop_id);
        if(!$shopObj){ throw new NotFoundHttpException('店铺错误'); }
        $type = \Yii::$app->request->get('type');
        if(empty($type)){
            $type = 'weixin';
        }
        $dev = \Yii::$app->request->get('dev') ?? '';
        $wechat_id = (int)\Yii::$app->request->get('wechat_id');
        $token = \Yii::$app->request->get('token');
        $mobile = '';
        $member_id = 0;
        $inside = 0;
        if(isset($memberEq)){
                if($memberEq && $member = Member::find()->where(['id'=>$memberEq['member_id']])->select('id,mobile,inside')->asArray()->one()){
                        $mobile = $member['mobile'];
                        $member_id = $member['id'];
                        $inside = $member['inside'] == 1 ?? 0;
                }
        }
        $price = $shopApplyObj->apply_brokerage;
        $month_price = $shopApplyObj->apply_brokerage_by_month;
        return $this->render('modify-shop',[
            'shopObj' => $shopObj,
            'shopApplyObj' => $shopApplyObj,
            'member_id'=>$member_id,
            'wechat_id'=>$wechat_id,
            'type'=>$type,
            'mobile'=>$mobile,
            'token'=>$token,
            'dev' => $dev,
            'inside' => $inside,
            'apply_brokerage' => $shopApplyObj->apply_brokerage,
            'apply_brokerage_token' => md5("http//bjyltfcom{$price}123as{$month_price}d+"),
        ]);
    }
    /*
     * 店铺申请审核未通过的修改页面
     */
    public function actionBranchInstallModify()
    {
        $token = Yii::$app->request->get('token');
        $shop_id = (int)Yii::$app->request->get('shop_id');
        if(!$shop_id){
            throw new NotFoundHttpException('店铺错误');
        }
        if($wechat_id = (int)\Yii::$app->request->get('wechat_id')){
            $shopObj = Shop::find()->where(['id'=>$shop_id,'wx_member_id'=>$wechat_id])->one();
        }else{
            if(!$memberEq = MemberEquipment::find()->where(['token'=>$token])->select('member_id')->asArray()->one()){
                throw new NotFoundHttpException('店铺错误');
            }
            $shopObj = Shop::find()->where(['id'=>$shop_id,'member_id'=>$memberEq['member_id']])->one();
        }
        $shopApplyObj = ShopApply::findOne($shop_id);
        if(!$shopObj){ throw new NotFoundHttpException('店铺错误'); }
        $type = \Yii::$app->request->get('type');
        if(empty($type)){
            $type = 'weixin';
        }
        $dev = \Yii::$app->request->get('dev') ?? '';
        $wechat_id = (int)\Yii::$app->request->get('wechat_id');
        $token = \Yii::$app->request->get('token');
        $mobile = '';
        $member_id = 0;
        $inside = 0;
        if(isset($memberEq)){
            if($memberEq && $member = Member::find()->where(['id'=>$memberEq['member_id']])->select('id,mobile,inside')->asArray()->one()){
                $mobile = $member['mobile'];
                $member_id = $member['id'];
                $inside = $member['inside'] == 1 ?? 0;
            }
        }

        $price = $shopApplyObj->apply_brokerage*100;
        $month_price = $shopApplyObj->apply_brokerage_by_month*100;
        return $this->render('branch-install-modify',[
            'shopObj' => $shopObj,
            'shopApplyObj' => $shopApplyObj,
            'member_id'=>$member_id,
            'wechat_id'=>$wechat_id,
            'type'=>$type,
            'mobile'=>$mobile,
            'token'=>$token,
            'dev' => $dev,
            'inside' => $inside,
            'apply_brokerage' => $shopApplyObj->apply_brokerage,
            'apply_brokerage_token' => md5("http//bjyltfcom{$price}123as{$month_price}d+"),
        ]);
    }

    //申请成功选择页面
    public function actionSuccess(){
        $shop_id = Yii::$app->request->get('shopid');
        $token = Yii::$app->request->get('token');
        $modify = Yii::$app->request->get('modify') ?? '';
        $active_id = Yii::$app->request->get('active_id') ?? '';
        $wechat_id = (int)\Yii::$app->request->get('wechat_id');
        $shop_operate_type = (int)Yii::$app->request->get('shop_operate_type');
        return $this->render('success',[
            'shop_id' => $shop_id,
            'token' => $token,
            'modify' => $modify,
            'active_id' => $active_id,
            'wechat_id' => $wechat_id,
            'shop_operate_type'=>$shop_operate_type
        ]);

    }
    //录入总店信息
    public function actionHeadOfficeCreate(){
        $type = \Yii::$app->request->get('type');
        if(empty($type)){
            $type = 'weixin';
        }
        $dev = \Yii::$app->request->get('dev') ?? '';
        $wechat_id = (int)\Yii::$app->request->get('wechat_id');
        $token = \Yii::$app->request->get('token');
        $active_id = (int)\Yii::$app->request->get('active_id');
        $mobile = '';
        $member_id = 0;
        if($token && $wechat_id){
            $memberEq = MemberEquipment::find()->where(['token'=>$token])->select('member_id')->asArray()->one();
            if($memberEq && $member = Member::find()->where(['id'=>$memberEq['member_id']])->select('id,mobile')->asArray()->one()){
                $mobile = $member['mobile'];
                $member_id = $member['id'];
            }
        }
        return $this->render('head-office-create',[
            'active_id' => $active_id,
            'member_id'=>$member_id,
            'wechat_id'=>$wechat_id,
            'type'=>$type,
            'mobile'=>$mobile,
            'token'=>$token,
            'dev' => $dev,
        ]);
    }
    //修改总店信息
    public function actionHeadOfficeModify(){
        $headquarters_id = Yii::$app->request->get('headquarters_id');
        $type = \Yii::$app->request->get('type');
        $dev = \Yii::$app->request->get('dev') ?? '';
        if(empty($type)){
            $type = 'weixin';
        }
        $dev = \Yii::$app->request->get('dev') ?? '';
        $wechat_id = (int)\Yii::$app->request->get('wechat_id');
        $token = \Yii::$app->request->get('token');
        $member_id = 0;
        if($token && $wechat_id){
            $memberEq = MemberEquipment::find()->where(['token'=>$token])->select('member_id')->asArray()->one();
            if($memberEq && $member = Member::find()->where(['id'=>$memberEq['member_id']])->select('id,mobile')->asArray()->one()){
                $mobile = $member['mobile'];
                $member_id = $member['id'];
            }
        }
        return $this->render('head-office-modify',[
            'head_id' => $headquarters_id,
            'member_id'=>$member_id,
            'wechat_id'=>$wechat_id,
            'type'=>$type,
            'token'=>$token,
            'dev' => $dev,
        ]);
    }
    /*
     * 选择总店
     * */
    public function actionSelectHeadShop(){
        $dev = \Yii::$app->request->get('dev') ?? '';
        $wechat_id = (int)\Yii::$app->request->get('wechat_id');
        $token = \Yii::$app->request->get('token');
        return $this->render('select-head-shop',[
            'wechat_id'=>$wechat_id,
            'token'=>$token,
            'dev' => $dev,
        ]);
    }

    /*
     * 选择分店
     * */
    public function actionSelectBranchShop(){
        $headquarters_id = (int)\Yii::$app->request->get('headquarters_id');
        $dev = \Yii::$app->request->get('dev') ?? '';
        $wechat_id = (int)\Yii::$app->request->get('wechat_id');
        $token = \Yii::$app->request->get('token');
        return $this->render('select-branch-shop',[
            'headquarters_id'=>$headquarters_id,
            'wechat_id'=>$wechat_id,
            'token'=>$token,
            'dev' => $dev,
        ]);
    }

    //分店装屏
    public function actionBranchInstall(){
        $headquarters_id = Yii::$app->request->get('headquarters_id');
        $branch_id = Yii::$app->request->get('branch_id');
        //$headquarters_id = 23;
        //$branch_id = 13;
        $type = \Yii::$app->request->get('type');
        if(empty($type)){
            $type = 'weixin';
        }
        $dev = \Yii::$app->request->get('dev') ?? '';
        $wechat_id = (int)\Yii::$app->request->get('wechat_id');
        $token = \Yii::$app->request->get('token');
        $mobile = '';
        $member_id = 0;
        if($token && $wechat_id){
            $memberEq = MemberEquipment::find()->where(['token'=>$token])->select('member_id')->asArray()->one();
            if($memberEq && $member = Member::find()->where(['id'=>$memberEq['member_id']])->select('id,mobile')->asArray()->one()){
                $mobile = $member['mobile'];
                $member_id = $member['id'];
            }
        }
        //总店信息
        $totalInfo = '';
        return $this->render('branch-install',[
            'member_id'=>$member_id,
            'wechat_id'=>$wechat_id,
            'type'=>$type,
            'mobile'=>$mobile,
            'token'=>$token,
            'dev' => $dev,
            'head_id' => $headquarters_id,
            'branch_id' => $branch_id,
        ]);
    }

    //奖励金协议
    public function actionRewardAgreed(){
        $this->layout = false;
        return $this->render('reward-agreed');
    }
    
    
    
}
