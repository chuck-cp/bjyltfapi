<?php

namespace api\modules\v1\models;

use common\libs\ToolsClass;
use Yii;
use yii\base\Exception;

/*
 * 用户基础信息
 * */
class MemberInfo extends \api\core\ApiActiveRecord
{
    public static function tableName()
    {
        return '{{%member_info}}';
    }

    /*
     * 验证身份证信息是否已审核通过
     * */
    public function checkExamineStatus(){
        return self::find()->where(['member_id'=>Yii::$app->user->id,'examine_status'=>[1,0]])->count();
    }

    /*
     * 获取身份审核状态(用于首页接口)
     * */
    public static function getMemberExamineStatusByIndex(){
        $memberModel = self::find()->where(['member_id'=>Yii::$app->user->id])->select('examine_status')->asArray()->one();
        $resultStatus = "-1";
        if(empty($memberModel)){
            return $resultStatus;
        }
        return $memberModel['examine_status'];
    }


    public function getMemberStatus(){
        //$memberInfo = self::find()->where(['member_id'=>Yii::$app->user->id,'examine_status'=>1])->select('name')->asArray()->one();
        $memberInfo = self::find()->where(['member_id'=>Yii::$app->user->id])->select('name,examine_status')->asArray()->one();
        $memberName = '';
        $examine_status = -1;
        if(!empty($memberInfo)){
            $memberName = $memberInfo['name'];
            $examine_status = $memberInfo['examine_status'];
        }
        return ['number'=>$examine_status,'memberName'=>$memberName];
    }

    /*
     * 获取我的审核状态
     * */
    public function getExamineStatus(){
        $examineModel = self::find()->where(['member_id'=>Yii::$app->user->id])->select('examine_status')->asArray()->one();
        if(empty($examineModel)){
            return -1;
        }
        return $examineModel['examine_status'];
    }

