<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use Yii;
use yii\base\Exception;
use yii\db\Expression;

/**
 * This is the model class for table "{{%sign_team_member}}".
 *
 * @property string $id
 * @property integer $team_id
 * @property string $member_id
 * @property integer $member_type
 */
class SignTeamMember extends ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sign_team_member}}';
    }

    public function beforeSave($insert){
        if(parent::beforeSave($insert)){
            if(!$insert && $this->member_type == 1){
                $this->update_at = '0000-00-00 00:00:00';
            }elseif (!$insert && $this->member_type == 2){
                $this->update_at = date('Y-m-d H:i:s');
            }
        }
        return true;
    }

    public function beforeDelete(){
        if(parent::beforeDelete()){
            $member = self::find()->where(['member_id'=>Yii::$app->user->id])->asArray()->one();
            if($member['member_type'] == 2){
                $mrr = explode(',',$this->member_id);
                foreach ($mrr as $v){
                    $otherMember = self::find()->where(['member_id'=>$v])->asArray()->one();
                    if($otherMember['member_type'] == 2){
                        return false;
                    }
                }
            }
        }
        return true;
    }
    //设置或取消负责人
    public function setPrincipal(){
        $teamMemberModel = self::find()->where(['member_id'=>$this->id])->one();
        if($teamMemberModel->member_type == 3){
            return 'SIGN_MANAGE_CAN_NOT_BE_CHANGED';
        }
        $trans = \Yii::$app->db->beginTransaction();
        try{
            $teamMemberModel->member_type = $this->member_type;
            $teamMemberModel->save();
            if($this->member_type == 1){
                $symbol = '-';
                $logWord = '取消';
            }else{
                $symbol = '+';
                $logWord = '设置';
            }
            //团队负责人加减
            SignTeam::updateAll(['team_manager_number'=>new Expression('team_manager_number'.$symbol.'1')],['id'=>$this->team_id]);
            //日志操作
            $teamObj = SignTeam::findOne($teamMemberModel->team_id);
            $memberName = Member::getMemberFieldByWhere(['id'=>$this->id],'name');
            if(!SignLog::writeLog($teamMemberModel->team_id,$teamObj->team_name,$teamObj->team_type,$logWord.$memberName.'的负责人权限')){
                throw new Exception('签到日志取消或设置负责人操作失败');
            }
            $trans->commit();
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            $trans->rollBack();
            return false;
        }
    }
    //判断负责人所在团队和要修改的团队是否是一个
    public static function judgeTeamId($member_id,$team_id){
        $teamId = SignTeamMember::getTeamId($member_id);
        return $team_id == $teamId;
    }
    //为团队添加成员
    public function addMember(){
        $memberType = SignTeamMember::getMemberType(Yii::$app->user->id);
        if($memberType < 2){
            return 'ONLY_MANAGER_OPERATE';
        }
        if($memberType == 2){
            if(!self::judgeTeamId(Yii::$app->user->id,$this->team_id)){
                return 'YOU_HAVE_NOT_PERMISSION';
            }
        }
        $trans = \Yii::$app->db->beginTransaction();
        try{
            $mrr = $this->member_id;
            if(empty($mrr)){
                return 'ERROR';
            }
            //成员表
            $model = new self();
            //成员统计表
            $signMemberCountModel = new SignMemberCount();
            $teamModel = SignTeam::findOne($this->team_id);
            if(!$teamModel){
                return 'THIS_TEAM_NOT_EXIST';
            }
            foreach ($mrr as $v){
                $currentModel = clone $model;
                $currentModel->member_id = $v;
                $currentModel->team_id = $this->team_id;
                //如果此人是超级管理员
                $sign_team_admin = $this->getTableField('yl_member','sign_team_admin',['id'=>$v]);
                if($sign_team_admin['sign_team_admin'] == 1){
                    $currentModel->member_type = 3;
                }
                $currentModel->save();
                Member::updateAll(['sign_team_id'=>$this->team_id], ['id'=>$v]);
                $currentMemberCountModel = clone $signMemberCountModel;
                if(!SignMemberCount::find()->where(['member_id'=>$v, 'create_at'=>date('Y-m-d'), 'team_id' => $this->team_id])->count()){
                    $currentMemberCountModel->team_id = $this->team_id;
                    $currentMemberCountModel->team_type = $teamModel->team_type;
                    $currentMemberCountModel->member_id = $v;
                    $currentMemberCountModel->update_at = '0000-00-00 00:00:00';
                    $currentMemberCountModel->create_at = date('Y-m-d');
                    $currentMemberCountModel->save();
                }
                $memberName = Member::getMemberFieldByWhere(['id'=>$v],'name');
                if(!SignLog::writeLog($this->team_id,$teamModel->team_name,$teamModel->team_type,'添加成员'.$memberName)){
                    throw new Exception('签到日志添加成员操作失败');
                }

            }
            //该团队人数增加
            SignTeam::updateAll(['team_member_number' => new Expression('team_member_number + '.count($mrr))],['id'=>$this->team_id]);
            $trans->commit();
            return 'SUCCESS';
        }catch (Exception $e){
            \Yii::error($e->getMessage(),'db');
            $trans->rollBack();
            return 'ERROR';
        }
    }
    //获取某团队可删除的成员
    public static function getDeleteMembers($id){
        $members = self::find()->where(['team_id'=>$id])->select('id,team_id,member_id,member_type')->orderBy('id DESC')->asArray()->all();
        if(!empty($members)){
            $patt = '/\d{11}/';
            foreach ($members as $k => $v){
                $memberInfo = Member::findOne($v['member_id']);
                if(!$memberInfo){
                    return 'SIGN_TEAM_MEMBER_ERROR';
                }
                $members[$k]['member_name'] = $memberInfo->name;
                if(!preg_match($patt,$memberInfo->name)){
                    $members[$k]['member_name'] = $memberInfo->name.' ('.$memberInfo->mobile.')';
                }
                $members[$k]['member_mobile'] = $memberInfo->mobile;
                $members[$k]['member_tx'] = $memberInfo->avatar;
            }
        }
        return [
            'member_type' => strval(SignTeamMember::getMemberType(\Yii::$app->user->id)),
            'items' => $members
        ];
    }
    //删除团队成员
    public function deleteMembers(){
        $memberType = SignTeamMember::getMemberType(Yii::$app->user->id);
        if($memberType < 2){
            return 'ONLY_MANAGER_OPERATE';
        }
        if($memberType == 2){
            if(!self::judgeTeamId(Yii::$app->user->id,$this->team_id)){
                return 'YOU_HAVE_NOT_PERMISSION';
            }
        }
        $trans = \Yii::$app->db->beginTransaction();
        $teamObj = SignTeam::findOne($this->team_id);
        try{
            $mrr = explode(',',$this->member_id);
            if(empty($mrr)){
                return 'ERROR';
            }
            $principal = 0;
            foreach ($mrr as $v){
                if($memberType == 2){
                    if(SignTeamMember::getMemberType($v) > 1){
                        return 'MANAGER_CAN_NOT_DELETE_MANAGER';
                    }
                }
                $currentModel = self::find()->where(['member_id'=>$v])->one();
                if($currentModel->getAttribute('member_type') == 2){
                    $principal ++;
                }
                $currentModel->delete();
                Member::updateAll(['sign_team_id'=>0], ['id'=>$v]);
                $delName = Member::getMemberFieldByWhere(['id'=>$v],'name');
                if(!SignLog::writeLog($teamObj->id,$teamObj->team_name,$teamObj->team_type,'移除成员'.$delName)){
                    throw new Exception('签到日志删除成员操作失败');
                }
            }
            //该团队人数减少（若有负责人则负责人人数减少）
            SignTeam::updateAll(['team_member_number' => new Expression('team_member_number - '.count($mrr)), 'team_manager_number' => new Expression('team_manager_number - '.$principal)],['id'=>$this->team_id]);
            $trans->commit();
            return 'SUCCESS';
        }catch (Exception $e){
            \Yii::error($e->getMessage(),'db');
            $trans->rollBack();
            return 'ERROR';
        }
    }
    //签到界面
    public static function getSignViewData(){
        $member_id = \Yii::$app->user->id;
        $data = [];
        //该业务员所在组
        $team = self::find()->where(['member_id'=>$member_id])->asArray()->one();
        if(!empty($team)){
            $teamObj = SignTeam::findOne($team['team_id']);
            $teamName = $teamObj->getAttribute('team_name');
            $data['team_name'] = $teamName;
            $data['team_id'] = strval($team['team_id']);
            $signTime = SignTeam::getTeamTime($team['team_id']);
            $data['sign_interval_time'] = $signTime['sign_interval_time'];
            $data['team_type'] = strval($teamObj->team_type);
        }else{
            $data['team_name'] = '尚未加入任何团队';
            $data['team_id'] = '0';
            $data['sign_interval_time'] = '0';
            $data['team_type'] = '0';
        }
        //是否是迟到签到
        if(isset($teamObj)){
            $selfTime = $signTime['first_sign_time'];
            $intervalTime = $signTime['sign_interval_time'];
        }else{
            $selfTime = '10:00:00';
        }

        $data['today'] = date('Y').'年'.date('m').'月'.date('d').'日';
        $data['now_time'] = date('H:i');
        //已经签到次数
        $signNum = SignMemberCount::find()->where(['member_id'=>$member_id, 'create_at'=>date('Y-m-d'),'team_id' => $data['team_id']])->select('sign_number')->asArray()->one();
        $data['sign_num'] = empty($signNum) ? '0' : strval($signNum['sign_number']);
        $data['interval'] = '';
        if($data['sign_num']){
            //判断两次签到间隔是否达标
            $lastSignTime = SignMemberCount::find()->where(['member_id'=>$member_id,'team_id'=>$team['team_id'], 'create_at'=>date('Y-m-d')])->one()->getAttribute('update_at');
            $tm = isset($intervalTime) ? $intervalTime : '30';
            if(floor((time() - strtotime($lastSignTime))/60) < $tm){
                $data['interval'] = '1';
            }
            $data['over_time'] = '';
        }else{
            //只有首次签到才显示超时时间
            $now = time();
            $today = date('Y-m-d');
            $tm = $today.' '.$selfTime;
            $diff = $now - strtotime($tm);
            if($diff > 0){
                $data['over_time'] = floor(($diff)/3600).'小时'.floor(($diff)%3600/60).'分钟';
            }else{
                $data['over_time'] = '';
            }
        }
        $data['member_name'] = Yii::$app->user->identity->name;
        return $data;
    }
    //检查队员类型
    public function checkMemberType(){
        //var_dump($this->member_type);exit;
        if(!in_array($this->member_type, [1,2])){
            return false;
        }
        return true;
    }
    //检查会员是否已存在
    public function checkMemberId(){
        $mrr = explode(',',$this->member_id);
        foreach ($mrr as $v){
            if(self::find()->where(['member_id'=>$v])->count()){
                return false;
            }
        }
        $this->member_id = $mrr;
        return true;
    }

    public function checkTeamId(){
        $mrr = explode(',',$this->member_id);
        foreach ($mrr as $v){
            if(!self::find()->where(['member_id'=>$v,'team_id'=>$this->team_id])->count()){
                return false;
            }
        }
        return true;
    }
    //设置负责人时检查该人是否属于该队
    public function checkTeamMember(){
            if(!self::find()->where(['member_id'=>$this->id,'team_id'=>$this->team_id])->count()){
                return false;
            }
        return true;
    }

    //获取该人员的团队类型
    public static function getTeamType(){
        $team = self::find()->where(['member_id'=>Yii::$app->user->id])->select('team_id')->asArray()->one();
        if(!empty($team)){
            $obj = SignTeam::findOne($team['team_id']);
            return $obj ? strval($obj->getAttribute('team_type')) : '0';
        }
        return '0';
    }
    //根据团队member_id获取team_id
    public static function getTeamId($member_id){
        $data = self::find()->where(['member_id'=>$member_id])->select('team_id')->asArray()->one();
        return empty($data) ? 0 : $data['team_id'];
    }
    //获取成员类别
    public static function getMemberType($member_id){
        $sign_team_admin = Member::findOne($member_id)->getAttribute('sign_team_admin');
        if($sign_team_admin){
            return '3';
        }
        $data = self::find()->where(['member_id'=>$member_id])->select('member_type')->asArray()->one();
        if(empty($data)){
            return '0';
        }
        return $data['member_type'];
    }
    public function scenes(){
        return [
            'set-principal' => [
                'id' => [
                    'required'=>'1',
                    'result'=>'SIGN_TEAM_MEMBER_ID_EMPTY'
                ],
                'member_type' => [
                    [
                        [
                            'required'=>'1',
                            'result'=>'SIGN_MEMBER_TYPE_EMPTY'
                        ],
                        [
                            'function'=>'this::checkMemberType',
                            'result'=>'SIGN_MEMBER_TYPE_ERROR'
                        ],
                    ],
                ],
                'team_id' => [
                    [
                        [
                            'required'=>'1',
                            'result'=>'SIGN_TEAM_ID_EMPTY'
                        ],
                        [
                            'function'=>'this::checkTeamMember',
                            'result'=>'SIGN_TEAM_ID_ERROR'
                        ],
                    ],
                ],
                'update_at' => [],
            ],
            'add-members' => [
                'team_id' => [

                    'required'=>'1',
                    'result'=>'SIGN_TEAM_ID_EMPTY'
                ],
                'member_id' => [
                    [
                        [
                            'required'=>'1',
                            'result'=>'SIGN_MEMBER_TYPE_EMPTY'
                        ],
                        [
                            'function'=>'this::checkMemberId',
                            'result'=>'SIGN_MEMBER_ALREADY_EXIST'
                        ],
                    ],
                ],
            ],
            'delete-members' => [
                'team_id' => [

                    'required'=>'1',
                    'result'=>'SIGN_TEAM_ID_EMPTY'
                ],
                'member_id' => [
                    [
                        [
                            'required'=>'1',
                            'result'=>'SIGN_MEMBER_TYPE_EMPTY'
                        ],
                        [
                            'function'=>'this::checkTeaMId',
                            'result'=>'SIGN_MEMBER_ALREADY_EXIST'
                        ],
                    ],
                ],
            ],

        ];
    }
    //团队详情member信息
    public function getMemberInfo(){
        return $this->hasOne(Member::className(),['id'=>'member_id'])->select('id,name,avatar');
    }

    // 获取用户的团队信息
    public static function getTeam($member_id = 0) {
        if (empty($member_id)) {
            $member_id = Yii::$app->user->id;
        }
        $teamMemberModel = SignTeamMember::find()->where(['member_id' => $member_id])->select('team_id,member_type')->asArray()->one();
        if (empty($teamMemberModel)) {
            return false;
        }
        $teamModel = SignTeam::find()->where(['id' => $teamMemberModel['team_id']])->asArray()->one();
        if ($teamModel['team_type'] == 1) {
            $configPrefix = 'salesman_';
        } else {
            $configPrefix = 'maintain_';
        }
        if(empty($teamModel['sign_interval_time'])){
            $teamModel['sign_interval_time'] = SystemConfig::getConfig($configPrefix.'check_interval_time') ?? '30';
        }
        if(empty($teamModel['sign_qualified_number'])){
            $teamModel['sign_qualified_number'] = SystemConfig::getConfig($configPrefix.'day_sign_number') ?? '5';
        }
        if(empty($teamModel['first_sign_time'])){
            $teamModel['first_sign_time'] = SystemConfig::getConfig($configPrefix.'first_check_time') ?? '10:00:00';
        }
        $teamModel['member_type'] = $teamMemberModel['member_type'];
        return $teamModel;
    }


}
