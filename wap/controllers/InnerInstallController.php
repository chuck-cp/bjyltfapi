<?php

namespace wap\controllers;

use wap\core\WapController;
use wap\models\Member;
use wap\models\MemberEquipment;
use wap\models\ShopHeadquarters;
use yii\web\NotFoundHttpException;
use wap\models\Shop;
class InnerInstallController extends WapController
{
    public $enableCsrfValidation = false;
    public function beforeAction($action){
        if($action->id == 'procedure'){
            return true;
        }
        if(parent::beforeAction($action)){
            if(!$this->authentication()){
                throw new NotFoundHttpException('TOKEN ERROR');
            }
            return true;
        }
        return false;
    }
    //已申请安装的店铺
    public function actionIndex(){
        $token = \Yii::$app->request->get('token');
        $dev = \Yii::$app->request->get('dev') ?? '';
        $memberObj = Member::findIdentityByAccessToken($token);
        $member_id = $memberObj ? $memberObj->member_id : 0;
        if(!$member_id) { throw new NotFoundHttpException('Member Error'); }
        $memObj = Member::findOne($memberObj->member_id);
        if(!$memObj) { throw new NotFoundHttpException('Member Error'); }
        //$inside = $memObj->inside;
        if($memObj->status == 2) { throw new NotFoundHttpException('Member Error'); }
        $shopList = Shop::getShopsByMemberStatus($member_id,2);
        //查找是否有总店信息
        $headShops = ShopHeadquarters::find()->where(['and',['member_id'=>$member_id],['<>','examine_status',1]])->select('id,company_name,company_area_name,company_address,business_licence,examine_status,create_at')->asArray()->all();
//        echo '<pre/>';
//        print_r($headShops);exit;
        $totalList = array_merge($headShops, $shopList);
        array_multisort(array_column($totalList,'create_at'),SORT_DESC,$totalList);
        return $this->render('index',[
            'shopList' => $totalList,
            'token' => $token,
            'dev' => $dev,
        ]);
    }
    //选择页面
    public function actionChoose(){
        $token = \Yii::$app->request->get('token');
        $dev = \Yii::$app->request->get('dev') ?? '';
        $memberObj = Member::findIdentityByAccessToken($token);
        $member_id = $memberObj ? $memberObj->member_id : 0;
        $memberInfo = Member::find()->where(['id'=>$member_id, 'status'=>1])->select('inside')->asArray()->one();
        $inside = $memberInfo['inside'] ?? 0;
        return $this->render('choose', [
            'member_id' => $member_id,
            'inside' => $inside,
            'token' => $token,
            'dev' => $dev,
        ]);
    }
    //选择店铺类型
    public function actionChooseShopType(){
        $token = \Yii::$app->request->get('token');
        $dev = \Yii::$app->request->get('dev') ?? '';
        $active_id = \Yii::$app->request->get('active_id') ?? '';
        $memberObj = Member::findIdentityByAccessToken($token);
        $member_id = $memberObj ? $memberObj->member_id : 0;
        $memberInfo = Member::find()->where(['id'=>$member_id, 'status'=>1])->select('inside')->asArray()->one();
        $inside = $memberInfo['inside'] ?? 0;
        return $this->render('choose-shop-type', [
            'member_id' => $member_id,
            'inside' => $inside,
            'token' => $token,
            'dev' => $dev,
            'active_id' => $active_id,
        ]);

    }
    //用户选择下一步的时候检查是否是内部人员
    public function actionCheckInner(){
        $member_id = \Yii::$app->request->post('member_id');
        $member = Member::find()->where(['id'=>$member_id, 'status'=>1])->select('inside')->asArray()->one();
        return $member['inside'] ?? 0;
    }

    public function actionProcedure(){
        return $this->render('procedure');
    }
    //业绩排行
    public function actionRank(){
        $token = \Yii::$app->request->get('token');
        $member = MemberEquipment::find()->where(['token'=>$token])->select('member_id')->asArray()->one();
        return $this->render('rank',[
            'token' => $token,
            'member_id' => $member['member_id'],
        ]);
    }
    //待签约
    public function actionWaitSign(){
        $token = \Yii::$app->request->get('token');
        $member = MemberEquipment::find()->where(['token'=>$token])->select('member_id')->asArray()->one();
        return $this->render('wait-sign',[
            'token' => $token,
            'member_id' => $member['member_id'],
        ]);
    }
    public function actionActiveShopInfo(){
        $shopid = \Yii::$app->request->get('active_id');
        $token = \Yii::$app->request->get('token');
        $dev = \Yii::$app->request->get('dev');
        return $this->render('active-shop-info',[
            'id'=>$shopid,
            'token'=>$token,
            'dev' => $dev,
        ]);
    }
}
