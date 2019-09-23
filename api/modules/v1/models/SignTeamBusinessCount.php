<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%sign_team_business_count}}".
 *
 * @property string $id
 * @property integer $team_id
 * @property integer $total_sign_member_number
 * @property integer $overtime_sign_member_number
 * @property integer $no_sign_member_number
 * @property integer $unqualified_member_number
 * @property integer $total_sign_shop_number
 * @property integer $repeat_sign_number
 * @property double $repeat_sign_rate
 * @property integer $repeat_shop_number
 * @property string $create_at
 */
class SignTeamBusinessCount extends ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%sign_team_business_count}}';
    }


    /*
     *  "team_id":"6",
        "":"0",
        "":"0",
        "":"0",
        "":"3",
        "":"3",
        "":"0",
        "":"0",
        "":"0",
        "":"0",
        "":"0.00",
        "":"2018-11-30",
     * */

    public function getDatas(){
        $re = [];
        if($this->team_id == 'business'){
            $re = SignBusinessCount::find()->where(['create_at'=>$this->create_at])->asArray()->one();
            $re['team_name'] = '全部业务组';
        }elseif ($this->team_id == 'maintain'){
            $re = SignMaintainCount::find()->where(['create_at'=>$this->create_at])->asArray()->one();
            $re['team_name'] = '全部维护组';
        }else{
            $type = SignTeam::getTeamType($this->team_id);
            $model = $type == 1 ? (new self()) : (new  SignTeamMaintainCount());
            $re = $model->find()->where(['team_id'=>$this->team_id, 'create_at'=>$this->create_at])->asArray()->one();
            $teamObj = SignTeam::findOne($this->team_id);
            if(empty($re)){
                $re['id'] = '0';
                $re['team_id'] = $teamObj->id;
                $re['total_sign_number'] = '0';
                $re['total_sign_member_number'] = '0';
                $re['overtime_sign_member_number'] = '0';
                $re['no_sign_member_number'] = '0';
                $re['unqualified_member_number'] = '0';
                $re['total_evaluate_number'] = '0';
                $re['good_evaluate_number'] = '0';
                $re['leave_early'] = '0';
                $re['middle_evaluate_number'] = '0';
                $re['bad_evaluate_number'] = '0';
                $re['bad_evaluate_rate'] = '0';
                $re['create_at'] = $this->create_at;

            }
            $re['team_name'] = $teamObj ? $teamObj->getAttribute('team_name') : '';
        }
        return $re;
    }

    public function scenes(){
        return [
            'sign-datas' => [
                'create_at' => [
                    'required'=>'1',
                    'result'=>'SIGN_CREATE_AT_ID_EMPTY'
                ],
                'team_id' => [
                    //'required'=>'1',
                    //'result'=>'SIGN_TEAM_ID_EMPTY'
                ],
            ],
        ];
    }
}
