<?php

namespace api\modules\v1\models;
use api\core\ApiActiveRecord;
use common\libs\ToolsClass;
use Yii;
use yii\data\Pagination;
use yii\db\Expression;

class Sign extends ApiActiveRecord
{

    public $qualified; // 今日签到是否达标
    public $member_type;
    public $earliest_closing_time;
    public static function tableName()
    {
        return '{{%sign}}';
    }


    /*
     * 检查是否超时签到
     * @param first_sign_time string 首次签到最晚时间
     * */
    public function checkLateSign($first_sign_time) {
        $lateSecond = time() - strtotime(date('Y-m-d') .' '.$first_sign_time);
        if ($lateSecond > 0) {
            return (int)($lateSecond / 60);
        }
        return false;
    }

    /*
     * 检查两次签到中间的间隔时间
     * @param sign_interval_time string 系统设置的间隔时间(分钟)
     * @param last_sign_time date 最后第一次签到时间
     * */
    public function checkSignIntervalTime($sign_interval_time,$last_sign_time) {
        return floor((time() - strtotime($last_sign_time))/60) < $sign_interval_time;
    }

    // 加载签到数据
    public function loadSignData() {
        $userIdentity = \Yii::$app->user->identity;
        $this->member_id = $userIdentity->getId();
        $this->member_name = $userIdentity->name;
        $this->member_avatar = $userIdentity->avatar;
        // 获取团队信息
        $teamModel = SignTeamMember::getTeam();
        if (empty($teamModel)) {
            return 'NOT_JOIN_SIGN_TEAM';
        }
        $this->team_id = $teamModel['id'];
        $this->team_type = $teamModel['team_type'];
        $this->team_member_type = $teamModel['member_type'];
        $this->team_name = $teamModel['team_name'];
        # 获取最早签退时间
        $this->earliest_closing_time = $teamModel['earliest_closing_time'];
        if (empty($this->earliest_closing_time)) {
            if ($this->team_type == 1) {
                $this->earliest_closing_time = SystemConfig::getConfig('salesman_earliest_closing_time','17:00:00');
            } else {
                $this->earliest_closing_time = SystemConfig::getConfig('maintain_earliest_closing_time','17:00:00');
            }
        }
        // 获取用户当日签到的数据
        $signMemberCount = SignMemberCount::getSignData($teamModel['id']);
        if ($signMemberCount['sign_number'] == 0) {
            // 判断是否是首次签到
            $this->first_sign = 1;
            if ($lateTime = $this->checkLateSign($teamModel['first_sign_time'])) {
                $this->late_sign = 1;
                $this->late_time = $lateTime;
            }
        } else {
            // 判断签到间隔时间
            if ($this->checkSignIntervalTime($teamModel['sign_interval_time'],$signMemberCount['update_at'])) {
                return 'SIGN_INTERVAL_TIME_ERROR';
            }
            // 判断是否达标
            if ($teamModel['sign_qualified_number'] - $signMemberCount['sign_number'] == 1) {
                $this->qualified = 1;
            }
        }
    }

    public function createSign() {
        try {
            $this->save();
            $update['sign_number'] = new Expression('sign_number + 1');
            if ($this->qualified == 1) {
                $update['qualified'] = 1;
            }
            if ($this->late_sign == 1) {
                $update['late_sign'] = 1;
            }
            $this->create_at = date('Y-m-d H:i:s');
            if (strtotime($this->create_at) >= strtotime(date('Y-m-d').' '.$this->earliest_closing_time)) {
                $update['leave_early'] = 2;
            }
            SignMemberCount::updateAll($update,['member_id' => \Yii::$app->user->id, 'team_id' => $this->team_id, 'create_at' => date('Y-m-d')]);
            return true;
        } catch (\Exception $e) {
            \Yii::error($e->getMessage());
            return false;
        }
    }

