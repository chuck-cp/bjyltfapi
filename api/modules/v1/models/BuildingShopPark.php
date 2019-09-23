<?php

namespace api\modules\v1\models;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%building_shop_park}}".
 *
 * @property string $id
 * @property string $company_id
 * @property string $shop_name
 * @property integer $shop_level
 * @property string $contact_name
 * @property string $contact_mobile
 * @property string $area_id
 * @property string $province
 * @property string $city
 * @property string $area
 * @property string $address
 * @property string $description
 * @property integer $led_screen_number
 * @property integer $poster_screen_number
 * @property string $shop_image
 * @property string $plan_image
 * @property string $other_image
 * @property string $create_at
 * @property string $install_finish_at
 */
class BuildingShopPark extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%building_shop_park}}';
    }

    public function beforeSave($insert){
        if($insert){
            $this->member_id = Yii::$app->user->id;
        }
        return parent::beforeSave($insert);
    }

    /**
     * 创建公园
     * @return bool
     */
    public function createPark(){
        try{
            $this->save();
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(), 'error');
            return false;
        }
    }

    /**
     * 获取我的公园列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getMyParks(){
        $data = self::find()->where(['member_id'=>Yii::$app->user->id])->select('id, shop_name')->asArray()->all();
        if(!empty($data)){
            return [
                'list' => ArrayHelper::map($data,'id','shop_name'),
                'config_id' => self::getTopId(),
            ];
        }
        return [];
    }
    //获取公园安装的一级config_id
    private static function getTopId(){
        $configData = BuildingPositionConfig::find()->where(['parent_id'=>0, 'shop_type'=>2, 'screen_type'=>2])->select('id')->asArray()->one();
        return $configData['id'];
    }
    // 获取安装业务列表页数据
    public function getInstallShopList()
    {
        $posterShopData = self::find()->select('poster_create_at as create_at,id,shop_name,shop_image,poster_screen_number,poster_examine_status as examine_status,province,city,area,address,street')->where(['and',['member_id' => Yii::$app->user->id],['<','poster_examine_status',5]])->asArray()->all();
        return $posterShopData;
    }

    /**
     * 我的公园安装任务列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getMyParkTaskList(){
        return self::find()->select('id,shop_name,shop_image,poster_screen_number  as screen_number,poster_examine_status as examine_status, CONCAT(`province`,`city`,`area`,`street`) as shop_area_name, address,poster_create_at as tm, DATE(poster_install_assign_at) as assign_at, poster_install_member_id as poster, poster_install_member_id as park, poster_examine_status as status')->where(['and', ['<','poster_examine_status',5], ['poster_install_member_id'=>Yii::$app->user->id]])->asArray()->all();
    }
    // 检查该公司ID是否属于我创建的
    public function checkCompanyId($id)
    {
        return BuildingCompany::isMy($id);
    }

    // 检查审核状态是否是被驳回
    public function checkExamineStatus()
    {
        if ($this->poster_examine_status == 1) {
            return true;
        }
        return false;
    }

    /*
     * 是否有安装权限
     * @param screen_type int 设备类型
     * */
    public function isInstallAuth($screen_type)
    {
        $member_id = Yii::$app->user->id;
        if ($screen_type == BuildingShop::SCREEN_LED) {
            return $this->led_install_member_id == $member_id;
        } else {
            return $this->poster_install_member_id == $member_id;
        }
        return false;
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
            'create-park' => [
                'company_id' => [
                    'required' => 1,
                    'result' => 'PARK_COMPANY_ID_EMPTY'
                ],
                'contact_name' => [
                    'required' => 1,
                    'result' => 'PARK_CONTACT_NAME_EMPTY',
                ],
                'contact_mobile' => [
                    'required' => 1,
                    'result' => 'PARK_CONTACT_MOBILE_EMPTY',
                ],
                'shop_name' => [
                    'required' => 1,
                    'result' => 'PARK_SHOP_NAME_EMPTY',
                ],
                'area_id' => [
                    [
                        [
                            'required' => 1,
                            'result' => 'PARK_SHOP_ID_EMPTY',
                        ],
                        [
                            'function'=>'this::checkAreaFormat',
                            'result'=>'AREA_ERROR'
                        ],
                    ]
                ],
                'floor_number'=>[
                    'required'=>'1',
                    'result'=>'PARK_FLOOR_NUMBER_EMPTY'
                ],
                'address' => [
                    'required' => 1,
                    'result' => 'PARK_ADDRESS_EMPTY',
                ],
                'description' => [],
                'shop_image' => [
                    'required' => 1,
                    'result' => 'PARK_SHOP_IMAGE_EMPTY',
                ],
                'shop_level' => [
                    'required' => 1,
                    'result' => 'PARK_SHOP_LEVEL_EMPTY',
                ],
                'plan_image' => [
                    'required' => 1,
                    'result' => 'PARK_PLAN_IMAGE_EMPTY',
                ],
                'other_image' => [],

            ],
            'update'=>[
                'company_id'=>[
                    [
                        [
                            'function' => 'this::checkCompanyId',
                            'result' => 'COMPANY_ID_ERROR'
                        ],
                        [
                            'function' => 'this::checkExamineStatus',
                            'result' => 'EXAMINE_STATUS_ERROR'
                        ]
                    ],
                    [
                        'required'=>'1',
                        'result'=>'COMPANY_ID_EMPTY'
                    ]
                ],
                'contact_name'=>[
                    'required'=>'1',
                    'result'=>'CONTACT_NAME_EMPTY'
                ],
                'contact_mobile'=>[
                    'required'=>'1',
                    'result'=>'CONTACT_MOBILE_EMPTY'
                ],
                'shop_name'=>[
                    'required'=>'1',
                    'result'=>'SHOP_NAME_EMPTY'
                ],
                'area_id'=>[
                    'required'=>'1',
                    'result'=>'COMPANY_NAME_EMPTY'
                ],
                'floor_number'=>[
                    'required'=>'1',
                    'result'=>'PARK_FLOOR_NUMBER_EMPTY'
                ],
                'street'=>[
                    'required'=>'1',
                    'result'=>'STREET_EMPTY'
                ],
                'area'=>[
                    'required'=>'1',
                    'result'=>'AREA_EMPTY'
                ],
                'city'=>[
                    'required'=>'1',
                    'result'=>'CITY_EMPTY'
                ],
                'province'=>[
                    'required'=>'1',
                    'result'=>'PROVINCE_EMPTY'
                ],
                'shop_level'=>[
                    'required'=>'1',
                    'result'=>'SHOP_LEVEL_EMPTY'
                ],
                'shop_image' => [
                    'required'=>'1',
                    'result'=>'SHOP_IMAGE_EMPTY'
                ],
                'plan_image' => [
                    'required'=>'1',
                    'result'=>'PLAN_IMAGE_EMPTY'
                ],
                'other_image'=>[],
                'description'=>[]
            ],
        ];
    }

}
