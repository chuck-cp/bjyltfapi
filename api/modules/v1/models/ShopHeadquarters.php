<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use api\modules\v1\models\Member;
use api\modules\v1\models\Shop;
use api\modules\v1\models\ShopAdvertImage;
use api\modules\v1\models\MemberRewardAcount;
use Yii;
use yii\base\Exception;
use common\libs\ToolsClass;
class ShopHeadquarters extends ApiActiveRecord
{
    public $keyword;
    public $type;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_headquarters}}';
    }

    // 获取安装业务列表页数据 0、申请待审核 1、申请未通过 2、待安装 3、安装待审核 4、安装未通过
    //                     0、待审核 1、审核通过 2、审核驳回
    public function getInstallShopList()
    {
        $posterShopData = self::find()->select('id,create_at,examine_status,company_name as shop_name,company_area_name as street,company_address as address')->where(['and',['member_id' => Yii::$app->user->id],['<>','examine_status',1]])->asArray()->all();
        if(!empty($posterShopData)){
            foreach($posterShopData as $k => $v){
                if($v['examine_status'] == 1){
                    $posterShopData[$k]['examine_status'] = 2;
                }elseif ($v['examine_status'] == 2){
                    $posterShopData[$k]['examine_status'] = 1;
                }
            }
        }
        return $posterShopData;
    }

    /*
     * 获取总部列表
     * */
    public function getHeadquartersList(){
        $where = ['examine_status'=>1,'member_id'=>Yii::$app->user->id];
        if(!empty($this->keyword)){
            $where = ['and',$where,['like','company_name',$this->keyword]];
        }
        return self::find()->where($where)->select('id,company_name,activity_detail_id')->asArray()->all();
    }

    // 获取活动页面分店列表
    public static function getHeadquartersListByActivity($activity_detail_id) {
        $headModel = ShopHeadquarters::find()->where(['activity_detail_id' => $activity_detail_id])->select('id')->asArray()->one();
        if (empty($headModel)) {
            return [];
        }
        return ShopHeadquartersList::find()->joinWith('shop',false)->where(['yl_shop_headquarters_list.headquarters_id' => $headModel['id']])->select('branch_shop_name,status')->asArray()->all();
    }

    /*
     * 检查重复提交
     * */
    public function checkRepeatShop($name){
        $create_at = date('Y-m-d H:i:s',strtotime("-1 minute"));
        if(self::find()->where(['and',['member_id'=>Yii::$app->user->id],['name'=>$name],['>','create_at',$create_at]])->count()){
            return false;
        }
        return true;
    }

    public function beforeSave($insert)
    {
        $other_images = '';
        if($insert){
            $memberModel = Yii::$app->user->identity;
            $this->member_id = $memberModel->id;
            $this->member_mobile = $memberModel->mobile;
            $this->member_name = $memberModel->name;
            $this->corporation_member_id = intval(Member::getMemberFieldByWhere(['mobile'=>$this->mobile],'id'));
            $this->identity_card_front = ToolsClass::replaceCosUrl($this->identity_card_front);
            $this->identity_card_front = ToolsClass::replaceCosUrl($this->identity_card_front);
            $this->business_licence = ToolsClass::replaceCosUrl($this->business_licence);
            $this->agreement_name = '';
        }else{
            $this->identity_card_front = ToolsClass::replaceCosUrl($this->identity_card_front);
            $this->identity_card_front = ToolsClass::replaceCosUrl($this->identity_card_front);
            $this->business_licence = ToolsClass::replaceCosUrl($this->business_licence);
        }
        $orr = $this->other_image;
        if(is_array($orr) && count($orr) > 0){
            foreach ($orr as $k => $v){
                if(strpos($v,'yulongchuanmei')){
                    $orr[$k] = ToolsClass::replaceCosUrl($v);
                }
            }
            $other_images = implode(',',$orr);
        }
        $this->other_image = $other_images;
        return parent::beforeSave($insert);
    }

    public function shopHeadCreate()
    {
        try {
            if ($this->activity_detail_id > 0) {
                if (!ActivityDetail::updateAll(['is_apply' => 1, 'status' => 0], ['id' => $this->activity_detail_id, 'custom_member_id' => Yii::$app->user->id, 'is_apply' => 0])) {
                    return 'ACTIVITY_NOT_EXIST';
                }
            }
            $this->save();
            return 'SUCCESS';
        } catch (Exception $e) {
            \Yii::error($e->getMessage(), 'db');
            return 'ERROR';
        }
    }

    /**
     * 内部安装 获取总店详情
     */
    public function getShopHeadInfo()
    {
        $shopHeadinfo=self::find()->where(['id'=>$this->id])->select('id,name,mobile,member_id,identity_card_num,identity_card_front,identity_card_back,company_name,company_area_id,company_area_name,company_address,registration_mark,business_licence,agreement_name,examine_status,create_at,other_image')->asArray()->one();
        if(empty($shopHeadinfo)){
            return [];
        }
        $shopHeadinfo['other_image'] = explode(',', $shopHeadinfo['other_image']);
        $shopHeadlsit=ShopHeadquartersList::find()->where(['headquarters_id'=>$this->id])->select('id,shop_id,headquarters_id,branch_shop_name,branch_shop_area_id,branch_shop_area_name,branch_shop_address')->asArray()->all();
        if(empty($shopHeadlsit)){
            return [];
        }
        $shopHeadinfo['list']=$shopHeadlsit;
        return  array_merge($shopHeadinfo);
    }
    public function shopHeadModify(){
        $model = self::findOne($this->id);
        if(!$model){ throw new Exception('[ERROR]总店修改失败'); }
        $model->name = $this->name;
        #$model->mobile = $this->mobile;
        $model->identity_card_num = $this->identity_card_num;
        $model->company_name = $this->company_name;
        $model->company_area_id = $this->company_area_id;
        $model->company_area_name = $this->company_area_name;
        $model->company_address = $this->company_address;
        $model->registration_mark = $this->registration_mark;
        $model->business_licence = $this->business_licence;
        $model->identity_card_back = $this->identity_card_back;
        $model->identity_card_front = $this->identity_card_front;
        $model->other_image = $this->other_image;
        $model->examine_status = 0;
        try{
            $model->save();
            return true;
        }catch (Exception $e){
            \Yii::error($e->getMessage(),'db');
            throw new Exception('修改店铺总表失败');
            return false;
        }
    }
    /*
     * 获取总店详情
     */
    public function getHeadAndList(){
        $head = [];
        $head = self::find()->where(['id'=>$this->id,'corporation_member_id'=>Yii::$app->user->id])->asArray()->one();
        if(empty($head)){
            return [];
        }
        //查找奖励金同意协议
        $agree = MemberRewardAcount::find()->where(['shop_id'=>$this->id,'member_id'=>Yii::$app->user->id,'shop_type'=>2])->select('agreed')->asArray()->one();
        if(empty($agree)){
            $head['reward_agreed'] = '0';
        }elseif ($agree){
            $head['reward_agreed'] = strval($agree['agreed']);
        }
        if($head['agreement_name']){
            $head['agreement_name'] = 'http://i1.bjyltf.com/agreement/'.str_replace('.pdf','.html',$head['agreement_name']);
        }
        if($head['other_image']){
            $head['other_image'] = explode(',',$head['other_image']);
        }else{
            $head['other_image'] = null;
        }
        $head['head_image'] = '';
        $head['company_area_name'] = str_replace(['&gt;',' '],'',$head['company_area_name']);
        $head['image_num'] = ShopAdvertImage::find()->where(['shop_id'=>$this->id,'shop_type'=>2])->count().'张';
        $head['loop_time'] = '15分钟';
        return ['shop_info'=>$head];
    }
    //店铺同意协议
    public function agree(){
        $obj = self::find()->where(['corporation_member_id'=>Yii::$app->user->id,'id'=>$this->id])->one();
        if(!$obj){ return 'ERROR'; }
        try{
            $obj->agreed = $this->agreed;
            $obj->save();
            return 'SUCCESS';
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return 'ERROR';
        }
    }


    public function scenes(){
        return [
            'shop-head-list'=>[
                'keyword'=>[]
            ],
            'shop-head-create' =>[
                'id' => [],
                'activity_detail_id' => [
                    [
                        'type' => 'int'
                    ],
                ],
                'name'=>[
                    [
                        'required'=>'1',
                        'result'=>'NAME_EMPTY'
                    ],
                    [
                        'function'=>'this::checkRepeatShop',
                        'result'=>'REPEAT_SHOP'
                    ],
                ],
                'mobile'=>[
                    'required'=>'1',
                    'result'=>'MOBILE_EMPTY'
                ],
                'identity_card_num'=>[
                    'required'=>'1',
                    'result'=>'IDENTITY_CARD_EMPTY',
                ],
                'identity_card_front'=>[
                    'required'=>'1',
                    'result'=>'IDENTITY_CARD_FRONT_EMPTY'
                ],
                'identity_card_back'=>[
                    'required'=>'1',
                    'result'=>'IDENTITY_CARD_BACK_EMPTY'
                ],
                'company_name'=>[
                    'required'=>'1',
                    'result'=>'COMPANY_NAME_EMPTY'
                ],
                'company_area_id' => [
                    'required'=>'1',
                    'result'=>'COMPANY_AREA_ID_EMPTY'
                ],
                'company_area_name' => [
                    'required'=>'1',
                    'result'=>'COMPANY_AREA_NAME_EMPTY'
                ],
                'company_address' => [
                    'required'=>'1',
                    'result'=>'COMPANY_ADDRESS_EMPTY'
                ],
                'registration_mark'=>[
                    'required'=>'1',
                    'result'=>'REGISTRATION_MARK_EMPTY'
                ],
                'business_licence'=>[
                    'required'=>'1',
                    'result'=>'BUSINESS_LICENCE_EMPTY'
                ],
                'other_image' => [],
//                'member_id' => [
//                    'required'=>'1',
//                    'result'=>'MEMBER_ID_EMPTY'
//                ],



            ],
            'headoffice'=>[
                'id'=>[
                    'required'=>'1',
                    'result'=>'HEAD_QUARTERS_ID_EMPTY'
                ]
            ],
            //店家同意协议
            'agree'=>[
                'agreed' => [
                    'required' => '1',
                    'result' => 'AGREE_EMPTY',
                ],
                'id' => [
                    'required' => '1',
                    'result' => 'SHOP_ID_EMPTY',
                ],
            ],
        ];
    }

}
