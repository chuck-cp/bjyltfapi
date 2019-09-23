<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%sign_team_count_member_detail}}".
 *
 * @property string $id
 * @property integer $team_id
 * @property string $member_id
 * @property integer $member_type
 * @property string $create_at
 */
class SignTeamCountMemberDetail extends ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sign_team_count_member_detail}}';
    }

    //签到数据详情  1、超时签到人数 2、未达标人数 3、未签到人数 4、中评人数 5、差评人数
    public function getMembersView(){
        $where = ['create_at' => $this->create_at];
        $orderBy = '';
        $sort = ' DESC';
        $model = '';
        switch ($this->member_type){
            case 1:
                $orderBy = 'overtime_sign_member_number'.$sort;
                break;
            case 2:
                $orderBy = 'unqualified_member_number'.$sort;
                break;
            case 3:
                $orderBy = 'no_sign_member_number'.$sort;
                break;
            case 4:
                $orderBy = 'middle_evaluate_number'.$sort;
                break;
            case 5:
                $orderBy = 'bad_evaluate_number'.$sort;
                break;
        }
        $data = [];
        if($this->team_id == 'business'){
            $data = SignTeamBusinessCount::find()->where($where)->orderBy($orderBy)->asArray()->all();
        }elseif ($this->team_id == 'maintain'){
            $data = SignTeamMaintainCount::find()->where($where)->orderBy($orderBy)->asArray()->all();
        }else{
            $where['team_id'] = $this->team_id;
            $teamType = SignTeam::getTeamType($this->team_id);
            $model = $teamType == 1 ? (new SignTeamBusinessCount()) : (new SignTeamMaintainCount());
            $data = $model->find()->where($where)->orderBy($orderBy)->asArray()->all();
        }
        $arr = [];
        /*
        if(!empty($data)){
            foreach ($data as $k => $v){
                $arr[$k]['team_name'] = SignTeam::findOne($v['team_id'])->getAttribute('team_name');
                $arr[$k]['items'] = self::find()->where(['team_id'=>$v['team_id'], 'yl_sign_team_count_member_detail.member_type'=>$this->member_type])->select('member_id,team_type,team_id,`name`,avatar')->joinWith('memberInfo',false)->asArray()->all();
            }
        }
        */
        //print_r($data);exit();
        if(!empty($data)){
            foreach ($data as $k => $v){
                //团队名称
                $arr[$k]['team_name'] = SignTeam::findOne($v['team_id'])->getAttribute('team_name');
                //团队类型
                $teamType = SignTeam::getTeamType($v['team_id']);

                if($this->member_type == 1 && $teamType == 1){//业务签到超时
                    $arr[$k]['items'] = SignBusiness::find()->where(['team_id'=>$v['team_id'], 'late_sign'=>1,'left(create_at,10)' => $this->create_at])->select('id,team_id,member_id,team_member_type,member_name,member_avatar')->asArray()->all();
                }elseif ($this->member_type == 1 && $teamType == 2){//维护签到超时
                    $arr[$k]['items'] = SignMaintain::find()->where(['team_id'=>$v['team_id'], 'late_sign'=>1,'left(create_at,10)' => $this->create_at])->select('id,team_id,member_id,team_member_type,member_name,member_avatar')->asArray()->all();
                }elseif ($this->member_type == 2 && $teamType == 1){//业务未达标
                    $arr[$k]['items'] = SignMemberCount::find()->where(['team_id'=>$v['team_id'],'qualified'=>0,'yl_sign_member_count.create_at' => $this->create_at])->joinWith('memberInfo',false)->asArray()->all();
                }elseif ($this->member_type == 2 && $teamType == 2){//维护未达标
                    $arr[$k]['items'] = SignMemberCount::find()->where(['team_id'=>$v['team_id'],'qualified'=>0,'yl_sign_member_count.create_at' => $this->create_at])->joinWith('memberInfo',false)->asArray()->all();
                }elseif ($this->member_type == 3 && $teamType == 1){//业务未签到
                    $arr[$k]['items'] = SignMemberCount::find()->where(['team_id'=>$v['team_id'],'sign_number'=>0,'yl_sign_member_count.create_at' => $this->create_at])->joinWith('memberInfo',false)->asArray()->all();
                }elseif ($this->member_type == 3 && $teamType == 2){//维护未签到
                    $arr[$k]['items'] = SignMemberCount::find()->where(['team_id'=>$v['team_id'],'sign_number'=>0,'yl_sign_member_count.create_at' => $this->create_at])->joinWith('memberInfo',false)->asArray()->all();
                }elseif ($this->member_type == 4 && $teamType == 2){//维护中评
                    $arr[$k]['items'] = SignMaintain::find()->where(['team_id'=>$v['team_id'], 'evaluate'=>2,'left(create_at,10)' => $this->create_at])->select('id,team_id,member_id,team_member_type,member_name,member_avatar')->asArray()->all();
                }elseif ($this->member_type == 5 && $teamType == 2){//维护差评
                    $arr[$k]['items'] = SignMaintain::find()->where(['team_id'=>$v['team_id'], 'evaluate'=>3,'left(create_at,10)' => $this->create_at])->select('id,team_id,member_id,team_member_type,member_name,member_avatar')->asArray()->all();
                }
                //1、超时签到人数 2、未达标人数 3、未签到人数 4、中评人数 5、差评人数
            }
        }
        return $arr;
    }
    //获取team_name
    public function getTeamName(){
        return $this->hasOne(SignTeam::className(),['team_id'=>'id'])->select('yl_sign_team.id, yl_sign_team.team_name');
    }
    //获取人员头像、名字
    public function getMemberInfo(){
        return $this->hasOne(Member::className(),['id'=>'member_id'])->select('yl_member.id,`name`,avatar');
    }
    public function getMemberName(){
        return $this->hasOne(Member::className(),['id' => 'member_id'])->select('id,name,name_prefix,avatar')->orderBy('name_prefix DESC');
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
        ];
    }
}
