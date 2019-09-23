<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use api\modules\v1\models\Member;
use api\modules\v1\models\MemberEquipment;
use Yii;


class Activity extends ApiActiveRecord
{

    public $verify;
    public static function tableName()
    {
        return '{{%activity}}';
    }

    // 获取密钥
    public function getActivityToken() {
        return md5($this->member_name.'___'.$this->member_mobile.'___activity');
    }

    // 创建活动用户
    public function createActivity() {
        try {
            $activityModel = self::find()->where(['member_mobile' => $this->member_mobile])->select('activity_token,member_mobile,member_name')->asArray()->one();
            if ($activityModel) {
                return $activityModel;
            }
            $this->member_name = $this->member_mobile;
            $this->activity_token = $this->getActivityToken();
            $this->save();
            return ['activity_token' => $this->activity_token, 'member_mobile' => $this->member_mobile, 'member_name' => $this->member_name];
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            return false;
        }
    }

    /*
     * 根据密钥获取用户的ID和活动密钥
     * 说明:如果是通过APP过来的用户,检查是否在活动表注册,没有写入、有责直接返回。
     * 微信端过来的用户,返回获取失败
     * */
    public function getMemberIdByToken($token)
    {
        try {
            $equipmentModel = MemberEquipment::find()->where(['token' => $token])->select('member_id')->asArray()->one();
            if ($equipmentModel) {
                $memberModel = Member::find()->where(['id' => $equipmentModel['member_id']])->select('mobile,name')->asArray()->one();
                if (empty($memberModel) || empty($memberModel['mobile'])) {
                    return false;
                }
                $activityModel = Activity::find()->where(['member_mobile' => $memberModel['mobile']])->select('activity_token')->asArray()->one();
                if (empty($activityModel)) {
                    $this->member_name = $memberModel['name'];
                    $this->member_mobile = $memberModel['mobile'];
                    $this->activity_token = $this->getActivityToken();
                    $this->save();
                    return ['activity_token' => $this->activity_token];
                }
                return $activityModel;
            }
            return false;
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            return false;
        }
    }

    /*
     * 场景
     * */
    public function scenes()
    {
        return [
            'create' => [
                'member_mobile' => [
                    'required' => '1',
                    'result' => 'MOBILE_EMPTY'
                ],
                'verify' => [
                    'required' => '1',
                    'result' => 'VERIFY_EMPTY'
                ],
            ],
        ];
    }
}
