<?php

namespace api\modules\v1\models;

use api\modules\v1\models\Activity;
use api\modules\v1\models\ActivityDetail;
use app\modules\v1\models\ShopHeadquarters;
use app\modules\v1\models\ShopHeadquartersList;
use common\libs\RedisClass;
use common\libs\ToolsClass;
use Yii;
use yii\base\Exception;
use yii\data\Pagination;
use api\modules\v1\models\User;
use api\modules\v1\models\ShopApply;
use api\modules\v1\models\AuthAssignment;
use api\modules\v1\models\Member;
use api\modules\v1\models\MemberAreaCount;
/**
 * 店铺管理
 */
class Shop extends \api\core\ApiActiveRecord
{
    public $type;
    public $keyword;
    public $shop_type;
    public $parent_id;
    public $verify_code;
    public $apply_mobile;
    public $is_gaode;
    public static function tableName()
    {
        return '{{%shop}}';
    }

    /*
     * 验证是否有查看店铺详情的权限
     * */
    public function verifyAuth($shop_id){
        if($this->shop_type == 1){
            return self::find()->where(['id'=>$shop_id,'admin_member_id'=>Yii::$app->user->id])->count();
        }elseif ($this->shop_type == 2){
            $install_team_id = MemberTeam::find()->where(['team_member_id'=>Yii::$app->user->id])->select('id')->asArray()->one();
            if(empty($install_team_id)){
                return false;
            }
            $num1 = self::find()->where(['id'=>$shop_id,'install_team_id'=>$install_team_id['id']])->count();
            return $num1;
        }else{
            if(self::find()->where(['id'=>$shop_id,'member_id'=>Yii::$app->user->id])->count() || self::find()->where(['id'=>$shop_id,'shop_member_id'=>Yii::$app->user->id])->count()){
                return true;
            }
            return ShopLower::find()->where(['member_id'=>Yii::$app->user->id,'shop_id'=>$shop_id])->count();
        }
        return false;
    }

    /*
     * 获取我的店铺数量
     * */
    public function getMyShopNumber(){
        return self::find()->where(['shop_member_id'=>Yii::$app->user->id])->count();
    }

