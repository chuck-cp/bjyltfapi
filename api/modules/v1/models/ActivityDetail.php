<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use api\modules\v1\models\LogOperation;
use api\modules\v1\models\Member;
use api\modules\v1\models\SystemAddress;
use common\libs\ToolsClass;
use Yii;
use yii\data\Pagination;
use yii\debug\models\search\Log;

class ActivityDetail extends ApiActiveRecord
{
    public $verify;
    public $reason;
    public $page;
    public static function tableName()
    {
        return '{{%activity_detail}}';
    }

    // 获取收益明细
    public function getIncomeDetail($activity_id) {
        $activityDetail = self::find()->select('id,shop_name,status')->where(['activity_id' => $activity_id])->orderBy('id desc');
        $pagination = new Pagination(['totalCount'=>$activityDetail->count(),'pageSize' => 9]);
        $pagination->validatePage = false;
        $activityDetail = $activityDetail->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        foreach ($activityDetail as $key => $value) {
            if ($value['status'] == 3) {
                // 推荐店铺类型为连锁店,查询该连锁店的所有店铺
                $activityDetail[$key]['list'] = ShopHeadquarters::getHeadquartersListByActivity($value['id']);
            }
        }
        return ['shop_list' => $activityDetail];
    }


    // 创建获取详情
    public function createActivityDetail($activity_id,$activity_member_mobile) {
        // 判断重复上传
        if (self::find()->where(['and',['activity_id' => $activity_id],['>','create_at',date('Y-m-d H:i:s',strtotime('-1 min'))]])->count()) {
            return 'REPEAT_SUBMIT';
        }
        try {
            $this->create_at = date('Y-m-d H:i:s');
            $this->area_name = SystemAddress::getAreaNameById($this->area_id);
            $this->activity_id = $activity_id;
            # 检查该手机号是否在系统中注册，如果已经注册并且有商家，把该店铺自动分配给他的上级
            $memberModel = Member::find()->where(['mobile' => $activity_member_mobile])->select('parent_id')->asArray()->one();
            if ($memberModel && !empty($memberModel['parent_id'])) {
                $parentMemberModel = Member::find()->where(['id' => $memberModel['parent_id']])->select('name')->asArray()->one();
                if (!empty($parentMemberModel)) {
                    $this->order_source = 1;
                    $this->custom_member_id = $memberModel['parent_id'];
                    $this->custom_member_name = $parentMemberModel['name'];
                }
            }
            $this->save();
            return 'SUCCESS';
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            return 'ERROR';
        }
    }

    /*
     * 获取签约店铺
     * */
    public function getContractShop() {
        $model = self::find()->where(['custom_member_id' => Yii::$app->user->id, 'is_apply'=>0])->orderBy('id desc');
        $pagination = new Pagination(['totalCount'=>$model->count(),'pageSize' => 8]);
        $pagination->validatePage = false;
        $detailModel = $model->select('id,shop_name,area_name,address,shop_image,status,order_source')->offset($pagination->offset)->limit($pagination->limit)->asArray()->all();
        return $detailModel;
    }

    /**
     *获取签约店铺详情
     */
    public static function getShopInfo($id){
        $detail = self::find()->where(['id'=>$id])->select('activity_id,shop_name,apply_name,apply_mobile,mirror_account,area_name,address,shop_image,status')->asArray()->one();
        if(empty($detail)){
            return [];
        }
        $active = Activity::find()->where(['id'=>$detail['activity_id']])->select('member_name,member_mobile')->asArray()->one();
        return array_merge($detail,$active);
    }

    /*
     * 签约失败
     * @param id int 店铺ID
     * */
    public function contractFailed($id) {
        $dbTrans = Yii::$app->db->beginTransaction();
        try {
            self::updateAll(['status' => 2],['id' => $id,'custom_member_id' => Yii::$app->user->id, 'status' => [0,2]]);
            if (!LogOperation::writeLog($id,1,$this->reason)) {
                throw new \Exception('写入签约失败原因失败');
            }
            $dbTrans->commit();
            return true;
        } catch (\Exception $e) {
            Yii::error($e->getMessage());
            $dbTrans->rollBack();
            return false;
        }
    }
    /*
     * 场景
     * */
    public function scenes()
    {
        return [
            'update' => [
                'reason' => [
                    'required' => '1',
                    'result' => 'FAILED_REASON_EMPTY'
                ],
            ],
            'create' => [
                'shop_name' => [
                    'required' => '1',
                    'result' => 'SHOP_NAME_EMPTY'
                ],
                'apply_name' => [
                    'required' => '1',
                    'result' => 'APPLY_NAME_EMPTY'
                ],
                'apply_mobile' => [
                    'required' => '1',
                    'result' => 'APPLY_MOBILE_EMPTY'
                ],
                'area_id' => [
                    'required' => '1',
                    'result' => 'AREA_ID_EMPTY'
                ],
                'address' => [
                    'required' => '1',
                    'result' => 'ADDRESS_EMPTY'
                ],
                'mirror_account' => [
                    'required' => '1',
                    'result' => 'MIRROR_ACCOUNT_EMPTY'
                ],
                'shop_image' => [
                    'required' => '1',
                    'result' => 'SHOP_IMAGE_EMPTY'
                ],
            ],
        ];
    }
}