    /*
     * 修改身份证信息
     * */
    public function updateMemberInfo(){
        $dbTrans = Yii::$app->db->beginTransaction();
        try{
            $user_id = Yii::$app->user->id;
            if($infoModel = self::find()->where(['member_id'=>$user_id])->one()){
                $infoModel->id_number = $this->id_number;
                $infoModel->id_front_image = ToolsClass::replaceCosUrl($this->id_front_image);
                $infoModel->id_back_image = ToolsClass::replaceCosUrl($this->id_back_image);
                $infoModel->id_hand_image = ToolsClass::replaceCosUrl($this->id_hand_image);
                $infoModel->name = $this->name;
                $infoModel->sex = $this->sex;
                $infoModel->examine_status = 0;
                $infoModel->apply_at = date('Y-m-d H:i:s');
                $infoModel->save();
            }else{
                $this->examine_status = 0;
                $this->member_id = Yii::$app->user->id;
                $this->apply_at = date('Y-m-d H:i:s');
                $this->save();
            }
            if(!LogExamine::writeLog(2,$user_id)){
                throw new Exception("写入申请日志失败");
            }
            $dbTrans->commit();
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage());
            $dbTrans->rollBack();
            return false;
        }
    }

    /*
     * 获取身份证信息
     * */
    public function getInfo(){
        $memberModel = self::find()->select('member_id,sex,name,id_number,id_front_image,id_back_image,id_hand_image,examine_status')->where(['member_id'=>Yii::$app->user->id])->asArray()->one();
        if(empty($memberModel)){
            return [];
        }
        $memberModel['examine_desc'] = '';
        if($memberModel['examine_status'] == 2){
            $memberModel['examine_desc'] = LogExamine::getExamineResult($memberModel['member_id'],2);
        }
        return $memberModel;
    }

    /*
     * 获取电工证件信息
     * */
    public function getElectricianCertificate(){
        $memberModel = self::find()->select('live_area_name,live_area_id,live_address,member_id,sex,name,id_number,electrician_certificate_number,electrician_certificate_type,electrician_certificate_front_image,electrician_certificate_back_image,professional_name,electrician_examine_status,electrician_certificate_area_name')->where(['member_id'=>Yii::$app->user->id])->asArray()->one();
        if(empty($memberModel)){
            return [];
        }
        //获取组队信息
        $teamModel = MemberTeamList::find()->joinWith('team',false)->where(['yl_member_team_list.member_id'=>Yii::$app->user->id,'yl_member_team_list.status'=>1])->select('yl_member_team_list.team_id,yl_member_team.team_member_id,yl_member_team.team_member_name,yl_member_team.company_name,yl_member_team.team_name,wait_shop_number')->asArray()->one();
        if(empty($teamModel)){
            $memberModel['team_member_id'] = '';
            $memberModel['team_member_name'] = '';
            $memberModel['team_member_mobile'] = '';
            $memberModel['company_name'] = '';
            $memberModel['team_name'] = '';
            $memberModel['team_id'] = '';
            $memberModel['wait_shop_number'] = '0';
        }else{
            $memberModel['team_member_id'] = $teamModel['team_member_id'];
            $memberModel['team_member_name'] = $teamModel['team_member_name'];
            $memberModel['team_member_mobile'] = Member::getMemberFieldByWhere(['id'=>$teamModel['team_member_id']],'mobile');
            $memberModel['company_name'] = $teamModel['company_name'];
            $memberModel['team_name'] = $teamModel['team_name'];
            $memberModel['team_id'] = $teamModel['team_id'];
            $memberModel['wait_shop_number'] = $teamModel['wait_shop_number'];
        }
        //获取居住地址
        $memberModel['examine_desc'] = '';
        if($memberModel['electrician_examine_status'] == 2){
            $memberModel['examine_desc'] = LogExamine::getExamineResult($memberModel['member_id'],6);
        }
        return $memberModel;
    }

    /*
     * 修改电工证信息
     * */
    public function updateMemberCert(){
        if($this->electrician_examine_status == 0){
            return 'CERT_IN_EXAMINEING';
        }elseif($this->electrician_examine_status == 1){
            return 'CERT_EXAMINE_SUCCESS';
        }
        $dbTrans = Yii::$app->db->beginTransaction();
        try{
            if($this->electrician_certificate_apply_at == '0000-00-00 00:00:00'){
                $this->electrician_certificate_apply_at = date('Y-m-d H:i:s');
            }
            $this->live_area_name = SystemAddress::getAreaNameById($this->live_area_id);
            $this->electrician_examine_status = 0;
            $this->save();
            if(!LogExamine::writeLog(6,$this->member_id)){
                throw new Exception("写入申请日志失败");
            }
            $dbTrans->commit();
            return 'SUCCESS';
        }catch (Exception $e){
            $dbTrans->rollBack();
            Yii::error($e->getMessage(),'db');
            return 'ERROR';
        }

    }
    /*
     * 场景
     * */
    public function scenes(){
        return [
            'update-cert' => [
                'electrician_certificate_number'=>[
                    'required'=>'1',
                    'result'=>'CERT_NUMBER_EMPTY'
                ],
                'electrician_certificate_type'=>[
//                    'required'=>'1',
//                    'result'=>'CERT_LEVEL_EMPTY'
                ],
                'electrician_certificate_front_image'=>[
                    'required'=>'1',
                    'result'=>'CERT_FRONT_IMAGE_EMPTY'
                ],
                'electrician_certificate_back_image'=>[
                    'required'=>'1',
                    'result'=>'CERT_BACK_IMAGE_EMPTY'
                ],
                'professional_name'=>[
                    'required'=>'1',
                    'result'=>'PROFESSIONAL_NAME_EMPTY'
                ],
                'live_area_id'=>[
                    'required'=>'1',
                    'result'=>'LIVE_AREA_ID_EMPTY'
                ],
                'live_address'=>[
                    'required'=>'1',
                    'result'=>'LIVE_ADDRESS_EMPTY'
                ],
                'electrician_certificate_area_name' => [
                    'required'=>'1',
                    'result'=>'ELECTRICIAN_CERTIFICATE_AREA_NAME_EMPTY'
                ],
            ],
            'update-id'=>[
                'id_number'=>[
                    'required'=>'1',
                    'result'=>'ID_NUMBER_EMPTY'
                ],
                'id_front_image'=>[
                    'required'=>'1',
                    'result'=>'ID_FRONT_IMAGE_EMPTY'
                ],
                'id_back_image'=>[
                    'required'=>'1',
                    'result'=>'ID_BACK_IMAGE_EMPTY'
                ],
                'sex'=>[
                    'required'=>'1',
                    'result'=>'MEMBER_SEX_EMPTY'
                ],
                'name'=>[
                    'required'=>'1',
                    'result'=>'MEMBER_NAME_EMPTY'
                ],
            ],
        ];
    }
}
