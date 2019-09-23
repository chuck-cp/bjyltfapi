<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use api\modules\v1\models\MemberInfo;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "{{%member_team_list}}".
 *
 * @property integer $team_member_id
 * @property integer $member_id
 * @property string $member_name
 * @property integer $install_shop_number
 * @property integer $install_screen_number
 * @property integer $wait_shop_number
 */
class MemberTeamList extends ApiActiveRecord
{
    public $type;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%member_team_list}}';
    }

    public function getTeam(){
        return $this->hasOne(MemberTeam::className(),['id'=>'team_id']);
    }

    /*
     * 检查我是否加入过小组
     * */
    public static function getJoinStatus(){
        return MemberTeamList::find()->where(['member_id'=>Yii::$app->user->id,'status'=>1])->count();
    }


    /*
    * 获取团队成员
    * */
    public function getTeamlist(){
        try{
            $memberTeamList = self::find()->joinWith('member',false)->where(['yl_member_team_list.team_id'=>$this->team_id,'yl_member_team_list.status'=>1])->select('yl_member.avatar,yl_member.mobile as member_mobile,yl_member_team_list.id,yl_member_team_list.team_id,yl_member_team_list.member_id,yl_member_team_list.member_name,yl_member_team_list.install_shop_number,yl_member_team_list.install_screen_number,yl_member_team_list.wait_shop_number,yl_member_team_list.wait_screen_number,yl_member_team_list.member_type')->orderBy('wait_shop_number,wait_screen_number asc')->asArray()->all();
            if($this->type == 1){
                //指派店铺获取成员列表时,需要先判断一下团队队长是不是电工,如果不是则从返回结果中去掉
                if(!MemberInfo::find()->where(['member_id'=>Yii::$app->user->id,'electrician_examine_status'=>1])->count()){
                    foreach($memberTeamList as $key=>$value){
                        if($value['member_id'] != Yii::$app->user->id){
                            $resultTeamList[] = $value;
                        }
                    }
                    return $resultTeamList;
                }
            }
            return $memberTeamList;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
        }

    }


    public function getMember(){
        return $this->hasOne(Member::className(),['id'=>'member_id']);
    }
    public function scenes()
    {
        return [
            'teamlsit' => [
                'team_id' => [
                    'required' => '1',
                    'result'=>'MEMBER_NAME_EMPTY',
                ],
                'type' => [],
            ]
        ];
    }

}
