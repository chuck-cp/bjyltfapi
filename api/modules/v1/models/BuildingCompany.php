<?php

namespace api\modules\v1\models;
use api\modules\v1\models\SystemAddress;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%building_company}}".
 *
 * @property string $id
 * @property string $member_id
 * @property string $member_name
 * @property string $member_mobile
 * @property string $led_member_price
 * @property string $poster_member_price
 * @property string $apply_name
 * @property string $apply_mobile
 * @property string $led_apply_price
 * @property string $poster_apply_price
 * @property string $company_name
 * @property string $area_id
 * @property string $registration_mark
 * @property string $description
 * @property string $agreement_name
 * @property string $identity_card_front
 * @property string $identity_card_back
 * @property string $business_licence
 * @property string $other_image
 */
class BuildingCompany extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%building_company}}';
    }

    /**
     * 創建公司
     * @return bool
     */
    public function createCompany()
    {
        try {
            $this->member_id = Yii::$app->user->identity->getId();
            $this->member_name = Yii::$app->user->identity->name;
            $this->member_mobile = Yii::$app->user->identity->mobile;
            $this->save();
            return true;
        } catch (Exception $e) {
            \Yii::error($e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * @return bool
     * 判断是否是短时间内重复提交
     */
    public function judgeIsRepeatPost(){
        $create_at = date('Y-m-d H:i:s',strtotime("-1 minute"));
        if(self::find()->where(['and',['member_id'=>Yii::$app->user->id],['company_name'=>$this->company_name],['area_id'=>$this->area_id],['>','create_at',$create_at]])->count()){
            return false;
        }
        return true;
    }
    /*
     * 检查该公司是否属于我
     * */
    public static function isMy($id)
    {
        return BuildingCompany::find()->where(['id' => $id, 'member_id' => Yii::$app->user->id])->count();
    }

    /*
     * 获取公司信息
     * */
    public function getCompanyData($where,$filed = '*',$result = 'one') {
        if (!is_array($where)) {
            $where = ['id' => $where];
        }
        if ($result == 'many') {
            return BuildingCompany::find()->where($where)->select($filed)->asArray()->all();
        } else {
            $companyModel = BuildingCompany::find()->where($where)->select($filed)->asArray()->one();
            if (isset($companyModel['other_image'])) {
                $companyModel['other_image'] = explode(",",$companyModel['other_image']);
            }
            return $companyModel;
        }
    }

    // 检查是否可以修改数据
    public function checkIsUpdate()
    {
        $shopModel = BuildingShopFloor::find()->where(['and',['company_id' => $this->id], ['or',['>','led_examine_status',1],['>','poster_examine_status',1]]])->count();
        if ($shopModel) {
            return false;
        }
        $shopModel = BuildingShopPark::find()->where(['and', ['company_id' => $this->id], ['>','poster_examine_status',1]])->count();
        if ($shopModel) {
            return false;
        }
        return true;
    }


    /**
     * 獲取我的物業公司列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getMyCompanyList($app){
        $data = self::find()->where(['member_id' => \Yii::$app->user->id])->select('id,company_name')->asArray()->all();
        if($app){
            return array_column($data, 'company_name');
        }
        if(!empty($data)){
            return ArrayHelper::map($data,'id','company_name');
        }
        return [];
    }

    /*
    * 验证地区格式
    * */
    public function checkAreaFormat($area_id){
        if(strlen($area_id) == 12){
            $this->province = SystemAddress::getName(substr($area_id,0,5));
            $this->city = SystemAddress::getName(substr($area_id,0,7));
            $this->area = SystemAddress::getName(substr($area_id,0,9));
            $this->street = SystemAddress::getName(substr($area_id,0,12));
            return true;
        }
        return false;
    }

    public function scenes(){
        return [
            'create-company' => [
                'company_name' => [
                    'required' => 1,
                    'result' => 'COMPANY_NAME_EMPTY'
                ],
                'area_id'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'AREA_EMPTY'
                        ],
                        [
                            'function'=>'this::checkAreaFormat',
                            'result'=>'AREA_ERROR'
                        ],
                    ]
                ],
                'address' => [
                    'required' => 1,
                    'result' => 'ADDRESS_EMPTY',
                ],
                'registration_mark' => [
                    'required' => 1,
                    'result' => 'REGISTRATION_MARK_EMPTY',
                ],
                'apply_name' => [
                    'required' => 1,
                    'result' => 'APPLY_NAME_EMPTY',
                ],
                'apply_mobile' => [
                    'required' => 1,
                    'result' => 'APPLY_MOBILE_EMPTY',
                ],
                'description' => [],
                'identity_card_front' => [],
                'identity_card_back' => [],
                'business_licence' => [
                    'required' => 1,
                    'result' => 'BUSINESS_LICENCE_EMPTY',
                ],
                'other_image' => [],
            ],
            'update'=>[
                'company_name'=>[
                    [
                        [
                            'function' => 'this::checkIsUpdate',
                            'result' => 'EXAMINE_STATUS_ERROR'
                        ]
                    ],
                    [
                        'required'=>'1',
                        'result'=>'COMPANY_NAME_EMPTY'
                    ]
                ],
                'apply_name'=>[
                    'required'=>'1',
                    'result'=>'APPLY_NAME_EMPTY'
                ],
                'apply_mobile'=>[
                    'required'=>'1',
                    'result'=>'APPLY_MOBILE_EMPTY'
                ],
                'area_id'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'AREA_ID_EMPTY'
                        ],
                        [
                            'function'=>'this::checkAreaFormat',
                            'result'=>'AREA_ERROR'
                        ],
                    ]
                ],
                'address'=>[
                    'required'=>'1',
                    'result'=>'ADDRESS_EMPTY'
                ],
                'registration_mark'=>[
                    'required'=>'1',
                    'result'=>'REGISTERATION_MARK_EMPTY'
                ],
                'identity_card_front'=>[
                    'required'=>'1',
                    'result'=>'IDENTITY_CARD_FRONT_EMPTY'
                ],
                'identity_card_back'=>[
                    'required'=>'1',
                    'result'=>'IDENTITY_CARD_BACK_EMPTY'
                ],
                'business_licence'=>[
                    'required'=>'1',
                    'result'=>'BUSINESS_LICENCE_EMPTY'
                ],
                'other_image'=>[],
                'description'=>[]
            ],
        ];
    }
}
