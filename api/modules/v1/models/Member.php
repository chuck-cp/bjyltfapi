<?php

namespace api\modules\v1\models;
use Yii;
use yii\base\Exception;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;
/**
 * member表的model
 */
class Member extends \api\core\ApiActiveRecord implements IdentityInterface
{
    public $password;
    public $repeat_password;
    public $verify;
    public $push_id;
    public $equipment_type;
    public $equipment_number;
    /**
     * 表名
     */
    public static function tableName()
    {
        return '{{%member}}';
    }

    public static function findIdentity($id){
        return static::findOne(['id' => $id]);
    }

    public static function findIdentityByAccessToken($token, $type = null){
        $memberModel = MemberEquipment::find()->where(['token'=>$token])->select('member_id')->asArray()->one();
        if(empty($memberModel)){
            return false;
        }
        return static::findOne(['id' => $memberModel['member_id'],'status'=>1]);
    }

    public function getId(){
        return $this->getPrimaryKey();
    }

    public function getAuthKey(){

    }

    public function validateAuthKey($authKey){
        return $this->getAuthKey() === $authKey;
    }

    /*
     * 验证手机号是否正确
     * */
    public static function checkMobile($mobile){
        if(empty($mobile)){
            return false;
        }
        return Member::find()->where(['id'=>Yii::$app->user->id,'mobile'=>$mobile])->count();
    }

