<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use Yii;
use yii\base\Exception;
use yii\db\Expression;

/**
 * This is the model class for table "{{%sign_member_count}}".
 *
 * @property string $id
 * @property string $member_id
 * @property integer $sign_number
 * @property string $update_at
 * @property string $create_at
 */
class SignMemberCount extends ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sign_member_count}}';
    }

//    public function beforeSave($insert){
//        if(parent::beforeSave($insert)){
//
//            return true;
//        }
//    }
    public function signSave(){
        try{
            //判断是否是新记录
            $nowNum = self::find()->select('sign_number')->where(['member_id'=>Yii::$app->user->id, 'create_at'=>date('Y-m-d')])->one();
            if(!$nowNum){
                $this->member_id = Yii::$app->user->id;
                $this->sign_number = 1;
                $this->update_at = date('Y-m-d H:i:s');
                $this->create_at = date('Y-m-d');
                if(!$this->save()){
                    throw new Exception('Id为:'.Yii::$app->user->id.'的成员'.date('Y-m-d').'第一条签到数量写入失败');
                }
            }else{
                $updateFields = ['sign_number' => new Expression('sign_number + 1')];
                //判断是否超时
                if($this->late_sign == 1){
                    $updateFields['late_sign'] = 1;
                }
                //判断是否达标
                $dbNum = $this->getTableField('yl_sign_team','sign_qualified_number',['id'=>$this->team_id]);
                if(empty($dbNum)){
                    $signTime = SignTeam::getTeamTime($this->team_id);
                    $dbNum['sign_qualified_number'] = $signTime['sign_qualified_number'];
                }
                if(!$dbNum || $dbNum['sign_qualified_number'] <= $nowNum['sign_number']){
                    $updateFields['qualified'] = 1;
                }
                if(!self::updateAll($updateFields,['member_id'=>Yii::$app->user->id, 'create_at'=>date('Y-m-d') ,'team_id' => $this->team_id])){
                    throw new Exception('Id为:'.Yii::$app->user->id.'的成员'.date('Y-m-d').'的签到数量更新失败');
                }
            }
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }

    }

    //获取未签到人员列表
    public function getNotSignMembers(){
        if(!$this->create_at){ $this->create_at = date('Y-m-d'); }
        if (Yii::$app->user->identity->sign_team_admin == 1) {
            if ($this->team_id == 'business') {
                $where = ['sign_number'=>0, 'yl_sign_member_count.create_at' => $this->create_at,'team_type' => 1];
            } elseif ($this->team_id == 'maintain'){
                $where = ['sign_number'=>0, 'yl_sign_member_count.create_at' => $this->create_at,'team_type' => 2];
            } else {
                $where = ['sign_number'=>0, 'yl_sign_member_count.create_at' => $this->create_at,'team_id' => $this->team_id];
            }
        } else {
            if (!SignTeamMember::find()->where(['member_id' => Yii::$app->user->id, 'team_id' => $this->team_id])->count()) {
                return false;
            }
            $where = ['sign_number'=>0, 'yl_sign_member_count.create_at' => $this->create_at,'team_id' => $this->team_id];
        }
        return self::find()->where($where)->select('yl_sign_member_count.id,member_id,yl_sign_member_count.create_at,yl_sign_member_count.team_id')->joinWith('memberName')->orderBy('name_prefix ASC,member_id DESC')->asArray()->all();
    }

    // 判断用户当日的签到数据
    public static function getSignData($team_id,$member_id = 0) {
        if (empty($member_id)) {
            $member_id = Yii::$app->user->id;
        }
        $signData = SignMemberCount::find()->select('sign_number,update_at')->where(['member_id' => $member_id,'team_id' => $team_id, 'create_at' => date('Y-m-d')])->asArray()->one();
        if (empty($signData)) {
            $memberCountModel = new SignMemberCount();
            $memberCountModel->team_id = $team_id;
            $memberCountModel->member_id = $member_id;
            $memberCountModel->create_at = date('Y-m-d H:i:s');
            $memberCountModel->save();
            return ['sign_number' => 0, 'update_at' => ''];
        }
        return $signData;
    }

    public function getTeam() {
        return $this->hasOne(SignTeam::className(),['id'=>'team_id']);
    }
    public function getMemberName(){
        return $this->hasOne(Member::className(),['id' => 'member_id'])->select('id,name,name_prefix,avatar');
    }


    //获取人员头像、名字
    public function getMemberInfo(){
        return $this->hasOne(Member::className(),['id'=>'member_id'])->select('yl_member.id,`name`,avatar');
    }
    public function scenes(){
        return [
            //未签到
            'not-sign-members' => [
                'create_at' => [],
                'team_id' => [
                    'required'=>'1',
                    'result' => 'TEAM_ID_EMPTY'
                ],
            ],
        ];
    }
}
