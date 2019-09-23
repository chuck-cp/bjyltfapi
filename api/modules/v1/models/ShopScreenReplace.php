<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use Yii;
use yii\base\Exception;

/**
 * This is the model class for table "{{%shop_screen_replace}}".
 *
 * @property string $id
 * @property string $shop_id
 * @property integer $replace_screen_number
 * @property string $install_member_id
 * @property integer $status
 * @property string $create_user_id
 * @property string $create_user_name
 * @property string $create_at
 */
class ShopScreenReplace extends ApiActiveRecord{
    public $install_name;
    public $install_mobile;
    public $install_images;
    public $isupdate;

    public $replace_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_screen_replace}}';
    }
    //关联shop
    public function getShopInfo(){
        return $this->hasOne(Shop::className(),['id'=>'shop_id'])->select('id,shop_image,name,area_name,address,install_member_id,install_team_id,status');
    }
    // 2、更换屏幕 3、拆除屏幕 4、新增屏幕 保存到replace表
    public function screenOperate($operateType=0){
        if(!$operateType){ return 'OPERATE_TYPE_CAN_NOT_EMPTTY'; }
        if($operateType == 4){
            $currentModel = self::findOne($this->replace_id);
            $ckre = ShopScreenReplace::checkIsInstallMember($this->replace_id);
            if($ckre != 'SUCCESS'){
                return $ckre;
            }
            if(!$currentModel){return 'REPLACE_SHOP_NOT_EXIST';}//换屏或新增或拆屏店铺不存在
            $screenArr = json_decode($this->install_images,true);
            //软件编号
            $solfArr = array_column($screenArr,'screen_number');
            $solfArr = array_filter($solfArr);
            //硬件编号
            $deviceArr = SystemDevice::getDevice($solfArr);
            $software_numbers = implode(',',$solfArr);
            $device_numbers = implode(',',$deviceArr);
            $shopArea = $this->getShopArea();
            if(!$shopArea){return 'SHOP_AREA_NOT_FOUND';}
            $inside = $this->getIsInside($shopArea['install_member_id']);
            if($inside === false){return 'INSTALL_MEMBER_NOT_FOUND';}
            $currentModel->install_price = SystemConfig::getConfig($this->getConfigId($shopArea['area'],$inside,1)) * count($deviceArr);
            $currentModel->install_device_number = $device_numbers;
            $currentModel->install_software_number = $software_numbers;
            $currentModel->replace_screen_number = count($solfArr);
            $currentModel->problem_description = $this->problem_description;
            $currentModel->status = 2;
        }
        if($operateType == 2){
            $currentModel = self::findOne($this->replace_id);
            if(!$currentModel){return 'REPLACE_SHOP_NOT_EXIST';}//换屏或新增或拆屏店铺不存在
            $ckre = ShopScreenReplace::checkIsInstallMember($this->replace_id);
            if($ckre != 'SUCCESS'){
                return $ckre;
            }
            if(!empty($this->install_software_number)){
                $software_numbers = implode(',',$this->install_software_number);
                $remove_devices = implode(',',$this->remove_device_number);
                $priceNum = SystemDevice::getDevice($this->install_software_number);
                $device_numbers = implode(',',$priceNum);
                $currentModel->install_software_number = $software_numbers;
                $currentModel->remove_device_number = $remove_devices;
                $currentModel->install_device_number = $device_numbers;
                $currentModel->replace_screen_number = count($priceNum);
                $currentModel->screen_status = 0;
            }else{
                $currentModel->screen_status = 1;
            }
            $currentModel->status = 2;
            $shopArea = $this->getShopArea();
            if(!$shopArea){return 'SHOP_AREA_NOT_FOUND';}
            $inside = $this->getIsInside($shopArea['install_member_id']);
            if($inside === false){return 'INSTALL_MEMBER_NOT_FOUND';}
            if($currentModel->install_price == 0){
                if(!isset($priceNum)){ $priceNum = []; }
                $currentModel->install_price = SystemConfig::getConfig($this->getConfigId($shopArea['area'],$inside,2)) * count($priceNum);
            }
            $currentModel->problem_description = $this->problem_description;
        }
        if($operateType == 3){
            $currentModel = self::findOne($this->id);
            $ckre = ShopScreenReplace::checkIsInstallMember($this->id);
            if($ckre != 'SUCCESS'){
                return $ckre;
            }
            if(!$currentModel){return 'REPLACE_SHOP_NOT_EXIST';}//拆屏时店铺未找到
            $remove_devices = implode(',',$this->remove_device_number);
            $currentModel->status = 2;
            $shopArea = $this->getShopArea('');
            if(!$shopArea){return 'SHOP_AREA_NOT_FOUND';}
            $inside = $this->getIsInside($shopArea['install_member_id']);
            if($inside === false){return 'INSTALL_MEMBER_NOT_FOUND';}
            $currentModel->install_price = SystemConfig::getConfig($this->getConfigId($shopArea['area'],$inside,3)) * count(array_filter($this->remove_device_number));
            $currentModel->remove_device_number = $remove_devices;
            //更改修改的屏幕数量
            $currentModel->replace_screen_number = count($this->remove_device_number);
            $currentModel->problem_description = $this->problem_description;
        }
        try{
            $currentModel->save();
            return 'SUCCESS';
        }catch (Exception $e){
            //print_r($e->getMessage());exit;
            Yii::error($e->getMessage(),'db');
            return 'ERROR';
        }
    }
    public function getIsInside($member_id){
        if($memberObj = MemberInfo::findOne($member_id)){
            return $memberObj->getAttribute('company_electrician');
        }
        return false;
    }
    public function getRemoveInfo(){
        $replaceInfo = self::find()->where(['id'=>$this->id])->select('id,shop_name,shop_area_name,shop_address,replace_screen_number,problem_description,remove_device_number,status')->asArray()->one();
        if(empty($replaceInfo)){
            return false;
        }
        $shopInfo = Shop::find()->where(['id'=>$this->shop_id])->select('member_name,member_mobile')->asArray()->one();
        $shopApplyInfo = ShopApply::find()->where(['id'=>$this->shop_id])->select('contacts_name,contacts_mobile')->asArray()->one();
        return array_merge($replaceInfo, $shopInfo, $shopApplyInfo);
    }

    /**
     * @param $area
     * @param int $inside
     * @param int $operate
     */
    public function getConfigId($area, $inside=1, $operate=2){
        $area_id = substr($area, 0, 9);
        $levelModel = SystemAddressLevel::find()->where(['area_id'=>$area_id])->asArray()->one();
        if(empty($levelModel)){
            return false;
        }
        $level = $levelModel['level'] >0 ? $levelModel['level'] : 3;
        $operateType = 'system_price_';
        switch ($operate){
            //1 新增 2 更换 3 拆除
            case 1:
                $operateType .= 'install';
                break;
            case 2:
                $operateType .= 'replace';
                break;
            case 3:
                $operateType .= 'remove';
                break;

        }
        return $operateType.'_'.$inside.'_'.$level;

    }

    /**
     * 获取店铺地区和安装人
     * @param string $id
     * @return array|bool|null|\yii\db\ActiveRecord
     */
    public function getShopArea($id='id'){
        $where = [];
        if($id == 'id'){
            $where = ['id'=>$this->id];
            $where2 = ['id'=>$this->replace_id];
        }else{
            $where = ['id'=>$this->shop_id];
            $where2 = ['id'=>$this->id];
        }
        $replace = self::find()->where($where2)->asArray()->one();
        $shopArea = Shop::find()->where($where)->select('area')->asArray()->one();
        if(empty($replace) || empty($shopArea)){
            return false;
        }
        $shopArea['install_member_id'] = $replace['install_member_id'];
        return $shopArea;
    }

    public static function checkIsInstallMember($id,$type='replace',$checkStatus=true){
        $currentInstall = Yii::$app->user->id;
        if($type == 'replace'){
            $replaceModel = self::findOne($id);
            if($replaceModel->getAttribute('install_member_id') != $currentInstall){
                return 'INSTALL_MEMBER_ID_CHANGED';
            }
            if($checkStatus){
                if (!in_array($replaceModel->status,[1,3])) {
                    return 'REPEAT_SUBMIT';
                }
            }
            return 'SUCCESS';
        }
        $shopObj = Shop::findOne($id);
        if($checkStatus){
            if(!in_array($shopObj->status, [1,2,4])){
                return 'REPEAT_SUBMIT';
            }
        }
        if($shopObj->getAttribute('install_member_id') != $currentInstall){
            return 'INSTALL_MEMBER_ID_CHANGED';
        }
        return 'SUCCESS';
    }

    /**
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getReplaceMiantainList(){
        return self::find()->where(['and',['install_member_id'=>Yii::$app->user->id],['>','maintain_type','1'],['<','status','4']])->select('id,maintain_type,shop_id,shop_name,shop_image,shop_address,replace_screen_number,status,screen_status,shop_area_name,assign_time as tm,assign_at')->asArray()->all();
    }
    public function scenes(){
        return [
            //新增屏幕
            'screen-incr'=>[
                'install_name'=>[],
                'install_mobile'=>[],
                'install_images'=>[
                    //'required' => '1',
                    //'result' => 'INSTALL_IMAGES_CAN_NOT_EMPTY',
                ],
                'problem_description' => [],
                'id'=>[
                    'required' => '1',
                    'result' => 'SHOP_ID_NOT_EMPTY',
                ],
                'replace_id'=>[
                    'required' => '1',
                    'result' => 'REPLACE_ID_CAN_NOT_EMPTY',
                ],
                'isupdate'=>[]
            ],
            //屏幕更换
            'change-post-new-check' => [
                'id'=>[
                    'required' => '1',
                    'result' => 'SHOP_ID_NOT_EMPTY',
                ],
                'replace_id' => [
                    'required' => '1',
                    'result' => 'REPLACE_ID_CAN_NOT_EMPTY',
                ],
                'install_software_number' => [],
                'remove_device_number' => [],
                'problem_description' => [
                    'required' => '1',
                    'result' => 'REPLACE_PROBLEM_DESCRIPTION_CAN_NOT_EMPTY',
                ]
            ],
            //拆除屏幕时获取信息
            'remove-screen-info' => [
                'id' => [
                    'required' => '1',
                    'result' => 'REPLACE_ID_CAN_NOT_EMPTY',
                ],
                'shop_id' => [
                    'required' => '1',
                    'result' => 'REPLACE_SHOP_ID_CAN_NOT_EMPTY',
                ],
            ],
            //拆屏时提交（带验证）
            'remove-screen-check' => [
                'id' => [
                    'required' => '1',
                    'result' => 'REPLACE_ID_CAN_NOT_EMPTY',
                ],
                'shop_id' => [
                    'required' => '1',
                    'result' => 'REPLACE_SHOP_ID_CAN_NOT_EMPTY',
                ],
                'remove_device_number' => [
                    'required' => '1',
                    'result' => 'REPLACE_ID_CAN_NOT_EMPTY',
                ],
                'problem_description' => []
            ],
        ];
    }
}
