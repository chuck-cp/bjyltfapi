<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use pms\modules\count\count;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "{{%sign_team}}".
 *
 * @property string $id
 * @property string $team_name
 * @property integer $team_type
 * @property string $team_member_id
 */
class SignTeam extends ApiActiveRecord
{
    public $type;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sign_team}}';
    }

    //获取该表里的某字段
    public static function getField($id,$field){
        return self::findOne($id)->getAttribute($field);
    }
    //获得业务组和维护组信息
    public function getTeamMembers($team_type = 1){
        return self::find()->where(['team_type'=>$team_type])->select('id,team_name,team_member_number')->asArray()->all();
    }
    //根据team_id获取团队
    public function getTeamById($team_id){
        return self::find()->where(['id'=>$team_id])->select('id,team_name,team_member_number')->asArray()->all();
    }
    //团队详情
    public function getTeamDetail($id){
        $data = self::find()->where(['yl_sign_team.id'=>$id])->joinWith('members')->select('yl_sign_team.id,team_type,sign_data_permission,yl_sign_team.team_name,member_id,yl_sign_team_member.member_type')->asArray()->one();
        if(!empty($data['members'])){
            foreach ($data['members'] as $k => $v){
                $data['members'][$k]['name'] = $v['memberInfo']['name'];
                $data['members'][$k]['avatar'] = $v['memberInfo']['avatar'];
                unset($data['members'][$k]['memberInfo']);
            }
        }
        $memberNum = count($data['members']);
        if($memberNum > 0){
            array_unshift($data['members'],['id'=>'','member_id'=>'','team_id'=>'','member_type'=>'','name'=>'添加','avatar'=>'http://i1.bjyltf.com/system/function/team_admin_add.png']);
            array_push($data['members'],['id'=>'','member_id'=>'','team_id'=>'','name'=>'移除','avatar'=>'http://i1.bjyltf.com/system/function/team_admin_reduce.png']);
        }else{
            array_unshift($data['members'],['id'=>'','member_id'=>'','team_id'=>'','member_type'=>'','name'=>'添加','avatar'=>'http://i1.bjyltf.com/system/function/team_admin_add.png']);
        }
        $data['title'] = $data['team_type'] == 1 ? '业务' : '维护';
        $data['team_member_num'] = $memberNum;
        $data['member_type'] = SignTeamMember::getMemberType(\Yii::$app->user->id);
        return $data;
    }
    //获取团队详情里人员
    public function getMembers(){
        return $this->hasMany(SignTeamMember::className(),['team_id'=>'id'])->select('yl_sign_team_member.id,yl_sign_team_member.member_type,member_id,team_id')->orderBy('yl_sign_team_member.member_type DESC, yl_sign_team_member.update_at DESC')->joinWith('memberInfo');
    }
    //修改团队名称
    public function updateTeamName(){
        $memberType = SignTeamMember::getMemberType(\Yii::$app->user->id);
        if($memberType !== '3'){
            return false;
        }
        try{
            $trans = Yii::$app->db->beginTransaction();
            $teamModel = self::findOne($this->id);
            $teamModel->team_name = $this->team_name;
            $teamModel->save();
            if(!SignLog::writeLog($this->id,$this->team_name,$teamModel->team_type,'团队'.$teamModel->team_name.'改名为'.$this->team_name)){
                throw new Exception('签到日志修改团队名称操作失败');
            }
            $trans->commit();
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            $trans->rollBack();
            return false;
        }
    }
    //获取所有团队
    public function getAllTeams(){
        $business = [
            'id' => 'business',
            'team_name' => '全部业务组',
            'team_member_number' => '',
        ];
        $maintain = [
            'id' => 'maintain',
            'team_name' => '全部维护组',
            'team_member_number' => '',
        ];
        $where = true;
        if($this->type == 'business'){
            $where = ['team_type' => 1];
        }elseif ($this->type == 'maintain'){
            $where = ['team_type' => 2];
        }
        $data = self::find()->where($where)->select('id,team_name,team_member_number,team_type')->asArray()->all();
        if($this->type == 'business'){
            array_unshift($data,$business);
        }elseif ($this->type == 'maintain'){
            array_unshift($data,$maintain);
        }else{
            array_unshift($data,$maintain,$business);
        }
        return $data;
    }
    //插入前操作
    public function beforeSave($insert)
    {
        if($insert){
//            if ($this->team_type == 1) {
//                $configPrefix = 'salesman_';
//            } else {
//                $configPrefix = 'maintain_';
//            }
//            $this->sign_interval_time = SystemConfig::getConfig($configPrefix.'check_interval_time') ?? '30';
//            $this->sign_qualified_number = SystemConfig::getConfig($configPrefix.'day_sign_number') ?? '5';
//            $this->first_sign_time = SystemConfig::getConfig($configPrefix.'first_check_time') ?? '10:00:00';
            $this->create_at = date('Y-m-d H:i:s');
        }
        return parent::beforeSave($insert);
    }
    //保存
    public function teamSave(){
        //如果不是管理员不允许创建团队
        $memberType = SignTeamMember::getMemberType(\Yii::$app->user->id);
        if($memberType != 3){
            return 'YOU_HAVE_NOT_PERMISSION';
        }
        $this->team_member_id = \Yii::$app->user->id;
        $memberObj = Member::findOne(\Yii::$app->user->id);
        if(!$memberObj){
            return 'ERROR';
        }
        $this->team_member_name = $memberObj->name;
        //操作日志
        try{
            $trans = Yii::$app->db->beginTransaction();
            $tid = $this->save();
            if(!SignLog::writeLog($this->id,$this->team_name,$this->team_type,'创建'.$this->team_name.'团队')){
                throw new Exception('签到日志创建团队操作失败');
            }
            $trans->commit();
            return 'SUCCESS';
        }catch (Exception $e){
            \Yii::error($e->getMessage(),'db');
            $trans->rollBack();
            return 'ERROR';
        }
    }
    //检测团队名称是否已存在
    public function checkTeamName($team_name){
        if($this->team_type){
            $team = self::find()->where(['team_name'=>$this->team_name, 'team_type'=>$this->team_type])->count();
        }else{
            $team = self::find()->where(['team_name'=>$this->team_name])->count();
        }
        if($team > 0){
            return false;
        }
        return true;
    }
    //获取团队类型
    public static function getTeamType($id){
        $obj = self::findOne($id);
        if(!$obj){ return false; }
        return $obj->getAttribute('team_type');
    }
    //获取团队的签到间隔、签到达标次数、签到、首次签到时
    public static function getTeamTime($team_id){
        $teamType = self::getTeamType($team_id);
        if ($teamType == 1) {
            $configPrefix = 'salesman_';
        } else {
            $configPrefix = 'maintain_';
        }
        $teamModel = self::find()->where(['id'=>$team_id])->select('sign_interval_time,sign_qualified_number,first_sign_time')->asArray()->one();
        if(empty($teamModel['sign_interval_time'])){
            $teamModel['sign_interval_time'] = SystemConfig::getConfig($configPrefix.'check_interval_time') ?? '30';
        }
        if(empty($teamModel['sign_qualified_number'])){
            $teamModel['sign_qualified_number'] = SystemConfig::getConfig($configPrefix.'day_sign_number') ?? '5';
        }
        if(empty($teamModel['first_sign_time'])){
            $teamModel['first_sign_time'] = SystemConfig::getConfig($configPrefix.'first_check_time') ?? '10:00:00';
        }
        return $teamModel;
    }
    //修改团队互相查看数据开关
    public function turnOnOff(){
        $team_id = SignTeamMember::getTeamId(Yii::$app->user->id);
        $memberType = SignTeamMember::getMemberType(Yii::$app->user->id);
        if(!in_array($memberType,[2,3])){
            return 'YOU_HAVE_NOT_PERMISSION';
        }
        if($memberType == 2){
            if($team_id !== $this->id){
                return 'YOU_HAVE_NOT_PERMISSION';
            }
        }
        try{
            $trans = Yii::$app->db->beginTransaction();
            $currentModel = self::findOne($this->id);
            $currentModel->sign_data_permission = $this->sign_data_permission;
            $currentModel->save();
            $logWord = $this->sign_data_permission == 0 ? '关闭' : '开启';
            if(!SignLog::writeLog($this->id,$currentModel->team_name,$currentModel->team_type,$logWord.'签到查看权限')){
                throw new Exception('签到日志查看数据开关操作失败');
            }
            $trans->commit();
            return 'SUCCESS';
        }catch (Exception $e){
            \Yii::error($e->getMessage(),'db');
            $trans->rollBack();
            return 'ERROR';
        }

    }

    public function checkPermission(){
        if(!in_array($this->sign_data_permission,[0,1])){
            return false;
        }
        return true;
    }
    public function scenes(){
        return [
            //队员互相查看数据
            'on-off' => [
                'id' => [
                    'required'=>'1',
                    'result'=>'SIGN_TEAM_ID_EMPTY'
                ],
                'sign_data_permission' => [
                    [
                        [
                            'function'=>'this::checkPermission',
                            'result'=>'SIGN_DATA_PERMISSION_ERROR'
                        ]
                    ],
                ],
            ],
            //选择团队
            'choose-team' => [
              'type' => [],
            ],
            //创建团队
            'create-team' => [
                'team_name' => [
                    [
                        [
                            'required'=>'1',
                            'result'=>'TEAM_NAME_EMPTY'
                        ],
                        [
                            'function'=>'this::checkTeamName',
                            'result'=>'TEAM_NAME_EXIST'
                        ],
                    ],
                ],
                'team_type' => [
                    'required'=>'1',
                    'result'=>'TEAM_TYPE_EMPTY'
                ],
                'sign_data_permission' => [
                    [
                        [
                            'function'=>'this::checkPermission',
                            'result'=>'SIGN_DATA_PERMISSION_ERROR'
                        ]
                    ],
                ],
            ],
            //修改团队名称
            'update-team-name' => [
                'id' => [
                    'required'=>'1',
                    'result'=>'TEAM_ID_EMPTY'
                ],
                'team_name' => [
                    [
                        [
                            'required'=>'1',
                            'result'=>'TEAM_NAME_EMPTY'
                        ],
                        [
                            'function'=>'this::checkTeamName',
                            'result'=>'TEAM_NAME_EXIST'
                        ]
                    ],

                ],
            ],
        ];
    }
}