    //个人签到详情（列表）
    public function getSignData(){
        if (!$this->create_at) {
            $this->create_at = date('Y-m');
        }
        $result = [];
        if(!$this->member_id){
            $member_id = \Yii::$app->user->id;
        }else{
            $member_id = $this->member_id;
        }
        $memberObj = Member::findOne($member_id);
        $result['member_name'] = $memberObj->getAttribute('name');
        $result['avatar'] = $memberObj->getAttribute('avatar');
        $teamInfo = $this->getTableField('yl_sign_team_member', 'team_id', ['member_id' => $this->member_id]);
        $this->team_id = $teamInfo['team_id'];
        $tobj = SignTeam::findOne($this->team_id);
        $result['team_name'] = $tobj ? $tobj->getAttribute('team_name') : '尚未加入任何团队';
        $totalData = Sign::find()->where(['member_id' => $member_id,'left(create_at,7)'=>$this->create_at])->select(new Expression('id,team_type,member_id,member_name,member_avatar,shop_name,shop_address,first_sign,late_sign,create_at,substr(create_at,1,10) as rq,substr(create_at,11,6) as tm'))->orderBy('create_at DESC');
        $result['total_num'] = $totalData->count();
        $pagination = new Pagination(['totalCount' => $totalData->count(), 'pageSize' => 10]);
        $pagination->validatePage = false;
        $re = $totalData->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        if (!empty($re)) {
            $arr = array_merge(array_unique(array_column($re, 'rq')));
            foreach ($re as $k => $v) {
                if ($v['team_type'] == 2) {//维护
                    $evaluate = $this->getTableField('yl_sign_maintain', 'evaluate', ['sign_id' => $v['id']]);
                    $re[$k]['evaluate'] = empty($evaluate) ? '0' : strval($evaluate['evaluate']);
                }else{
                    $re[$k]['evaluate'] = '';
                }
            }
            foreach ($arr as $k => $v) {
                foreach ($re as $kk => $vv) {
                    $result['data'][$k]['time'] = $v;
                    if ($v == $vv['rq']) {
                        $result['data'][$k]['items'][] = $vv;
                    }
                }
            }
        }
        return $result;
    }

    //个人单次签到详情
    public function singleDetail(){
        $data = self::find()->where(['id'=>$this->id])->asArray()->one();
        $image = SignImage::find()->where(['sign_id'=>$this->id])->select('image_url')->asArray()->one();
        $image['image_url'] = explode(',',$image['image_url']);
        $data['content'] = '';
        if($data['team_type'] == 1){
            $info = SignBusiness::find()->where(['sign_id'=>$this->id])->select('shop_acreage,shop_mirror_number,screen_number,minimum_charge,longitude,latitude,shop_acreage,contacts_mobile,shop_type,description,screen_brand_name')->asArray()->one();
            $data['shop_acreage'] = $info['shop_acreage'].'平方米';
            $data['shop_mirror_number'] = $info['shop_mirror_number'].'面';
            $data['screen_number'] = $info['screen_number'] == '0' ? '无' : $info['screen_number'].'台';
            $data['minimum_charge'] = $info['minimum_charge'].'元';
            $data['contacts_mobile'] = empty($info['contacts_mobile']) ? '无' : $info['contacts_mobile'];
            $data['longitude'] = $info['longitude'];
            $data['latitude'] = $info['latitude'];
            $data['shop_type'] = $info['shop_type'];
            $data['description'] = empty($info['description']) ? '无' : $info['description'];
            $data['screen_brand_name'] = $info['screen_brand_name'];
        }elseif ($data['team_type'] == 2){
            $crr = array_column(SignMaintain::getMainTainContent(),'content','id');
            $info = SignMaintain::find()->where(['sign_id'=>$this->id])->select('maintain_content,longitude,latitude,contacts_name,description,contacts_mobile,description,evaluate,evaluate_description,screen_start_at,screen_end_at')->asArray()->one();
            $info['description'] = $info['description'] ?? '无';
            $info['contacts_mobile'] = $info['contacts_mobile'] ?? '无';
            $data = array_merge($data,$info);
            $data['maintain_content'] = explode(',',$info['maintain_content']);
            $filterData = array_filter($data['maintain_content']);
            $last_key = count($filterData);
            if(!empty($filterData)){
                foreach ($filterData as $k => $v){
                    if($k !== ($last_key-1)){
                        if($v != 6){
                            $data['content'] .= $crr[$v].'、';
                        }else{
                            $data['content'] .= $crr[$v].':'.$info['screen_start_at'].'-'.$info['screen_end_at'].'、';
                        }

                    }else{
                        if($v == 6){
                            $data['content'] .= $crr[$v].':'.$info['screen_start_at'].'-'.$info['screen_end_at'];
                        }else{
                            $data['content'] .= $crr[$v];
                        }

                    }
                }
                unset($data['maintain_content']);
            }
        }
        //$data['team_name'] = SignTeam::findOne($data['team_id'])->getAttribute('team_name');
        return array_merge($data,$image);
    }

