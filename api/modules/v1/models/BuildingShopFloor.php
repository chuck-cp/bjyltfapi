<?php

namespace api\modules\v1\models;
use api\modules\v1\models\SystemAddress;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "{{%building_shop_floor}}".
 *
 * @property string $id
 * @property string $company_id
 * @property string $shop_name
 * @property integer $shop_level
 * @property integer $shop_type
 * @property string $contact_name
 * @property string $contact_mobile
 * @property integer $floor_number
 * @property integer $low_floor_number
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
 * @property string $floor_image
 * @property string $other_image
 * @property string $screen_start_at
 * @property string $screen_end_at
 * @property string $create_at
 * @property string $install_finish_at
 */
class BuildingShopFloor extends \api\core\ApiActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%building_shop_floor}}';
    }

    public function beforeSave($insert){
        if($insert){
            $this->floor_type = $this->floor_type == 0 ? 1 : 2;
            $this->member_id = Yii::$app->user->id;
        }
        return parent::beforeSave($insert);
    }

    /**
     * 创建楼宇
     * @return bool
     */
    public function createBuild(){
        try{
            $this->save();
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(), 'error');
            return false;
        }
    }
    /**
     * @return bool
     * 判断是否是短时间内重复提交
     */
    public function judgeIsRepeatPost(){
        $create_at = date('Y-m-d H:i:s',strtotime("-1 minute"));
        if(self::find()->where(['and',['shop_name'=>$this->shop_name],['area_id'=>$this->area_id],['>','create_at',$create_at]])->count()){
            return false;
        }
        return true;
    }
    /**
     * 获取我的楼宇列表
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getMyBuildings(){
        $data = self::find()->where(['member_id' => Yii::$app->user->id])->select('id, shop_name')->asArray()->all();
        if(!empty($data)){
            return ArrayHelper::map($data,'id','shop_name');
        }
        return [];
    }

    /**
     * 获取我的楼宇海报或者Led安装任务
     * @return array
     */
    public static function getMyBuildTaskList(){
        $ledShopData = self::find()->select('id, shop_name, shop_image, led_screen_number as screen_number, led_examine_status as examine_status, CONCAT(`province`,`city`,`area`,`street`) as shop_area_name, address, led_create_at as tm, DATE(led_install_assign_at) as assign_at, led_install_member_id as led, led_install_member_id as build, led_examine_status as status')->where(['and', ['<','led_examine_status',5], ['led_install_member_id'=>Yii::$app->user->id]])->asArray()->all();

        $posterShopData = self::find()->select('id,shop_name,shop_image,poster_screen_number  as screen_number,poster_examine_status as examine_status, CONCAT(`province`,`city`,`area`,`street`) as shop_area_name, address,poster_create_at as tm, DATE(poster_install_assign_at) as assign_at, poster_install_member_id as poster, poster_install_member_id as build, poster_examine_status as status')->where(['and', ['<','poster_examine_status',5], ['poster_install_member_id'=>Yii::$app->user->id]])->asArray()->all();
        return array_merge($ledShopData,$posterShopData);
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

    // 获取安装业务列表页数据
    public function getInstallShopList()
    {
        $ledShopData = self::find()->select('led_create_at as create_at,id,shop_name,shop_image,led_screen_number,led_examine_status as examine_status,province,city,area,address,street')->where(['and',['member_id' => Yii::$app->user->id],['<','led_examine_status',5]])->asArray()->all();
        $posterShopData = self::find()->select('poster_create_at as create_at,id,shop_name,shop_image,poster_screen_number,poster_examine_status as examine_status,province,city,area,address,street')->where(['and',['member_id' => Yii::$app->user->id],['<','poster_examine_status',5]])->asArray()->all();
        return array_merge($ledShopData,$posterShopData);
    }

    // 检查该公司ID是否属于我创建的
    public function checkCompanyId($id)
    {
        return BuildingCompany::isMy($id);
    }

    // 检查审核状态是否是被驳回
    public function checkExamineStatus()
    {
        if ($this->led_examine_status == 1 || $this->poster_examine_status == 1) {
            return true;
        }
        return false;
    }

    /**
     * 楼宇总提交
     * @param $id
     * @param $screen_type
     * @param $shop_type
     * @return bool|string
     */
    public function devicePost($id, $screen_type, $shop_type){
        try{
            $obj = self::findOne($id);
            if(!$obj){
                throw new \yii\db\Exception(self::tableName().'表中id为：'.$id. '的记录未找到');
            }
            $field1 = $screen_type == 1 ? 'led_examine_status' : 'poster_examine_status';
            $field2 = $screen_type == 1 ? 'led_total_screen_number' : 'poster_total_screen_number';
            if($obj->$field1 !== -1){
                return 'RECORD_STATUS_ERROR';
            }
            $obj->$field1 = 0;
            $obj->$field2 = BuildingShopPosition::find()->where(['shop_id'=>$id, 'screen_type'=>$screen_type, 'shop_type'=>$shop_type])->sum('screen_number');
            $obj->save();
            return true;
        }catch (\yii\db\Exception $e){
            Yii::error($e->getMessage().' at line '.$e->getLine());
            return false;
        }
    }

    public static function getFloors($id){
        return self::find()->where(['id'=>$id])->select('floor_number,low_floor_number')->asArray()->one();
    }

    public function scenes(){
        return [
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
                    [
                        [
                            'required' => 1,
                            'result' => 'AREA_ID_EMPTY',
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
                'floor_type'=>[
                    'required'=>'1',
                    'result'=>'SHOP_TYPE_EMPTY'
                ],
                'floor_number'=>[
                    'required'=>'1',
                    'result'=>'FLOOR_NUMBER_EMPTY'
                ],
                'low_floor_number'=>[
                    'required'=>'1',
                    'result'=>'LOW_FLOOR_NUMBER_EMPTY'
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
                'floor_image' => [
                    'required'=>'1',
                    'result'=>'FLOOR_IMAGE_EMPTY'
                ],
                'other_image'=>[],
                'description'=>[]
            ],
            'create-build' => [
                'company_id' => [
                    'required' => 1,
                    'result' => 'BUILD_COMPANY_ID_EMPTY',
                ],
                'contact_name' => [
                    'required' => 1,
                    'result' => 'BUILD_CONTACT_NAME_EMPTY',
                ],
                'contact_mobile' => [
                    'required' => 1,
                    'result' => 'BUILD_CONTACT_MOBILE_EMPTY',
                ],
                'shop_name' => [
                    'required' => 1,
                    'result' => 'BUILD_SHOP_NAME_EMPTY',
                ],
                'area_id' => [
                    [
                        [
                            'required' => 1,
                            'result' => 'BUILD_SHOP_ID_EMPTY',
                        ],
                        [
                            'function'=>'this::checkAreaFormat',
                            'result'=>'AREA_ERROR'
                        ],
                    ]
                ],
                'shop_level' => [
                    'required' => 1,
                    'result' => 'BUILD_SHOP_LEVEL_EMPTY',
                ],
                'address' => [
                    'required' => 1,
                    'result' => 'BUILD_ADDRESS_EMPTY',
                ],

                'floor_number' => [
                    'required' => 1,
                    'result' => 'BUILD_FLOOR_NUMBER_EMPTY',
                ],
                'low_floor_number' => [
                    'required' => 1,
                    'result' => 'BUILD_LOW_FLOOR_NUMBER_EMPTY',
                ],

                'description' => [],
                'shop_image' => [
                    'required' => 1,
                    'result' => 'BUILD_SHOP_IMAGE_EMPTY',
                ],
                'plan_image' => [
                    'required' => 1,
                    'result' => 'BUILD_PLAN_IMAGE_EMPTY',
                ],
                'floor_image' => [
                    'required' => 1,
                    'result' => 'BUILD_FLOOR_IMAGE_EMPTY',
                ],
                'other_image' => [],
            ],
        ];
    }
}