    /*
     * 更新店主ID
     * */
    public function updateShopMemberId($mobile,$member_id){
        if(empty($mobile)){
            return true;
        }
        try{
            /*20190117*order->member_id,member_mobile******************************/
            Order::updateAll(['member_name'=>$mobile, 'member_id'=>$member_id], ['member_mobile'=>$mobile, 'member_id' => 0]);
            /**********************************************************************/
            $shopApplyModel = ShopApply::find()->where(['apply_mobile'=>$mobile])->select('id')->asArray()->all();
            if(!empty($shopApplyModel)){
                $shop_id = array_column($shopApplyModel,'id');
                if($applyList = Shop::find()->where(['id'=>$shop_id,'shop_member_id' => 0])->select('id,name,status,area')->asArray()->all()){
                    foreach($applyList as $shop){
                        self::updateAll(['shop_member_id'=>$member_id],['id'=>$shop['id']]);
                        if($shop['status'] != 5){
                            continue;
                        }
                        if(!LogAccount::writeLog(ShopApply::findOne($shop['id'])->getAttribute('apply_brokerage'),1,'店铺费用',$shop['name'],$member_id)){
                            throw new Exception("[error]创建收入日志失败");
                        }
                    }
                }
            }
            /**************************************/
            // 计算要发放本月的金额
            if(date('d') >= SystemConfig::getConfig('subsidy_date')) {
                $month = date('Ym',strtotime('-1 month'));
            } else {
                $month = date('Ym',strtotime('-2 month'));
            }
            ScreenRunTimeShopSubsidy::updateAll(['apply_id' => $member_id],['apply_mobile' => $mobile]);
            // 查询维护费发放记录
            $subsidyShopList = ScreenRunTimeShopSubsidy::find()->select('price,shop_name,id')->where(['and',['apply_mobile'=>$mobile],['status'=>1],['grant_status'=>0],['<=','date',$month]])->asArray()->all();
            if(!empty($subsidyShopList)){
                foreach($subsidyShopList as $shop){
                    ScreenRunTimeShopSubsidy::updateAll(['grant_status'=>1],['id'=>$shop['id'],'grant_status'=>0]);
                    if(!LogAccount::writeLog($shop['price'],1,'设备维护费',$shop['shop_name'],$member_id)){
                        throw new Exception("[error]创建安装联系费收入日志失败");
                    }
                }
            }
            /**************************************/
            //店铺第二年每月维护费统计表
            ShopApplyBrokerage::updateAll(['apply_id' => $member_id],['apply_mobile' => $mobile]);
            $sablist = ShopApplyBrokerage::find()->select('price,shop_name,id,date_desc')->where(['and',['apply_mobile'=>$mobile],['grant_status'=>0],['<=','date',$month]])->asArray()->all();
            if(!empty($sablist)){
                foreach ($sablist as $sa){
                    ShopApplyBrokerage::updateAll(['grant_status'=>1],['id'=>$sa['id'],'grant_status'=>0]);
                    if(!LogAccount::writeLog($sa['price'],1,$sa['date_desc'].'买断费',$sa['shop_name'],$member_id)){
                        throw new Exception("[error]创建月买断费收入日志失败");
                    }
                }
            }
            /************************************************/
            //更新总店法人id
            $shopHead = ShopHeadquarters::find()->where(['mobile'=>$mobile])->select('id')->asArray()->all();
            if(!empty($shopHead)){
                $head_ids = array_column($shopHead,'id');
                ShopHeadquarters::updateAll(['corporation_member_id'=>$member_id],['id'=>$head_ids]);
            }
            //更新introducer_member_id(业务介绍人)
            $shopModel = Shop::find()->where(['introducer_member_mobile'=>$mobile,'introducer_member_id'=>0])->select('id,area,name,status,introducer_member_price')->asArray()->all();
            if(!empty($shopModel)){
                $updateIds = array_column($shopModel,'id');
                Shop::updateAll(['introducer_member_id'=>$member_id], ['id'=>$updateIds]);

                foreach($shopModel as $shop){
                    if($shop['status'] != 5){
                        continue;
                    }
                    if(!LogAccount::writeLog($shop['introducer_member_price'],1,'推荐店铺奖励金',$shop['name'],$member_id,0,0,0)){
                        throw new Exception("[error]创建推荐店铺奖励金收入日志失败");
                    }
                }
            }
            //更新shop_screen_replace中的shop_member_id
            ShopScreenReplace::updateAll(['shop_member_id'=>$member_id], ['apply_mobile'=>$mobile]);
            return true;
        }catch (\Throwable $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }

    /*
     * 修改店铺信息
     */
    public function modifyShop()
    {
        try{
            $this->status = 0;
            $this->area_name = SystemAddress::getAreaNameById($this->area,'ALL');
            $this->screen_number = $this->apply_screen_number;
            $this->mirror_account = $this->mirror_account;
            $this->setMemberPrice();
            $res = $this->save();
            if($res == false){
                throw new Exception("[error]修改店铺失败");
            }
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }

    }
    /*
     * 店主身份获取我的店铺
     * */
    public function getMemberShopByKeeper($shop_id = 0){
        $where = ['shop_member_id'=>Yii::$app->user->id];
        if($shop_id){
            $where['id'] = $shop_id;
        }
        $info = self::find()->where($where)->orderBy('id desc')->select('id,name,member_name,agreement_name,status')->asArray()->all();
        if(!empty($info)){
            foreach ($info as $k => $item) {
                $info[$k]['agreement_name'] = str_replace('.pdf','.html', $item['agreement_name']);
            }
        }
        return $info;
    }

    // 获取安装业务列表页数据
    public function getInstallShopList()
    {
        $posterShopData = self::find()->select('activity_detail_id,id,name as shop_name,screen_number as led_screen_number,status as examine_status,shop_image,shop_province as province,shop_city as city,shop_area as area,address,shop_street as street,create_at')->where(['and',['member_id' => Yii::$app->user->id],['<','status',5]])->asArray()->all();
        return $posterShopData;
    }

    /*
     * 获取我的店铺
     * */
    public function getMemberShop($member_id = ''){
        $member_id = empty($member_id) ? Yii::$app->user->id : $member_id;
        if($this->shop_type){
            $shopModel = self::find()->where(['admin_member_id'=>$member_id])->orderBy('id desc')->select('id,shop_image,name,area_name,address,screen_number,date(create_at) as create_at,screen_status,status');
            $shopModel->andWhere(['status'=>5])->andFilterWhere(['screen_status'=>$this->screen_status]);
        }else{
            $shopModel = self::find()->where(['member_id'=>$member_id])->orderBy('id desc')->select('id,shop_image,name,area_name,address,screen_number,date(create_at) as create_at,screen_status,status');
            if(is_numeric($this->status)){
//                if($this->status == 1){
//                    //如果是待安装,搜索待发货、待安装两个状态
//                    $this->status = [1,3,4,5];
//                }else if($this->status == 6){
//                    //如果是安装完成,搜索确认安装和安装完成两个状态
//                    $this->status = 6;
//                }
                $shopModel->andWhere(['status'=>$this->status]);
            }
        }
        if($this->keyword){
            $shopModel->andWhere(['or',['like','area_name',$this->keyword],['like','name',$this->keyword],['like','member_name',$this->keyword]]);
        }
        if($this->create_at){
            $start = $this->create_at.'-01';
            $end = date("Y-m-d",strtotime("+1 month",strtotime($start)));
            $shopModel->andWhere(['and',['>=','create_at',$start],['<','create_at',$end]]);
        }
        $shopModel->andFilterWhere(['left(area,'.strlen($this->area).')'=>$this->area]);
        $pagination = new Pagination(['totalCount'=>$shopModel->count()]);
        $pagination->validatePage = false;
        return $shopModel->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
    }

    /*
     * 获取我下级的店铺
     * */
    public function getMemberLowerShop($member_id = 0){
        if($member_id){
            $shopModel = self::find()->where(['member_id'=>$member_id])->orderBy('id desc')->select('id,shop_image,member_name,name,area_name,screen_number,date(create_at) as create_at,screen_status,status');
        }else{
            $shopModel = ShopLower::find()->joinWith('shop')->where(['yl_shop_lower.member_id'=>Yii::$app->user->id])->select('shop_id,yl_shop.id,shop_image,member_name,name,area_name,screen_number,date(create_at) as create_at,screen_status,status');
        }
        if(is_numeric($this->status)){
//            if($this->status == 1){
//                //如果是待安装,搜索待发货、待安装两个状态
//                $this->status = [1,3,4,5];
//            }
            $shopModel->andWhere(['status'=>$this->status]);
        }
        if($this->keyword){
            $shopModel->andWhere(['or',['like','area_name',$this->keyword],['like','name',$this->keyword],['like','member_name',$this->keyword]]);
        }
        if($this->create_at){
            $start = $this->create_at.'-01';
            $end = date("Y-m-d",strtotime("+1 month",strtotime($start)));
            $shopModel->andWhere(['and',['>=','create_at',$start],['<','create_at',$end]]);
        }

        $shopModel->andFilterWhere(['left(area,'.strlen($this->area).')'=>$this->area]);
        $pagination = new Pagination(['totalCount'=>$shopModel->count()]);
        $pagination->validatePage = false;
        $shopModel = $shopModel->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        return $shopModel;
    }

    /*
     * 获取店铺详情
     * */
    public function getShopInfo($id){
        $applyModel = ShopApply::find()->where(['id'=>$id])->select('contacts_mobile as apply_mobile,contacts_name as apply_name,panorama_image,apply_brokerage,apply_brokerage_by_month,screen_start_at,screen_end_at')->asArray()->one();
        if(empty($applyModel)){
            return [];
        }
        $shopModel = self::find()->where(['id'=>$id])->select('name,admin_member_id,member_mobile,member_name,member_id,area_name,apply_screen_number,screen_number,status,screen_status,create_at,address,shop_image,agreement_name,agreed,status')->asArray()->one();
        if(empty($shopModel)){
            return [];
        }
        //查找奖励金同意协议
        $agree = MemberRewardAcount::find()->where(['shop_id'=>$id,'member_id'=>Yii::$app->user->id,'shop_type'=>1])->select('agreed')->asArray()->one();
        if(empty($agree)){
            $shopModel['reward_agreed'] = '0';
        }elseif ($agree){
            $shopModel['reward_agreed'] = strval($agree['agreed']);
        }
        if($shopModel['agreement_name']){
            $shopModel['agreement_name'] = 'http://i1.bjyltf.com/agreement/'.str_replace('.pdf','.html',$shopModel['agreement_name']);
        }
        $examineModel = LogExamine::getExamineByShop($id,$shopModel['status']);
        if(!in_array($shopModel['status'],[1,4])){
            $examineModel['fail_reason'] = NULL;
        }
        $shopModel = array_merge($shopModel,$examineModel);
        $shopModel['month_price'] = 600;
        $applyModel['screen_start_at'] = $applyModel['screen_start_at'].'-'.$applyModel['screen_end_at'];
        return array_merge($applyModel,$shopModel);
    }

    /*
     * 判断店铺申请人手机号和公司名称是否有重复数据
     * */
    public function checkRepeatMobileAndName($type,$apply_model,$company_name) {
        if ($type == 'create') {
            if (ShopApply::find()->where(['apply_mobile' => $apply_model])->count()) {
                $this->repeat_mobile = 1;
            }
            if (ShopApply::find()->where(['company_name' => $company_name])->count()) {
                $this->repeat_company_name = 1;
            }
        } else {
            $this->repeat_company_name = 0;
            if (ShopApply::find()->where(['and',['company_name' => $company_name],['!=','id',$this->id]])->count()) {
                $this->repeat_company_name = 1;
            }
        }
    }
    /*
     * 创建店铺
     * */
    public function createShop($is_inner = false){
        try{
            $this->member_inside = Yii::$app->user->identity->inside;
            if(!$this->setParentId()) {
                throw new Exception("设置上级ID失败");
            }
            $this->setMemberPrice();
            $this->shop_image = ToolsClass::replaceCosUrl($this->shop_image);
            $this->screen_number = $this->apply_screen_number;
            $this->area_name = SystemAddress::getAreaNameById($this->area);
            $this->shop_province = SystemAddress::getAreaNameById(substr($this->area,0,5),'');
            $this->shop_city = SystemAddress::getAreaNameById(substr($this->area,0,7),'');
            $this->shop_area = SystemAddress::getAreaNameById(substr($this->area,0,9),'');
            $this->shop_street = SystemAddress::getAreaNameById(substr($this->area,0,12),'');
            //如果是内部人员
            if($is_inner == true){
                $this->member_mobile = Yii::$app->user->identity->mobile;
                $this->member_id = Yii::$app->user->identity->id;
                $this->member_name = Yii::$app->user->identity->name;
                $this->install_status = 2;
                $this->status = 0;
            }
            $this->save();
            if(!$this->createShopLower()){
                throw new Exception("店铺关联上级失败");
            }
            return true;
        }catch (Exception $e){
            //var_dump($e->getMessage());exit;
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }

    // 设置上级ID
    public function setParentId() {
        $this->parent_member_id = Yii::$app->user->identity->parent_id;
        # 如果没有上级ID,并且不是内部人员
        if (!$this->parent_member_id && $this->member_inside != 1) {
            return false;
        }
        return true;
    }

    // 设备店铺联系费
    public function setMemberPrice() {
        $this->activity_detail_id = (int)$this->activity_detail_id;
        if($this->activity_detail_id){
            $activeDetailObj = ActivityDetail::findOne($this->activity_detail_id);
            if(!$activeDetailObj){
                throw new Exception('找不到设备安装活动签约明细记录');
            }
            $this->activity_detail_id = $this->activity_detail_id;
            $activeObj = Activity::findOne($activeDetailObj->activity_id);
            if(!$activeObj){
                throw new Exception('找不到设备安装活动记录');
            }
            //介绍人是否是注册用户
            if($memberInfo = Member::find()->where(['mobile'=>$activeObj->member_mobile])->select('id,`name`')->asArray()->one()){
                $this->introducer_member_id = $memberInfo['id'];
                $this->introducer_member_name = $memberInfo['name'];
            }else{
                $this->introducer_member_name = $activeObj->member_name;
            }
            $this->introducer_member_mobile = $activeObj->member_mobile;
            $this->introducer_member_price = SystemConfig::getConfig('shop_contact_price_outside_self') ?? 0;
            $this->member_price = SystemConfig::getConfig('shop_contact_price_outside_parent') ?? 0;
        } elseif ($this->member_inside == 1) {
            $priceConfig = [];
            if($this->mirror_account == 2){
                $priceConfig['shop_contact_price_inside_self'] = SystemConfig::getConfig('small_shop_price_first_install_salesman');
                $priceConfig['shop_contact_price_inside_parent'] = SystemConfig::getConfig('small_shop_price_first_install_salesman_parent');
            }else{
                # 内部人员价格
                $priceConfig = SystemConfig::getAllConfigById(['shop_contact_price_inside_self','shop_contact_price_inside_parent']);
            }
            $this->member_price = isset($priceConfig['shop_contact_price_inside_self']) ? $priceConfig['shop_contact_price_inside_self'] : 0;
            $this->parent_member_price = isset($priceConfig['shop_contact_price_inside_parent']) ? $priceConfig['shop_contact_price_inside_parent'] : 0;
        } else {
            $priceConfig = [];
            if($this->mirror_account == 2){
                $priceConfig['shop_contact_price_outside_self'] = SystemConfig::getConfig('small_shop_price_first_install_salesman');
                $priceConfig['shop_contact_price_outside_parent'] = SystemConfig::getConfig('small_shop_price_first_install_salesman_parent');
            }else{
                # 外部人员价格
                $priceConfig = SystemConfig::getAllConfigById(['shop_contact_price_outside_self','shop_contact_price_outside_parent']);
            }

            $this->member_price = isset($priceConfig['shop_contact_price_outside_self']) ? $priceConfig['shop_contact_price_outside_self'] : 0;
            $this->parent_member_price = isset($priceConfig['shop_contact_price_outside_parent']) ? $priceConfig['shop_contact_price_outside_parent'] : 0;
        }
    }

    /*
     * 为新增店铺分配审核人员
     */
//    public function afterSave($insert, $changedAttributes){
//        if($insert){
//            //如果是插入操作分配审核人
//            $redisObj = Yii::$app->redis;
//            $redisObj->select(3);
//            $admin = $redisObj->get('examine_user');
//            $base  = ['and',['item_name'=>'安装反馈审核'],['>','yl_user.member_group',0]];
//            //缓存中没有记录
//            if(!$admin){
//                $adminInfo = AuthAssignment::getUserInfo($base);
//            }else{
//                $adminPrevInfo = json_decode($admin,true);
//                $where = ['and',['status'=>1],['>','yl_user.member_group',$adminPrevInfo['member_group']],['item_name'=>'安装反馈审核']];
//                $adminInfo = AuthAssignment::getUserInfo($where);
//                if(empty($adminInfo)){
//                    $adminInfo = AuthAssignment::getUserInfo($base);
//                }
//
//            }
//            if(!empty($adminInfo)){
//                $len = count($adminInfo);
//                if($len == 1){
//                    self::updateAll(['examine_user_group'=>$adminInfo[0]['member_group'], 'examine_user_name'=>$adminInfo[0]['username']], ['id'=>$this->id]);
//                }elseif ($len == 2){
//                    self::updateAll(['examine_user_group'=>$adminInfo[0]['member_group'], 'examine_user_name'=>$adminInfo[0]['username'].','.$adminInfo[1]['username']], ['id'=>$this->id]);
//                }
//
//                //将该用户信息写入缓存
//                $redisObj->set('examine_user',json_encode(['member_group'=>$adminInfo[0]['member_group']]));
//            }
//
//        }
//        parent::afterSave($insert, $changedAttributes);
//    }

    /*
     * 创建伙伴店铺关联关系
     * */
    public function createShopLower($shop_id=0){
        try{
            if($this->member_id){
                $result = MemberShopArea::createArea($this->member_id,$this->area,1);
                if(empty($result)){
                    throw new Exception("地区创建失败");
                }
                $result = MemberShopDate::createDate($this->member_id,date('Y-m'),1);
                if(empty($result)){
                    throw new Exception("日期创建失败");
                }
            }
            if($this->parent_id){
                $lowerModel = new ShopLower();
                $lowerModel->member_id = $this->parent_id;
                $lowerModel->shop_id = $this->id;
                $lowerModel->save();
                $result = MemberShopArea::createArea($this->parent_id,$this->area,2);
                if(empty($result)){
                    throw new Exception("地区创建失败");
                }
                $result = MemberShopDate::createDate($this->parent_id,date('Y-m'),2);
                if(empty($result)){
                    throw new Exception("日期创建失败");
                }
            }
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }
    /*
     * 加载店铺管理人信息
     * */
    public function loadAdminMember($admin_area){
        $shop_area = substr($this->area,0,9);
        if($shop_area == $admin_area){
            $this->admin_member_id = $this->member_id;
        }
    }

    /*
     * 检查上级手机号
     * */
    public function checkMemberMobile($mobile){
        if(empty($mobile)){
            return true;
        }
        $memberModel = Member::find()->where(['mobile'=>$mobile])->select('id,name,admin_area,parent_id')->asArray()->one();
        if(empty($memberModel)){
            $this->member_name = $mobile;
            return true;
        }
        $this->member_id = $memberModel['id'];
        $this->member_name = $memberModel['name'];
        $this->parent_id = $memberModel['parent_id'];
        return true;
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

    /*
     * 验证地区格式
     * */
    public function checkAreaFormat($area_id){
        if(strlen($area_id) == 12){
            return true;
        }
        return false;
    }

    /*
    * 获取店铺安装状态
    */
    public function getShopStatus($id){
        return  self::find()->where(['id'=>$id])->select('id,install_member_id,status')->asArray()->one();
    }

    /*
     * 获取店铺信息，提交到综合事业部屏幕安装接口
     * */
    public function getShopStorage($id){
        $applyModel = ShopApply::find()->where(['id'=>$id])->select('apply_name,company_name,install_name,apply_mobile,screen_start_at,screen_end_at')->asArray()->one();

        if(empty($applyModel)){
            return [];
        }
        $shopModel = self::find()->where(['id'=>$id])->select('shop_member_id,member_id,member_name,name,area,address,install_member_name')->asArray()->one();
        if(empty($shopModel)){
            return [];
        }
        return array_merge($applyModel,$shopModel);
    }
    /*
    * 获取微信端申请记录
     *     * */
    public function getRecord(){
        if($this->member_id>0){
            $shopModel = self::find()->where(['or',['member_id'=>$this->member_id],['wx_member_id'=>$this->wx_member_id]])->orderBy('id desc')->select('id,member_name,area_name,address,apply_screen_number,create_at,status');
        }else{
            $shopModel = self::find()->where(['wx_member_id'=>$this->wx_member_id])->orderBy('id desc')->select('id,member_name,area_name,address,apply_screen_number,create_at,status');
        }
        $pagination = new Pagination(['totalCount'=>$shopModel->count()]);
        $pagination->validatePage = false;
        $shopModel = $shopModel->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        if(empty($shopModel)){
            return [];
        }
        //获取申请人姓名
        foreach($shopModel as $key=>$value){
            $shopapplyModel=ShopApply::find()->where(['id'=>$value['id']])->select('apply_name')->orderBy('id desc')->limit(1)->asArray()->one();
            if(empty($shopapplyModel)){
                $shopModel[$key]['apply_name'] ='';
            }
            else{
                $shopModel[$key]['apply_name'] =$shopapplyModel['apply_name'] ;
            }
        }
        return $shopModel;
    }

    /*
   * 获取微信端申请记录详情
    *     * */
    public function getRecordinfo($id){
        $applyModel = ShopApply::find()->where(['id'=>$id])->select('apply_code,apply_name,apply_mobile,company_name,panorama_image,identity_card_num')->asArray()->one();
        if(empty($applyModel)){
            return [];
        }
        $shopModel = self::find()->where(['id'=>$id])->select('member_name,area_name,address,name,acreage,screen_number,mirror_account,shop_image,create_at,status,delivery_status')->asArray()->one();
        if(empty($shopModel)){
            return [];
        }
        $shopModel['logisticsname']="";
        $shopModel['logistics_id']="";
        // 状态(0、申请待审核 1、申请未通过 2、待安装 3、安装待审核 4、安装未通过 5、已安装)',
        switch ($shopModel['status']){
            case '0':
                $shopModel['status']='申请待审核';
                break;
            case '1':
                $shopModel['status']='申请未通过';
                break;
            case '2':
                $shopLogistic=ShopLogistics::find()->where(['shop_id'=>$id])->select('name,logistics_id')->asArray()->one();
                if(empty($shopLogistic)){
                    $shopModel['status']='待安装';
                }else{
                    $shopModel['status']='待安装';
                    $shopModel['logisticsname']=$shopLogistic['name'];
                    $shopModel['logistics_id']=$shopLogistic['logistics_id'];
                }
                break;
            case '3':
                $shopModel['status']='安装待审核';
                break;
            case '4':
                $shopModel['status']='安装未通过';
                break;
            case '5':
                $shopModel['status']='已安装';
                break;
        }
        return array_merge($applyModel,$shopModel);
    }

    public static function getShopByHeadId($head_id){
        return self::find()->where(['headquarters_id'=>$head_id, 'status'=>5, 'shop_member_id'=>Yii::$app->user->id])->select('id,`name`')->asArray()->all();
    }
    /*
     * 查看会员安装了多少家店铺
     */
    public static function getMemNumber($member_id){
        if(!$member_id) return 0;
        return self::find()->where(['member_id'=>$member_id, 'status'=>5])->count();
    }
    /*
     * 验证验证码是否合法
     */
    public function checkApplyBrokerageToken($token){
        return $token == ToolsClass::getKeeperBrokerageToken($this->apply_mobile);
    }
    /*
     * 按条件获取店铺
     */
    public static function getShopByCondition($condition, $field, $orderBy='id desc'){
        return self::find()->where($condition)->select($field)->orderBy($orderBy)->asArray()->one();
    }

    /*
     * 检查店铺类型
     * */
    public function checkShopOperateType(){
        if(!in_array($this->shop_operate_type,[1,2,3])){
            return false;
        }
        return true;
    }

    /*
     * 若有分店检验分店id是否重复
     */
    public function checkHeadListId(){
        if(!$this->headquarters_list_id){
            return true;
        }
        $obj = ShopHeadquartersList::findOne($this->headquarters_list_id);
        if(!$obj){
            return false;
        }
        $re = $obj->shop_id;
        return $re > 0 ? false : true;
    }
    /*
     * 安装历史列表查看店铺详情
     */
    public function getShopDetailById($id,$maintain_type){
        $memberInstall = MemberInstallHistory::find()->where(['id'=>$id])->select('shop_id,replace_id')->asArray()->one();
        $shop_id = $memberInstall['shop_id'];
        $replace_id = $memberInstall['replace_id'];
        if($maintain_type == 5){
            return $this->getFieldLists($replace_id,'maintain');
        }
        if(!$replace_id){
            $re = $this->getFieldLists($shop_id);
        }else{
            $re = $this->getFieldLists($replace_id,'replace');
        }
        return $re;

    }
    private function getShopAndApply($id){
        $shop = self::find()->where(['id'=>$id,'status'=>5])->select('shop_member_id,wx_member_id,shop_image,name,area_name,address,screen_number,acreage,mirror_account,member_name,member_mobile')->asArray()->one();
        if(!$shop){ $shop = []; }
        $apply = [];
        if($shop){
            $apply = ShopApply::find()->where(['id'=>$id])->select('contacts_name,contacts_mobile,company_name,panorama_image,screen_start_at,screen_end_at')->asArray()->one();
            if(!$apply){ $apply = []; }
            if(!empty($apply)){
                $apply['screen_start_at'] = $apply['screen_start_at'].'-'.$apply['screen_end_at'];
            }
        }
        return ['shop'=>$shop, 'apply'=>$apply];
    }
    private function getFieldLists($id,$type='shop'){
        $re = [];
        $i = 0;
        $fy = self::getFieldChinese();
        if($type == 'shop'){
            $shopInfo = $this->getShopAndApply($id);
            $apply = $shopInfo['apply'];
            $shop = $shopInfo['shop'];
            $total['contacts_name'] = $apply['contacts_name'];
            $total['contacts_mobile'] = $apply['contacts_mobile'];
            $total['company_name'] = $apply['company_name'];
            $total['name'] = $shop['name'];
            $total['area_name'] = $shop['area_name'];
            $total[''] = $shop['address'];
            $total['acreage'] = $shop['acreage'].'平米';
            $total['screen_number'] = $shop['screen_number'].'台';
            $total['mirror_account'] = $shop['mirror_account'].'面';
            $total['screen_start_at'] = $apply['screen_start_at'];
            $total['member_name'] = $shop['member_name'];
            $total['member_mobile'] = $shop['member_mobile'];
            $total['shop_image'] = $shop['shop_image'];
            $total['panorama_image'] = $apply['panorama_image'];
            foreach ($total as $k => $v){
                $re[$i]['value'] = $v;
                if($k == 'shop_image' || $k == 'panorama_image'){
                    $re[$i]['remark'] = 'image';
                }else{
                    $re[$i]['remark'] = 'string';
                }
                if($k){
                    $re[$i]['name'] = $fy[$k];
                }else{
                    $re[$i]['name'] = '';
                }
                $i++;
            }
        }elseif($type == 'replace'){
            $fields = 'maintain_type,shop_id,shop_name as name,shop_area_name as area_name,shop_address as address,remove_device_number,install_device_number,install_device_number,install_software_number,problem_description';
            $replaceInfo = ShopScreenReplace::find()->where(['id'=>$id])->select($fields)->asArray()->one();
            $shopInfo = $this->getShopAndApply($replaceInfo['shop_id']);
            $apply = $shopInfo['apply'];
            $shop = $shopInfo['shop'];
            $total['name'] = $shop['name'];
            $total['area_name'] = $shop['area_name'];
            $total[''] = $shop['address'];
            $total['contacts_name'] = $apply['contacts_name'];
            $total['contacts_mobile'] = $apply['contacts_mobile'];
            $total['member_name'] = $shop['member_name'];
            $total['member_mobile'] = $shop['member_mobile'];
            $total['remove_device_number'] = $replaceInfo['remove_device_number'];
            $total['gh_device_number'] = $replaceInfo['remove_device_number'];
            $total['install_device_number'] = $replaceInfo['install_device_number'];
            $total['install_software_number'] = $replaceInfo['install_software_number'];
            $total['xz_software_number'] = $replaceInfo['install_software_number'];
            $total['problem_description'] = $replaceInfo['problem_description'];
            //2、更换屏幕 3、拆除屏幕 4、新增屏幕
            return $this->getMaintainList($replaceInfo['maintain_type'],$total);

        }elseif($type == 'maintain'){
            $fields = 'shop_id,shop_name as name,shop_area_name as area_name,shop_address as address,apply_name,apply_mobile,problem_description,images';
            $maintainInfo = ShopScreenAdvertMaintain::find()->where(['id'=>$id])->select($fields)->asArray()->one();
            $softNumberArr = json_decode($maintainInfo['images'], true);
            $softNumbers = '';
            foreach ($softNumberArr as $k => $v){
                $softNumbers .= $v['number'].",";
            }
            $total['maintain_soft'] = ltrim($softNumbers,",");
            //业务对接人
            $hzrInfo = Shop::find()->where(['id'=>$maintainInfo['shop_id']])->select('member_name, member_mobile')->asArray()->one();
            $total['name'] = $maintainInfo['name'];
            $total['area_name'] = $maintainInfo['area_name'];
            $total[''] = $maintainInfo['address'];
            $total['contacts_name'] = $maintainInfo['apply_name'];
            $total['contacts_mobile'] = $maintainInfo['apply_mobile'];
            $total['member_name'] = $hzrInfo['member_name'];
            $total['member_mobile'] = $hzrInfo['member_mobile'];
            $total['problem_description'] = $maintainInfo['problem_description'];
            return $this->getMaintainList(5,$total);
        }
        return $re;
    }
    private function getMaintainList($nu,$replaceInfo){
        $i = 0;
        $insert = [];
        $fy = self::getFieldChinese();
        switch ($nu){
            case 2:
                unset($replaceInfo['install_device_number']);
                unset($replaceInfo['remove_device_number']);
                unset($replaceInfo['xz_software_number']);
                $delArr = explode(',',$replaceInfo['gh_device_number']);
                $addArr = explode(',',$replaceInfo['install_software_number']);
                $softArr = [];
                foreach ($delArr as $k => $v){
                    if($k == 0){
                        $insert[$k]['value'] = $v;
                        $insert[$k]['remark'] = 'string';
                        $insert[$k]['name'] = '更换旧设备编号';
                    }else{
                        $insert[$k]['value'] = $v;
                        $insert[$k]['remark'] = 'string';
                        $insert[$k]['name'] = '';
                    }
                }
                foreach ($addArr as $k => $v){
                    if($k == 0){
                        $softArr[$k]['value'] = $v;
                        $softArr[$k]['remark'] = 'string';
                        $softArr[$k]['name'] = '更换新设备编号';
                    }else{
                        $softArr[$k]['value'] = $v;
                        $softArr[$k]['remark'] = 'string';
                        $softArr[$k]['name'] = '';
                    }
                }
                $insert = array_merge($insert,$softArr);
                unset($replaceInfo['gh_device_number']);
                unset($replaceInfo['install_software_number']);
                break;
            case 3:
                unset($replaceInfo['install_device_number']);
                unset($replaceInfo['install_software_number']);
                unset($replaceInfo['gh_device_number']);
                unset($replaceInfo['xz_software_number']);
                $delArr = explode(',',$replaceInfo['remove_device_number']);
                foreach ($delArr as $k => $v){
                    if($k == 0){
                        $insert[$k]['value'] = $v;
                        $insert[$k]['remark'] = 'string';
                        $insert[$k]['name'] = '拆除设备编号';
                    }else{
                        $insert[$k]['value'] = $v;
                        $insert[$k]['remark'] = 'string';
                        $insert[$k]['name'] = '';
                    }
                }
                unset($replaceInfo['remove_device_number']);
                break;
            case 4:
                unset($replaceInfo['install_device_number']);
                unset($replaceInfo['remove_device_number']);
                unset($replaceInfo['install_software_number']);
                unset($replaceInfo['gh_device_number']);
                $delArr = explode(',',$replaceInfo['xz_software_number']);
                foreach ($delArr as $k => $v){
                    if($k == 0){
                        $insert[$k]['value'] = $v;
                        $insert[$k]['remark'] = 'string';
                        $insert[$k]['name'] = '新增设备编号';
                    }else{
                        $insert[$k]['value'] = $v;
                        $insert[$k]['remark'] = 'string';
                        $insert[$k]['name'] = '';
                    }
                }
                unset($replaceInfo['xz_software_number']);
                break;
            case 5:
                $delArr = explode(',',$replaceInfo['maintain_soft']);
                foreach ($delArr as $k => $v){
                    if($k == 0){
                        $insert[$k]['value'] = $v;
                        $insert[$k]['remark'] = 'string';
                        $insert[$k]['name'] = '设备软件编号';
                    }else{
                        $insert[$k]['value'] = $v;
                        $insert[$k]['remark'] = 'string';
                        $insert[$k]['name'] = '';
                    }
                }
                unset($replaceInfo['maintain_soft']);
                break;

        }
        unset($replaceInfo['shop_id']);
        foreach ($replaceInfo as $k => $v){
            $re[$i]['value'] = $v;
            if($k == 'problem_description'){
                $re[$i]['remark'] = 'text';
            }else{
                $re[$i]['remark'] = 'string';
            }
            if($k == 'address'){
                $re[$i]['name'] = '';

            }else{
                if(isset($fy[$k])){
                    $re[$i]['name'] = $fy[$k];
                }else{
                    $re[$i]['name'] = $k;
                }
            }
            $i++;
        }
        array_splice($re,-1,0,$insert);
        return $re;
    }
    public static function getFieldChinese(){
        return [
            'contacts_name' => '店铺联系人',
            'contacts_mobile' => '手机号码',
            'company_name' => '公司名称',
            'name' => '店铺名称',
            'area_name' => '店铺地址',
            'acreage' => '店铺面积',
            'screen_number' => '安装数量',
            'mirror_account' => '镜面数量',
            'screen_start_at' => '屏幕运行时间',
            'member_name' => '业务合作人',
            'member_mobile' => '联系电话',
            'shop_image' => '店铺门脸照片',
            'panorama_image' => '室内全景照片',
            'remove_device_number' => '拆除屏幕编号',
            'gh_device_number' => '更换旧设备编号',
            //'install_device_number' => '',
            'install_software_number' => '更换新设备编号',
            'xz_software_number' => '新增设备编号',
            'problem_description' => '问题描述',

        ];
    }
    //店主身份查看我的店铺
    public function getMyShop($list = false){
        $shop = $head = [];
        $shop = self::find()->where(['and',['shop_member_id' => \Yii::$app->user->id],['in','shop_operate_type',[1,2]],['status'=>[5,6]]])->select('id,shop_image,name,area_name,address,shop_operate_type')->asArray()->all();
        $head = ShopHeadquarters::find()->where(['corporation_member_id'=>\Yii::$app->user->id,'examine_status'=>1])->select('id,company_name,company_area_name,company_address')->asArray()->all();
        if(!empty($head)){
            foreach ($head as $k => $v){
                $head[$k]['shop_image'] = 'http://i1.bjyltf.com/system/function/head_default_image.jpg';
                $head[$k]['shop_operate_type'] = '4';
                $head[$k]['company_area_name'] = str_replace(['&gt;',' '],'', $v['company_area_name']);
            }
        }
        $shopNum = count($shop);
        $headNum = count($head);
        if(!$list){
            //租赁或自营单个店铺
            if($shopNum == 1 && $headNum == 0){
                return ['shop_type' => '1', 'shop_id'=>strval($shop[0]['id'])];
            }
            //没有店铺
            if($shopNum == 0 && $headNum ==0){
                return ['shop_type' => '2', 'shop_id' => '0'];
            }
            //总店单个
            if($shopNum == 0 && $headNum == 1){
                return ['shop_type' => '3', 'shop_id'=>strval($head[0]['id'])];
            }
            //多家店铺
            if($shopNum >= 1 || $headNum >= 1){
                return ['shop_type' => '4', 'shop_id' => '0'];
            }
        }
        if(!empty($head)){
            foreach ($head as $k => $v){
                $head[$k]['name'] = $v['company_name'];
                $head[$k]['area_name'] = $v['company_area_name'];
                $head[$k]['address'] = $v['company_address'];
            }
        }
        return array_merge($shop, $head);

    }
    //店铺同意协议
    public function agree(){
        $obj = self::find()->where(['shop_member_id'=>Yii::$app->user->id,'id'=>$this->id])->one();
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
    
    /**
     * @param $shop_id
     */
    public function signGetInfo($shop_id){
        return array_merge($this->getTableField('yl_shop','name',['id'=>$shop_id]),$this->getTableField('yl_shop_apply','contacts_name,contacts_mobile,screen_start_at,screen_end_at',['id'=>$shop_id]));
    }
    /**
     * @param $shop_id
     * @param $member_id
     * @param $type 1 member_id 2 admin_member_id 3 shop_member_id
     */
    public function checkAuthShop($shop_id, $member_id, $type){
        if(!self::find()->where(['id'=>$shop_id,$type=>Yii::$app->user->id])->one()){
            return 'ERROR';
        }
        return 'SUCCESS';
    }

    // 判断总部是否有推荐人,如果有责把推荐人信息写到店铺表
    public function setActivityDetailId($head_id)
    {
        if (empty($head_id)) {
            return true;
        }
        $headModel = ShopHeadquarters::find()->where(['id' => $head_id])->select('activity_detail_id')->asArray()->one();
        if (empty($headModel)) {
            return false;
        }
        $this->activity_detail_id = $headModel['activity_detail_id'];
        return true;
    }
    public function getAppliers()
    {
        $shopIds = self::find()->where(['shop_member_id' => Yii::$app->user->id])->select('id')->asArray()->all();
        if (empty($shopIds)) {
            return [];
        }
        $res = ShopApply::find()->where(['id' => $shopIds])->select('apply_name')->distinct()->asArray()->all();
        return array_column($res,'apply_name');
    }

    /**
     * 获取地图数据
     * @return array|\yii\db\ActiveRecord[]
     */
    public function getMaps(){
        $fields = 'id,`name`,area_name,address,screen_number,mirror_account';
        $andWhere = [];
        if($this->is_gaode == '0'){
            $fields .= ',bd_longitude as jd,bd_latitude as wd';
            $andWhere = ['and',['<>','bd_longitude',''],['<>','bd_latitude',''],['<>','screen_number',0],['=','status',5]];
        }else{
            $fields .= ',longitude as jd,latitude as wd';
            $andWhere = ['and',['not',['longitude'=>'']],['not',['latitude'=>'']],['not',['screen_number'=>0]],['=','status',5]];
        }
        return self::find()->where(['like','area',$this->area.'%',false])->andWhere($andWhere)->select($fields)->asArray()->all();
    }

    public function getMapDetail(){
        if(!$obj = self::find()->where(['id'=>$this->id])){
            return [];
        }
        $fields = 'id,`name`,area_name,address,screen_number,mirror_account';
        if($this->is_gaode == '0'){
            $fields .= ',bd_longitude as jd,bd_latitude as wd';
        }else{
            $fields .= ',longitude as jd,latitude as wd';
        }
        return $obj->select($fields)->asArray()->one();
    }


    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getShopMainList(){
        return self::find()->where(['and',['install_member_id'=>Yii::$app->user->id],['>','status','1'],['<','status','5']])->select("id,shop_image,name,area_name,address,status,install_member_id,install_team_id,screen_status,screen_number,install_assign_time as tm,install_assign_at as assign_at")->asArray()->all();
    }
    /*
     * 场景
     * */
    public function scenes()
    {
        return [
            'view'=>[
                'shop_type'=>[
                    'type' => 'int',
                ],
            ],
            'index' => [
                'keyword' => [
                    'type' => 'string',
                ],
                'area'=>[
                    'type' => 'int',
                ],
                'create_at' => [
                    'type' => 'string',

                ],
                'status'=>[
                    'type' => 'int',
                ],
                'screen_status'=>[
                    'type' => 'int',
                ],
                'shop_type'=>[
                    'type' => 'int',
                ],
            ],
            'create'=>[
                'activity_detail_id'=>[],
                'apply_mobile'=>[
                    [

                            'required'=>'1',
                            'result'=>'APPLY_MOBILE_EMPTY'
                    ]
                ],
                'name'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'SHOP_NAME_EMPTY'
                        ],
                        [
                            'function'=>'this::checkRepeatShop',
                            'result'=>'REPEAT_SHOP'
                        ],
                    ]
                ],
                'area'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'AREA_EMPTY'
                        ],
                        [
                            'function'=>'this::checkAreaFormat',
                            'result'=>'AREA_ERROR'
                        ],
                    ]
                ],

                'address'=>[
                    'required'=>'1',
                    'result'=>'ADDRESS_EMPTY'
                ],
                'install_status'=>[
                    'type'=>'int'
                ],
                'acreage'=>[
                    'required'=>'1',
                    'result'=>'ACREAGE_EMPTY'
                ],
                'apply_screen_number'=>[
                    'required'=>'1',
                    'result'=>'APPLY_SCREEN_NUMBER_EMPTY'
                ],
                'mirror_account'=>[
                    'required'=>'1',
                    'result'=>'MIRROR_NUMBER_EMPTY'
                ],
                'shop_image'=>[
                    'required'=>'1',
                    'result'=>'SHOP_IMAGE_EMPTY'
                ],
                'member_mobile'=>[
                    [
                        [
                            'function'=>'this::checkMemberMobile',
                            'result'=>'CREATE_SHOP_MEMBER_MOBILE_ERROR'
                        ],
                    ]
                ],
                'shop_operate_type' => [
                    [
                        [
                            'required' => '1',
                            'result' => 'SHOP_OPERATE_TYPE_EMPTY'
                        ],
                        [
                            'function'=>'this::checkShopOperateType',
                            'result'=>'SHOP_OPERATE_TYPE_ERROR'
                        ],
                    ]
                ],
                'wx_member_id'=>[
                    'type'=>'int'
                ],
                //新加三个字段
                'headquarters_id' => [
                    'function' => 'this::setActivityDetailId',
                    'result' => 'HEADQUARTERS_ID_ERROR'
                ],
                'headquarters_list_id' => [
                    'function' => 'this::checkHeadListId',
                    'result' => 'HEADQUARTERS_LIST_ID_ERROR'
                ],
            ],
            'modify'=>[
                'name'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'SHOP_NAME_EMPTY'
                        ],
                        [
                            'function'=>'this::checkRepeatShop',
                            'result'=>'REPEAT_SHOP'
                        ],
                    ]
                ],
                'area'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'AREA_EMPTY'
                        ],
                        [
                            'function'=>'this::checkAreaFormat',
                            'result'=>'AREA_ERROR'
                        ],
                    ]
                ],
                'address'=>[
                    'required'=>'1',
                    'result'=>'ADDRESS_EMPTY'
                ],
                'acreage'=>[
                    'required'=>'1',
                    'result'=>'ACREAGE_EMPTY'
                ],
                'apply_screen_number'=>[
                    'required'=>'1',
                    'result'=>'APPLY_SCREEN_NUMBER_EMPTY'
                ],
                'mirror_account'=>[
                    'required'=>'1',
                    'result'=>'MIRROR_NUMBER_EMPTY'
                ],
                'shop_image'=>[
                    'required'=>'1',
                    'result'=>'SHOP_IMAGE_EMPTY'
                ],
                //新加三个字段
                'headquarters_id' => [],
                'headquarters_list_id' => [
                    'function' => 'this::checkHeadListId',
                    'result' => 'HEADQUARTERS_LIST_ID_ERROR'
                ],
            ],
            //连锁店修改
            'shop_modify'=>[
                'name'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'SHOP_NAME_EMPTY'
                        ],
                        [
                            'function'=>'this::checkRepeatShop',
                            'result'=>'REPEAT_SHOP'
                        ],
                    ]
                ],
                'area'=>[
                    [
                        'required'=>'1',
                        'result'=>'AREA_EMPTY'
                    ],
                    [
                        'function'=>'this::checkAreaFormat',
                        'result'=>'AREA_ERROR'
                    ],
                ],
                'address'=>[
                    'required'=>'1',
                    'result'=>'ADDRESS_EMPTY'
                ],
                'acreage'=>[
                    'required'=>'1',
                    'result'=>'ACREAGE_EMPTY'
                ],
                'apply_screen_number'=>[
                    'required'=>'1',
                    'result'=>'APPLY_SCREEN_NUMBER_EMPTY'
                ],
                'mirror_account'=>[
                    'required'=>'1',
                    'result'=>'MIRROR_NUMBER_EMPTY'
                ],
                'shop_image'=>[
                    'required'=>'1',
                    'result'=>'SHOP_IMAGE_EMPTY'
                ],
                //新加三个字段
                'headquarters_id' => [],
                'headquarters_list_id' => [
                    'function' => 'this::checkHeadListId',
                    'result' => 'HEADQUARTERS_LIST_ID_ERROR'
                ],
            ],
            'lower' => [
                'keyword' => [
                    'type' => 'string',
                ],
                'area'=>[
                    'type' => 'int',
                ],
                'create_at' => [
                    'type' => 'string',
                ],
                'status'=>[
                    'type' => 'int',
                ],
            ],
            'record' => [
                'member_id' => [
                    'type' => 'int',
                ],
                'wx_member_id'=>[
                    'type'=>'int'
                ],
            ],
            'recordinfo' => [
                'id' => [
                    'required'=>'1',
                    'result'=>'SHOP_ID_EMPTY'
                ]
            ],
            //获取安装历史
            'install' => [
                'install_member_id' => [
                    'required' => '1',
                    'result' => 'MEMBER_ID_EMPTY',
                ],
            ],
            //点击安装列表查看店铺详情
            'shop-detail' => [
                'id' => [
                    'required' => '1',
                    'result' => 'SHOP_ID_EMPTY',
                ],
                'type' => [
                    'required' => '1',
                    'result' => 'MAINTAIN_TYPE_EMPTY',
                ],
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
            //pad map
            'map' => [
                'area' => [
                    'required' => '1',
                    'result' => 'AREA_EMPTY',
                ],
                'is_gaode' => [
                    'default' => '0',
                ],
            ],
            //map detail
            'map-detail' => [
                'id' => [
                    'required' => '1',
                    'result' => 'SHOP_ID_EMPTY',
                ],
            ],
        ];
    }
}