    //获取团队足迹
    public function getFootmark(){
        $data = [
            'team_name' => '尚未加入任何团队',
            'sign_data' => [],
            'total' => '0',
            'not_sign' => '0',
            'team_id' => '0',
            'member_type'=>'0',
            'sign_data_permission' => '1'
        ];
        $sign_team_admin = Yii::$app->user->identity->sign_team_admin;
        if ($sign_team_admin == 1) {
            $data['member_type'] = '3';
            // 管理员
            if ($this->team_id == 'business'){
                $data['team_name'] = '全部业务组';
                $data['team_id'] = 'business';
                $where['team_type'] = 1;
                $noSignWhere['team_type'] = 1;
            } elseif ($this->team_id == 'maintain'){
                $data['team_name'] = '全部维护组';
                $data['team_id'] = 'maintain';
                $where['team_type'] = 2;
                $noSignWhere['team_type'] = 2;
            } elseif (!empty($this->team_id)) {
                $teamModel = SignTeam::find()->where(['id' => $this->team_id])->asArray()->one();
                if (empty($teamModel)) {
                    return $data;
                }
                $data['team_name'] = $teamModel['team_name'];
                $data['team_id'] = $this->team_id;
                $where['team_id'] = $this->team_id;
                $noSignWhere['team_id'] = $this->team_id;
            } else {
                $teamModel = SignTeamMember::getTeam();
                // 获取我当前的组
                if (empty($teamModel)) {
                    $data['team_name'] = '全部业务组';
                    $data['team_id'] = 'business';
                    $where['team_type'] = 1;
                    $noSignWhere['team_type'] = 1;
                } else {
                    $data['team_name'] = $teamModel['team_name'];
                    $data['team_id'] = $teamModel['id'];
                    $where['team_id'] = $teamModel['id'];
                    $noSignWhere['team_id'] = $teamModel['id'];
                }
            }
        } else {
            // 普通用户
            $teamModel = SignTeamMember::getTeam();
            $data['member_type'] = strval(SignTeamMember::getMemberType(Yii::$app->user->id));
            // 获取我当前的组
            if (empty($teamModel)) {
                $data['total'] = '0';
                $data['sign_data_permission'] = '0';
                $data['sign_data'] = [];
                return $data;
            }else{
                $data['team_name'] = $teamModel['team_name'];
                $data['team_id'] = $teamModel['id'];
                $noSignWhere['team_id'] = $teamModel['id'];
                $data['sign_data_permission'] = $teamModel['sign_data_permission'];
                $where['team_id'] = $teamModel['id'];
                if ($data['sign_data_permission'] == 0 && $teamModel['member_type'] == 1) {
                    $where['member_id'] = Yii::$app->user->id;
                }
            }

        }
        /****************************************************/
        if(!$this->create_at){
            $this->create_at = ToolsClass::getDate(false,false);
        }
        $where['date(create_at)'] = $this->create_at;
        $signBusiness = Sign::find()->where($where)->select('id,member_name,member_avatar,member_id,shop_name,team_id,shop_address,late_sign,team_type,create_at')->orderBy('create_at desc');
        $pagination = new Pagination(['totalCount'=>$signBusiness->count(),'pageSize'=>10]);
        $pagination->validatePage = false;
        $data['sign_data'] = $signBusiness->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        foreach ($data['sign_data'] as $k => $v){
            $data['sign_data'][$k]['create_at'] = substr($v['create_at'],11,5);
        }
        if (isset($where['member_id'])) {
            unset($where['member_id']);
        }
        $noSignWhere['create_at'] = $this->create_at;
        $noSignWhere['sign_number'] = 0;
        $data['now_date'] = $this->create_at;
        $data['total'] = Sign::find()->where($where)->count();
        $data['not_sign'] = strval(SignMemberCount::find()->where($noSignWhere)->count());
        return $data;
    }
    //获取团队签到数据(全部)
    public function getTeamAllData(){
        if(!$this->create_at){ $this->create_at = date('Y-m-d'); }
        $memberType = SignTeamMember::getMemberType(Yii::$app->user->id);
        $teamType = SignTeamMember::getTeamType();
        if($this->team_id == 'business'){
            if($memberType != 3){
                return 'CAN_NOT_LOOK_OTHER';
            }
            $data = Sign::find()->where(['team_type'=>1,'left(create_at,10)'=>$this->create_at])->select('id,team_name,shop_name,team_type,create_at')->asArray()->all();
        }elseif ($this->team_id == 'maintain'){
            if($memberType != 3){
                return 'CAN_NOT_LOOK_OTHER';
            }
            $data = Sign::find()->where(['team_type'=>2,'left(create_at,10)'=>$this->create_at])->select('id,team_name,shop_name,team_type,create_at')->asArray()->all();
        }else{
            if($memberType == 1){
                //普通用户判断权限
                $team_id = SignTeamMember::getTeamId(Yii::$app->user->id);
                if(!$team_id){ return false; }
                $permission = SignTeam::findOne($team_id)->getAttribute('sign_data_permission');
                if($permission == 0){
                    return 'CAN_NOT_LOOK_OTHER';
                }
            }
            $data = Sign::find()->where(['team_id'=>$this->team_id,'left(create_at,10)'=>$this->create_at])->select('id,team_name,shop_name,team_type,create_at')->asArray()->all();
        }
        if(!empty($data)){
            foreach ($data as $k => $v){
                if($v['team_type'] ==1){
                    $info = SignBusiness::find()->where(['sign_id'=>$v['id']])->asArray()->one();
                    $data[$k]['longitude'] = $info['longitude'];
                    $data[$k]['latitude'] = $info['latitude'];
                }elseif ($v['team_type'] ==2){
                    $info = SignMaintain::find()->where(['sign_id'=>$v['id']])->asArray()->one();
                    $data[$k]['longitude'] = $info['longitude'];
                    $data[$k]['latitude'] = $info['latitude'];
                }
            }
        }
        return ['total'=>strval(count($data)),'sign_data'=>$data];
    }
    //签到数据详情  1、超时签到人数 2、未达标人数 3、未签到人数 4、中评人数 5、差评人数 6、早退人数
    public function getMembersView(){
        $where = ['create_at' => $this->create_at];
        $orderBy = '';
        $field = '';
        $sort = ' DESC';
        $model = '';
        switch ($this->member_type){
            case 1:
                $field = 'overtime_sign_member_number';
                $orderBy = $field.$sort;
                break;
            case 2:
                $field = 'unqualified_member_number';
                $orderBy = $field.$sort;
                break;
            case 3:
                $field = 'no_sign_member_number';
                $orderBy = $field.$sort;
                break;
            case 4:
                $field = 'middle_evaluate_number';
                $orderBy = $field.$sort;
                break;
            case 5:
                $field = 'bad_evaluate_number';
                $orderBy = $field.$sort;
                break;
            case 6:
                $field = 'leave_early_number';
                $orderBy = $field.$sort;
                break;
        }
        $andWhere = ['>',$field,0];
        $data = [];
        if($this->team_id == 'business'){
            $data = SignTeamBusinessCount::find()->where($where)->andWhere($andWhere)->orderBy($orderBy)->asArray()->all();
        }elseif ($this->team_id == 'maintain'){
            $data = SignTeamMaintainCount::find()->where($where)->andWhere($andWhere)->orderBy($orderBy)->asArray()->all();
        }else{
            $where['team_id'] = $this->team_id;
            $teamType = SignTeam::getTeamType($this->team_id);
            $model = $teamType == 1 ? (new SignTeamBusinessCount()) : (new SignTeamMaintainCount());
            $data = $model->find()->where($where)->andWhere($andWhere)->orderBy($orderBy)->asArray()->all();
        }
        $arr = [];

        if(!empty($data)){
            foreach ($data as $k => $v){
                //团队名称
                $arr[$k]['team_name'] = SignTeam::findOne($v['team_id'])->getAttribute('team_name');
                //团队类型
                $teamType = SignTeam::getTeamType($v['team_id']);
                if($this->member_type == 1 && $teamType == 1){//业务签到超时  SignBusiness
                    $arr[$k]['items'] = Sign::find()->where(['team_id'=>$v['team_id'], 'late_sign'=>1,'left(create_at,10)' => $this->create_at])->select('id,team_id,member_id,member_name as name,member_avatar as avatar')->asArray()->all();
                }elseif ($this->member_type == 1 && $teamType == 2){//维护签到超时  SignMaintain
                    $arr[$k]['items'] = Sign::find()->where(['team_id'=>$v['team_id'], 'late_sign'=>1,'left(create_at,10)' => $this->create_at])->select('id,team_id,member_id,member_name as name,member_avatar as avatar')->asArray()->all();
                }elseif ($this->member_type == 2 && $teamType == 1){//业务未达标
                    $arr[$k]['items'] = SignMemberCount::find()->where(['team_id'=>$v['team_id'],'qualified'=>0,'yl_sign_member_count.create_at' => $this->create_at])->select('team_id,member_id,name,avatar')->joinWith('memberInfo',false)->asArray()->all();
                }elseif ($this->member_type == 2 && $teamType == 2){//维护未达标
                    $arr[$k]['items'] = SignMemberCount::find()->where(['team_id'=>$v['team_id'],'qualified'=>0,'yl_sign_member_count.create_at' => $this->create_at])->select('team_id,member_id,name,avatar')->joinWith('memberInfo',false)->asArray()->all();
                }elseif ($this->member_type == 3 && $teamType == 1){//业务未签到
                    $arr[$k]['items'] = SignMemberCount::find()->where(['team_id'=>$v['team_id'],'sign_number'=>0,'yl_sign_member_count.create_at' => $this->create_at])->select('team_id,member_id,name,avatar')->joinWith('memberInfo',false)->asArray()->all();
                }elseif ($this->member_type == 3 && $teamType == 2){//维护未签到
                    $arr[$k]['items'] = SignMemberCount::find()->where(['team_id'=>$v['team_id'],'sign_number'=>0,'yl_sign_member_count.create_at' => $this->create_at])->select('team_id,member_id,name,avatar')->joinWith('memberInfo',false)->asArray()->all();
                }elseif ($this->member_type == 4 && $teamType == 2){//维护中评  SignMaintain
                    $arr[$k]['items'] = Sign::find()->where(['team_id'=>$v['team_id'], 'evaluate'=>2,'left(create_at,10)' => $this->create_at])->select('team_id,member_id,member_name as name,member_avatar as avatar')->distinct(['team_id','member_id'])->joinWith('maintain',false)->asArray()->all();
                }elseif ($this->member_type == 5 && $teamType == 2){//维护差评  SignMaintain
                    $arr[$k]['items'] = Sign::find()->where(['team_id'=>$v['team_id'], 'evaluate'=>3,'left(create_at,10)' => $this->create_at])->distinct(['team_id','member_id'])->select('team_id,member_id,member_name as name,member_avatar as avatar')->joinWith('maintain',false)->asArray()->all();
                }elseif ($this->member_type == 6 ){//签到早退  SignBusiness
                    $arr[$k]['items'] = SignMemberCount::find()->where(['team_id'=>$v['team_id'],'leave_early'=>1,'yl_sign_member_count.create_at' => $this->create_at])->select('team_id,member_id,name,avatar')->joinWith('memberInfo',false)->asArray()->all();
                }
                //1、超时签到人数 2、未达标人数 3、未签到人数 4、中评人数 5、差评人数 6、业务早退
            }
        }
        return $arr;
    }