    /*
     * 注册
     * @param object 上级用户的model
     * */
    public function register(){
        $dbTrans = Yii::$app->db->beginTransaction();
        try{
            $this->name = $this->mobile;
            $this->create_at = date('Y-m-d H:i:s');
            $this->save();

            $equipmentModel = new MemberEquipment();
            $equipmentModel->token = $this->generateAccessToken();
            $equipmentModel->member_id = $this->id;
            $equipmentModel->push_id = $this->push_id;
            $equipmentModel->equipment_number = $this->equipment_number;
            $equipmentModel->equipment_type = $this->equipment_type;
            $equipmentModel->save();

            $functionModel = new MemberFunction();
            if(!$functionModel->checkMyFunction($this->id)){
                $functionModel->loadDefaultFunction($this->id);
                $functionModel->save();
            }

            //判断该用户是否有店铺但未给佣金
            $shopModel = new Shop();
            if(!$shopModel->updateShopMemberId($this->mobile,$this->id)){
                throw new Exception("[error]更新店主ID失败");
            }
            $dbTrans->commit();
            return [
                'token'=>$equipmentModel->token,
                'id'=>$this->id
            ];
        }catch (Exception $e){
            $dbTrans->rollBack();
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }

    /*
     * 修改密码
     * */
    public function updatePassword(){
        try{
            $memberModel = self::find()->where(['mobile'=>$this->mobile])->select('id')->asArray()->one();
            if(empty($memberModel)){
                return false;
            }
            $new_token = $this->generateAccessToken();
            if(!MemberEquipment::find()->where(['member_id'=>$memberModel['id'],'equipment_number'=>$this->equipment_number])->count()){
                $equipmentModel = new MemberEquipment();
                $equipmentModel->member_id = $memberModel['id'];
                $equipmentModel->push_id = (int)$this->push_id;
                $equipmentModel->equipment_number = $this->equipment_number;
                $equipmentModel->equipment_type = $this->equipment_type;
                $equipmentModel->token = $new_token;
                $equipmentModel->save();
            }else{
                MemberEquipment::updateAll(['status'=>1,'token'=>$new_token],['member_id'=>$memberModel['id'],'equipment_number'=>$this->equipment_number]);
            }
            $equipmentModel['id'] = $memberModel['id'];
            $equipmentModel['token'] = $new_token;
            return $equipmentModel;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }

    /*
     * 退出
     * */
    public function logout(){
        return MemberEquipment::updateAll(['status'=>2],['member_id'=>Yii::$app->user->id,'equipment_number'=>$this->equipment_number]);
    }

    /*
     * 登陆
     * @param string old_mobile 旧手机号
     * */
    public function login($old_mobile = ''){
        $userModel = self::find()->where(['mobile'=>$this->mobile])->select('id,create_at,name')->asArray()->one();
        if(empty($userModel)){
            if($old_mobile){
                //如果有旧手机号,说明在上次修改手机号时失败,继续执行修改操作
                if(!$userModel = $this->updateMobile($old_mobile)){
                    return false;
                }
            }else{
                return $this->register();
            }
        }
        $dbTrans = Yii::$app->db->beginTransaction();
        try{
            $equipmentModel = MemberEquipment::find()->where(['member_id'=>$userModel['id'],'equipment_number'=>$this->equipment_number])->select('token')->asArray()->one();
            MemberEquipment::updateAll(['status'=>2],['equipment_number'=>$this->equipment_number]);
            $new_token = $this->generateAccessToken();
            if(empty($equipmentModel)){
                $userModel['equipment_number'] = $this->equipment_number;
                $equipmentModel = new MemberEquipment();
                $equipmentModel->member_id = $userModel['id'];
                $equipmentModel->push_id = $this->push_id;
                $equipmentModel->equipment_number = $this->equipment_number;
                $equipmentModel->equipment_type = $this->equipment_type;
                $equipmentModel->token = $new_token;
                $equipmentModel->save();
            }else{
                MemberEquipment::updateAll(['status'=>1,'token'=>$new_token],['member_id'=>$userModel['id'],'equipment_number'=>$this->equipment_number]);
            }
            $dbTrans->commit();
        }catch (Exception $e){
            $dbTrans->rollBack();
            Yii::error("[login]".$e->getMessage(),'db');
            return false;
        }
        $userModel['token'] = $new_token;
        unset($userModel['equipment_number']);
        unset($userModel['create_at']);
        return $userModel;
    }

    /*
     * 验证Token
     * */
    public function getToken($userModel = ''){
        $userModel = empty($userModel) ? Yii::$app->user->identity : $userModel;
        return md5($userModel['id'].$userModel['equipment_number'].$userModel['create_at'].md5('wwwbjyltfcom'));
    }
    /*
     * 生成随机token
     */
    public function generateAccessToken()
    {
        return Yii::$app->security->generateRandomString();
    }

    /*
     * 获取用户信息
     * */
    public function getInfo(){
        $infoModel = MemberInfo::find()->where(['member_id'=>Yii::$app->user->id])->select('examine_status,electrician_examine_status')->asArray()->one();
        $memberModel = self::find()->where(['id'=>Yii::$app->user->id])->select('school,emergency_contact_relation,emergency_contact_mobile,emergency_contact_name,education,school,area,area_name,address,mobile,name,member_type,avatar,parent_id')->asArray()->one();
        if(isset($memberModel['area'])){
            $memberModel['district'] = SystemAddress::getAreaNameById(substr($memberModel['area'],0,9));
        }
        if(empty($infoModel)){
            $memberModel['electrician_examine_status'] = "-1";
            $memberModel['examine_status'] = "-1";
        }else{
            $memberModel['examine_status'] = $infoModel['examine_status'];
            $memberModel['electrician_examine_status'] = $infoModel['electrician_examine_status'];
        }
        $orderModel = Order::find()->select('payment_status,count(*) as order_number')->where(['member_id' => Yii::$app->user->id])->groupBy('payment_status')->asArray()->all();
        if(!empty($orderModel)){
            $orderModel = ArrayHelper::map($orderModel,'payment_status','order_number');
        }
        $memberModel['waiting_order_number'] = isset($orderModel[0]) ? $orderModel[0] : 0;
        if (!isset($orderModel[1])) {
            $orderModel[1] = 0;
        }
        if (!isset($orderModel[2])) {
            $orderModel[2] = 0;
        }
        $memberModel['waiting_back_order_number'] = $orderModel[1] + $orderModel[2];
        $memberModel['finished_order_number'] = isset($orderModel[3]) ? $orderModel[3] : 0;
        return $memberModel;
    }

    /*
     * 获取用户状态
     * */
    public static function getMemberType(){
        $memberModel = Member::find()->where(['id'=>Yii::$app->user->id])->select('member_type')->asArray()->one();
        return $memberModel['member_type'];
    }

    /*
     * 获取店铺申请人信息
     * */
    public function getMemberByShop($member_id){
        return self::find()->where(['id'=>$member_id])->select('name,mobile')->asArray()->one();
    }

    /*
     * 检查上级编号
     * */
    public function getMemberByMobile($mobile){
        if(empty($mobile)){
            return false;
        }
        $res = self::find()->where(['mobile'=>$mobile])->select('id,parent_id,name')->asArray()->one();
        if(empty($res)){
            return $res['parent_id'];
        }
        return false;
    }

    /*检查上级编号*/
    public function checkMemberParentMobile($mobile){
        if(empty($mobile)){
            return true;
        }
        $model = self::find()->where(['mobile'=>$mobile])->select('id')->asArray()->one();
        if(empty($model)){
            return false;
        }
        $this->parent_id = $model['id'];
        return true;
    }

    /*
     * 获取用户名称
     * */
    public function getMemberName($member_id){
        if($memberModel = self::find()->where(['id'=>$member_id])->select('name')->asArray()->one()){
            return $memberModel['name'];
        }
    }

    /*
     * 获取下级的用户信息
     * */
    public function getLowerMemberById($member_id){
        return self::find()->where(['id'=>$member_id])->select('name,mobile,avatar,id,parent_id')->asArray()->one();
    }

    /*
     * 获取上级用户信息
     * */
    public function getParentMemberById($member_id){
        return self::find()->where(['id'=>$member_id])->select('id,name,avatar')->asArray()->one();
    }

    /*
     * 获取我的地区
     * */
    public function getMemberArea(){
        return self::find()->where(['id'=>Yii::$app->user->id])->select('name,mobile,avatar,admin_area,member_type')->asArray()->one();
    }

    /*
     * 获取我的某个字段
     * */
    public static function getMemberFieldById($field){
        $memberModel = Member::find()->where(['id'=>Yii::$app->user->id])->select($field)->asArray()->one();
        if(!empty($memberModel)){
            return $memberModel[$field];
        }
    }

    /*
     * 获取我的某个字段
     * */
    public static function getMemberFieldByWhere($where,$field){
        $memberModel = Member::find()->where($where)->select($field)->asArray()->one();
        if(!empty($memberModel)){
            return $memberModel[$field];
        }
    }

    /*
     * 修改手机号
     * @param string mobile 原手机号
     * */
    public function updateMobile($mobile){
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            $memberModel = Member::find()->where(['mobile'=>$mobile])->select('id,name')->asArray()->one();
            if(empty($memberModel)){
                throw new Exception("手机号不存在");
            }
            Member::updateAll(['mobile'=>$this->mobile],['id'=>$memberModel['id']]);
            $shopModel = new Shop();
            if(!$shopModel->updateShopMemberId($this->mobile,$memberModel['id'],$memberModel['name'])){
                throw new Exception("更新店铺数据失败");
            }
            $dbTrans->commit();
            return $memberModel;
        } catch (Exception $e) {
            Yii::error($e->getMessage());
            $dbTrans->rollBack();
            return false;
        }
    }

    /*
     * 修改管理的地区
     * */
    public function updateArea(){
        $memberModel = self::findOne(Yii::$app->user->id);
        /*****
        if($memberModel->member_type != 2){
            return 'NOT_PUT_ADMIN_AREA_PERMISSION';
        }
         ******/
        if($memberModel->admin_area){
            return 'PUT_ADMIN_AREA_ERROR';
        }

        if(!MemberAreaCount::find()->where(['member_id'=>Yii::$app->user->id,'area'=>$this->admin_area])->count()){
            return 'PUT_ADMIN_AREA_NOT_EXIST';
        }
        $memNum = Shop::getMemNumber(\Yii::$app->user->id);
        /******/
        $configNum = SystemConfig::getConfig('shop_number');
        if($memNum < $configNum){
            return 'NOT_PUT_ADMIN_AREA_PERMISSION';
        }
        /******/
        $transaction = Yii::$app->db->beginTransaction();
        try{
            //达到安装数量后一次性发放红包给联系人
            /***
            $totalPrice = $memNum * 5000;
            if(!LogAccount::writeLog($totalPrice,1,'业务合作人红包','',Yii::$app->user->id)){
                throw new Exception("[error]创建收入日志失败");
            }
            //确定此时应该给哪一个店铺加钱
            $shopObj = Shop::getShopByCondition(['member_id'=>Yii::$app->user->id, 'status'=>5], 'id');
            if(!$shopObj){
                throw new Exception("[error]未找到加钱的店铺");
            }
            $shopModel = Shop::findOne($shopObj['id']);
            $shopModel->member_reward_price = $totalPrice;
            $shopModel->save();
            ***/

            $memberModel->admin_area = $this->admin_area;
            $memberModel->member_type = 2;
            $memberModel->save();
            //查看该业务员有没有冻结金额，如果有解冻
            if(!MemberAccount::unfreezeMoneyById(Yii::$app->user->id)){
                throw new Exception("[error]解冻冻结金额失败");
            }
            $transaction->commit();
            return 'SUCCESS';
        }catch (Exception $e){
            $transaction->rollBack();
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }
    /*
     * 获取我的下级伙伴
     * */
    public function getMemberLower(){
        $memberModel = self::find()->where(['parent_id'=>Yii::$app->user->id])->andFilterWhere(['like','name',$this->name])->select('id,name,avatar');
        $pagination = new Pagination(['totalCount'=>$memberModel->count()]);
        $pagination->validatePage = false;
        $memberModel = $memberModel->orderBy("id asc")->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        if(empty($memberModel)){
            return [];
        }
        return $memberModel;
    }
    public function areaName(){
        $this->area_name = SystemAddress::getAreaNameById($this->area,'ALL');
        return true;
    }
    //获取没有加入维护或者业务团队的
    public static function getNotAddSignTeamMembers(){
        $data = self::find()->where(['status'=>1, 'inside'=>1, 'sign_team_id'=>0])->orderBy('name_prefix ASC')->select('id,`name`, avatar, mobile,name_prefix')->asArray()->all();
        $nrr = [];
        if(!empty($data)){
            $sortArr = array_values(array_column($data,'name_prefix','name_prefix'));
            foreach ($sortArr as $k => $v){
                foreach ($data as $kk => $vv){
                    $nrr[$k]['title'] = $v;
                    if($vv['name_prefix'] == $v){
                        if($vv['name_prefix']){
                            $vv['name'] = $vv['name'].' ('.$vv['mobile'].')';
                        }
                        $nrr[$k]['items'][] = $vv;
                        unset($data[$kk]);
                    }
                }
            }
        }
        return $nrr;
    }

    /**
     *为member写入邀请人
     */
    public function addInviteCode(){
        $member = self::find()->where(['mobile'=>$this->mobile])->select('id,inside,name,avatar')->asArray()->one();
        if(empty($member)){
            return 'INVITE_MEMBER_NOT_EXIST';
        }
        if($member['id'] == Yii::$app->user->id){
            return 'INVITE_MEMBER_CAN_NOT_SELF';
        }
        if($member['inside'] == 0){
            return 'INVITE_MEMBER_MUST_BE_INSIDE';
        }
        if(self::findOne(Yii::$app->user->id)->parent_id == $member['id']){
            return 'THIS_INVITE_IS_REPEAT';
        }
        //邀请人和被邀请人之间不能互为上下级
        $data = self::find()->where(['id'=>$member['id'], 'parent_id'=>Yii::$app->user->id])->count();
        if($data){
            return 'INVITER_AND_INIVTEE_CON_NOT_BE_EACH_OTHER';
        }
        $res = self::updateAll(['parent_id'=>$member['id']], ['id'=>Yii::$app->user->id]);
        $this->name = $member['name'];
        $this->avatar = $member['avatar'];
        return $res ? 'SUCCESS' : 'ERROR';
    }

    public static function getInivter(){
        $my = self::find()->where(['id' => Yii::$app->user->id])->select('parent_id,mobile')->asArray()->one();
        $inivter = self::find()->where(['id'=>$my['parent_id']])->select('name,avatar,mobile')->asArray()->one();
        return [
           'my_mobile' => $my['mobile'],
           'inviter_mobile' => $inivter['mobile'],
           'inviter_name' => $inivter['name'],
           'inviter_avatar' => $inivter['avatar'],
        ];
    }

    public static function getInivterName($mobile){
        if(Yii::$app->user->identity->mobile == $mobile){
            return '';
        }
        $member = self::find()->where(['mobile'=>$mobile, 'inside'=>1])->select('name')->asArray()->one();
        return empty($member) ? '' : $member['name'];
    }
    /*
     * 场景
     * */
    public function scenes(){
        return [
            'login'=>[
                'mobile'=>[
                    'required'=>'1',
                    'result'=>'MOBILE_EMPTY'
                ],
                'password'=>[
                    'required'=>'1',
                    'result'=>'PASSWORD_EMPTY'
                ],
                'equipment_number'=>[
                    'required'=>'1',
                    'result'=>'EQUIPMENT_NUMBER_EMPTY'
                ],
                'equipment_type'=>[
                    'required'=>'1',
                    'result'=>'EQUIPMENT_TYPE_EMPTY'
                ],
                'push_id'=>[
                    'type'=>'string',
                    'default'=>'0',
                ],
            ],
            'logout'=>[
                'equipment_number'=>[
                    'required'=>'1',
                    'result'=>'EQUIPMENT_NUMBER_EMPTY'
                ],
            ],
            'register'=>[
                'mobile'=>[
                    'required'=>'1',
                    'result'=>'MOBILE_EMPTY',
                ],
                'parent_id'=>[
                    [
                      [
                          'function'=>'this::checkMemberParentMobile',
                          'result'=>'PARENT_NUMBER_ERROR'
                      ]
                    ],
                    [
                        'type'=>'int',
                        'default'=>0
                    ]
                ],
                'password'=>[
                    'required'=>'1',
                    'result'=>'PASSWORD_EMPTY'
                ],
                'verify'=>[
                    'required'=>'1',
                    'result'=>'VERIFY_EMPTY'
                ],
                'equipment_number'=>[
                    'required'=>'1',
                    'result'=>'EQUIPMENT_NUMBER_EMPTY'
                ],
                'equipment_type'=>[
                    'required'=>'1',
                    'result'=>'EQUIPMENT_TYPE_EMPTY'
                ],
                'push_id'=>[
                    'type'=>'string',
                    'default'=>'0',
                ],
            ],
            'password'=>[
                'mobile'=>[
                    'required'=>'1',
                    'result'=>'MOBILE_EMPTY'
                ],
                'password'=>[
                    'required'=>'1',
                    'result'=>'PASSWORD_EMPTY'
                ],
                'repeat_password'=>[
                    'required'=>'1',
                    'result'=>'REPEAT_PASSWORD_EMPTY'
                ],
                'verify'=>[
                    'required'=>'1',
                    'result'=>'VERIFY_EMPTY'
                ],
                'equipment_number'=>[
                    'required'=>'1',
                    'result'=>'EQUIPMENT_NUMBER_EMPTY'
                ],
            ],
            'update'=>[
                'emergency_contact_relation'=>[],
                'emergency_contact_mobile'=>[],
                'emergency_contact_name'=>[],
                'education'=>[],
                'school'=>[],
                'avatar'=>[],
                'area'=>[
                    [
                        [
                            'function'=>'this::areaName'
                        ]
                    ]
                ],

                'address'=>[],
            ],
            'update-area'=>[
                'admin_area'=>[
                    'required'=>'1',
                    'result'=>'ADMIN_AREA_EMPTY'
                ],
            ],
            'lower-index' => [
                'name' => [
                ],
            ],
            'update-mobile'=>[
                'mobile'=>[
                    'required'=>'1',
                    'result'=>'MOBILE_EMPTY'
                ],
                'password'=>[
                    'required'=>'1',
                    'result'=>'PASSWORD_EMPTY'
                ],
                'verify'=>[
                    'required'=>'1',
                    'result'=>'VERIFY_EMPTY'
                ],
            ],
            'invite-code'=>[
                'mobile'=>[
                    'required'=>'1',
                    'result'=>'MOBILE_EMPTY'
                ],
            ],
        ];
    }

    /**
     * Returns static class instance, which can be used to obtain meta information.
     * @param bool $refresh whether to re-create static instance even, if it is already cached.
     * @return static class instance.
     */
    public static function instance($refresh = false)
    {
        // TODO: Implement instance() method.
    }
}