    public function getMaintain(){
        return $this->hasOne(SignMaintain::className(),['sign_id'=>'id'])->select('sign_id,yl_sign_maintain.id,evaluate');
    }
    //判断tema_id是否有变化
    public function checkThisTeamId(){
        $currentTeamId = SignTeamMember::getTeamId(Yii::$app->user->id);
        if($currentTeamId != $this->team_id){
            return false;
        }
        return true;
    }
    public function scenes(){
        return [
            'get-view' =>[
                'member_type' => [
                    'required'=>'1',
                    'result'=>'SIGN_MEMBER_TYPE_EMPTY'
                ],
                'team_type' => [

                ],
                'create_at' => [
                    'required'=>'1',
                    'result'=>'SIGN_CREATE_AT_EMPTY'
                ],
                'team_id' => [
                    'required'=>'1',
                    'result'=>'SIGN_TEAM_ID_EMPTY'
                ],
            ],
            //全部签到数据
            'team-all-data' => [
                'create_at' => [],
                'team_id' => [
                    'required'=>'1',
                    'result'=>'SIGN_TEAM_ID_EMPTY'
                ],
            ],
            'create' => [
                'shop_name' => [
                    'required'=>'1',
                    'result'=>'SIGN_SHOP_NAME_EMPTY'
                ],
                'shop_address' => [
                    'required'=>'1',
                    'result'=>'SIGN_SHOP_ADDRESS_EMPTY'
                ],
                'team_id' => [
                   [
                       [
                           'required' => '1',
                           'result' => 'SIGN_TEAM_ID_EMPTY'
                       ],
                       [
                           'function' => 'this::checkThisTeamId',
                           'result' => 'SING_TEAM_ID_CHANGED'
                       ],
                   ]
                ],
            ],
            //业务足迹
            'team-footmark' => [
                'create_at' => [],
                'team_id' => [],
            ],
            //个人签到详情(列表)
            'single-sign-view' => [
                'create_at' => [],
                'member_id' => [],
            ],
            //个人单次签到详情
            'single-detail' => [
                'id' => [
                    'required'=>'1',
                    'result'=>'SIGN_ID_EMPTY'
                ],
                'member_id' => [
//                    'required'=>'1',
//                    'result'=>'SIGN_MEMBER_ID_EMPTY'
                ],
            ],
        ];
    }

}
