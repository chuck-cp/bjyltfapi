<?php

namespace api\modules\v1\models;

use common\libs\Redis;
use pms\modules\count\count;
use Yii;
use yii\base\Exception;
use api\modules\v1\models\Shop;
use api\modules\v1\models\ShopScreenReplaceList;
use api\modules\v1\models\ShopScreenReplace;
/**
 * 屏幕管理
 */
class Screen extends \api\core\ApiActiveRecord
{
    public $install_software_number;
    public $remove_device_number;
    public $new_images;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%screen}}';
    }
    /*
     * 获取屏幕
     * */
    public function getShopScreens($shop_id){
        if($resultScreen = self::find()->where(['shop_id'=>$shop_id])->select('name,status')->asArray()->all()){
            foreach ($resultScreen as $key => $value) {
                $resultScreen[$key]['name'] = "屏幕".($key + 1);
            }
            return $resultScreen;
        }
        $shopModel = Shop::find()->where(['id'=>$shop_id])->select('screen_number')->asArray()->one();
        if(empty($shopModel)){
            return [];
        }
        for($i = 1;$i <= $shopModel['screen_number']; $i++){
            $result[] = [
              'name'=>'屏幕'.$i,
              'status'=>0
            ];
        }
        return $result;
    }
/*
* 获取屏幕编码
* */
    public function getScteensNumber($shop_id){
        $shopModel = self::find()->where(['shop_id'=>$shop_id])->select('software_number')->asArray()->all();
        if(empty($shopModel)){
            return [];
        }
        return $shopModel;
    }
    /*
    * 获取屏幕id
    * */
    public function getScteensId($shop_id){
        $shopModel = self::find()->where(['shop_id'=>$shop_id])->select('id')->asArray()->all();
        if(empty($shopModel)){
            return [];
        }
        return $shopModel;
    }

    /*
    *   获取屏幕是否激活
    * */
    public function getActivation($shop_id)
    {
        $shopModel = self::find()->where(['and',['shop_id'=>$shop_id],['status'=>0]])->select('id')->asArray()->all();
        if(empty($shopModel)){
            return true;
        }
        return false;
    }
    /*
    *   获取店铺安装屏幕数量
    * */
    public function getScteensNumberunline($shop_id){
        $shopModel = Shop::find()->where(['id'=>$shop_id])->select('screen_number,,')->asArray()->all();
        if(empty($shopModel)){
            return [];
        }
        $shopData=array();
        for($i=1;$i<=$shopModel[0]['screen_number'];$i++){
            $shopData[]['id']=$shop_id.'000'.$i;
        }
        return $shopData;
    }
    /*
    *   获取线下安装未激活的屏幕编码
    * */
    public function getActivationunline($shop_id)
    {
        $shopModel = self::find()->where(['and',['shop_id'=>$shop_id],['status'=>0]])->select('id,software_number')->asArray()->all();
        return $shopModel;
    }
    /*
    *   屏幕管理  获取店铺安装屏幕数量
    * */
    public function getScteens($shop_id, $replace_id=0){
        if(!$replace_id){
            $shopModel = Shop::find()->where(['id'=>$shop_id])->select('screen_number,install_member_id,install_team_id,install_member_name,install_mobile')->asArray()->one();
            if(!empty($shopModel) && $shopModel['install_team_id'] > 0){
                $install = $this->getInstallInfo($shopModel['install_team_id']);
            }
        }else{
            $shopModel = ShopScreenReplace::find()->where(['id'=>$replace_id])->select('replace_screen_number as screen_number')->asArray()->one();
        }
        if(empty($shopModel)){
            return [];
        }
        $shopData = [];
        for($i=1; $i<=$shopModel['screen_number']; $i++){
            $shopData[]['id']=$shop_id.'000'.$i;
        }
        $shopModel['shopData']=$shopData;
        if(isset($install)){
            $shopModel['team_member_name'] = $install['team_member_name'];
            $shopModel['team_member_mobile'] = $install['team_member_mobile'];
        }

        return $shopModel;
    }

    /**
     * @param $install_team_id
     * @return bool
     */
    public function getInstallInfo($install_team_id){
        $teamModel=MemberTeam::find()->where(['id'=>$install_team_id])->select('team_member_id')->asArray()->one();
        if(empty($teamModel)){ return false; }
        $memberModel=Member::find()->where(['id'=>$teamModel['team_member_id']])->select('name,mobile')->asArray()->one();
        $shopModel['team_member_name']=$memberModel['name'];
        $shopModel['team_member_mobile']=$memberModel['mobile'];
        return $shopModel;
    }
    /*
*   屏幕管理 获取安装未激活的屏幕编码
* */
    public function getScreenactivation($shop_id)
    {
        $shopscreenModel = self::find()->where(['and',['shop_id'=>$shop_id],['status'=>0]])->select('id,shop_id,software_number')->asArray()->all();
        if(empty($shopscreenModel)){
            return [];
        }
        $shopModel = Shop::find()->where(['id'=>$shop_id])->select('screen_number,install_member_id,install_team_id,install_member_name,install_mobile')->asArray()->one();
        if(empty($shopModel)){
            return [];
        }
        if($shopModel['install_team_id']>0){
            $teamModel=MemberTeam::find()->where(['id'=>$shopModel['install_team_id']])->select('team_member_id')->asArray()->one();
            $memberModel=Member::find()->where(['id'=>$teamModel['team_member_id']])->select('name,mobile')->asArray()->one();
            $shopModel['team_member_name']=$memberModel['name'];
            $shopModel['team_member_mobile']=$memberModel['mobile'];
        }
        $shopModel['shopData']=$shopscreenModel;
        return $shopModel;
    }
    /*
     * 更换屏幕后激活失败再次更换屏幕的提交
     */
    public function changeFailPost($params){
        $solfts = json_decode($params['install_images'],true);
        if(empty($solfts)){
            return 'ERROR';
        }
        $software_number = [];
        foreach ($solfts as $v){
            //入屏幕管理平台软件编码
            $software_number[] = $v['screen_number'];
            //删除screen列表
            $shopScreen = Screen::find()->where(['id'=>$v['id']])->asArray()->one();
            $delArr[] = $shopScreen['number'];
            $delSoft[] = $shopScreen['software_number'];
            $new_images[] = $v['image'];
        }
        $this->new_images = $new_images;
        if(!$params['replace_id']){
            $this->replace_id = 0;
        }else{
            $this->replace_id = $params['replace_id'];
        }

        //1.验证屏幕是否在device表中出库,并获取设备硬件编号
        if(!$device_numbers = SystemDevice::checkIsOut($software_number)){
            return 'SCREEN_NOT_EXIST';
        }
        $dbTrans = Yii::$app->db->beginTransaction();
        try{
            //2-1:删除设备
            if(!$this->delScreen($params['id'],$delArr)){
                return 'DELETE_FROM_SCREEN_FAIL';
            }
            //2-2:添加设备
            if('SUCCESS' != $this->addScreen($params['id'],$software_number)){
                return 'ADD_TO_SCREEN_FAIL';
            }
            if(!self::screenRedisOperate('delete',implode(',',$delSoft))){
                return 'DEL_WRITE_LIST_FAIL'; //删除的数据写入队列失败
            }
            //入综合事业部库
            if(!self::storePost($params['id'],$device_numbers,$params['replace_id'])){
                return 'STORE_TO_STORAGE_FAIL'; //综合事业部入库失败
            }
            //3.更改状态
            ShopScreenReplace::updateAll(['status'=>2, 'install_device_number'=>implode(',',array_column($device_numbers,'realNum')), 'install_software_number'=>implode(',',$software_number)],['id'=>$params['replace_id']]);
            $dbTrans->commit();
            return 'SUCCESS';
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            $dbTrans->rollBack();
            return 'ERROR';
        }
    }

    //屏幕入庫 屏幕出庫(screen表)
    public function screenInOut($operate=0){
        //load加载的id实际上是shop_id
        if(!Shop::findOne($this->id)){
            return 'SHOP_NOT_EXIST'; //店铺不存在
        }
        if(empty($this->install_software_number) && empty($this->remove_device_number)){
            return 'SHOP_NOT_NEED_OPERATE'; //无需操作
        }
        //换屏  2、更换屏幕 3、拆除屏幕 4、新增屏幕
        if($operate == 2){
            if(count($this->install_software_number) != count($this->remove_device_number)){
                return 'INCR_AND_DEL_NUMBER_NOT_SAME'; //增减屏幕数量不一致
            }
            //减
            if(!$this->delScreen($this->id, $this->remove_device_number)){
                return 'DEL_FROM_SCREEN_FAIL';
            }
            //减 redis
            $delSolfArr = SystemDevice::getSolf($this->remove_device_number);
            if(!self::screenRedisOperate('delete',implode(',',$delSolfArr))){
                return 'DEL_WRITE_LIST_FAIL'; //删除的数据写入队列失败
            }
            //增
            $addRes = $this->addScreen($this->id,$this->install_software_number);
            if($addRes != 'SUCCESS'){
                return $addRes;
            }
            //增 redis
            if(!self::storePost($this->id,SystemDevice::checkIsOut($this->install_software_number),$this->replace_id)){
                return 'ADD_WRITE_LIST_FAIL'; //增加的数据写入队列失败
            }
            return 'SUCCESS';
        }
        //拆屏
        if($operate == 3){

        }
    }
    //删除屏幕
    public function delScreen($shop_id, $delArr){
        try{
            foreach ($delArr as $k =>$v){
                $delModel = self::find()->where(['shop_id'=>$shop_id, 'number'=>$v])->one();
                if(!$delModel){
                    return 'DEL_DEVICE_NOT_FOUND'; //屏幕表中未找到要删除的表
                }
                $delModel->delete();
            }
            return true;
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }
    //增加屏幕
    public function addScreen($shop_id, $incrArr){
        try{
            //首先验证设备是否合法，若合法获得硬件编号
            $deviceArr = SystemDevice::getDevice($incrArr);
            if(!$deviceArr){
                return 'DEVICE_SYSTEM_ERROR'; //设备未出库或未在库中找到
            }
            if(count($deviceArr) != count($incrArr)){
                return 'SCREEN_SOFT_DEVICE_NUMBER_NOT_SAME'; //软硬件编号不一致
            }
            //验证要增加的屏幕是否在其他店铺或者自己店铺里
            if($this->checkScreenIsInScreen($incrArr)){
                return 'SCREEN_ALREDY_IN_SHOP';
            }
            $screenModel = new self();
            foreach ($incrArr as $k => $v){
                $currentModel = clone $screenModel;
                $currentModel->number = $deviceArr[$k];
                $currentModel->software_number = $v;
                $currentModel->name = '屏幕1';
                $currentModel->shop_id = $shop_id;
                $currentModel->image = $this->new_images[$k];
                $currentModel->replace_id = $this->replace_id;
                $currentModel->save();
            }
            return 'SUCCESS';
        }catch (Exception $e){
            //var_dump($e->getMessage());exit;
            Yii::error($e->getMessage(),'db');
            return 'ERROR';
        }
    }
    /**
     * 屏幕增删改写队列
     * @param $type
     * @param $store_number
     */
    public static function screenRedisOperate($type, $data, $store_number=1){
        return Redis::getInstance($store_number)->lpush('system_push_data_to_device_list',json_encode(['type'=>$type, 'data'=>$data]));
    }

    /**
     * 提交入综合事业部
     */
    public static function storePost($shop_id,$softDvArr,$replace_id=0){
        //新店安装
        $shopModel=new Shop();
        $shopData=$shopModel->getShopStorage($shop_id);
        if($replace_id){
            $shopData['install_member_name'] = ShopScreenReplace::findOne($replace_id)->getAttribute('install_member_name');
        }
        if(!empty($shopData['screen_start_at']) && !empty($shopData['screen_end_at'])){
            $startUpTime = $shopData['screen_start_at'].":00";
            $shutDownTime = $shopData['screen_end_at'].":00";
        }else{
            $startUpTime="10:00:00";
            $shutDownTime="22:00:00";
        }
        $data = [
            'address'=>$shopData['address'],//详细地址
            'applier'=>$shopData['apply_name'],//申请人姓名
            'applyId'=>$shopData['shop_member_id'],//申请人id
            'area'=>substr($shopData['area'],0,9),//区域代码  9位
            'city'=>substr($shopData['area'],0,7),//市级代码 7位
            'companyName'=>$shopData['company_name'],//公司名称
            'street'=>$shopData['area'],//所属街道
            'scene' => '0',
            'shopId'=>$shop_id,//店铺ID
            'deviceNums'=>$softDvArr,
            'installName'=>$shopData['install_member_name'],//安装人姓名
            'mobile'=>$shopData['apply_mobile'],//申请人手机号
            'province'=>substr($shopData['area'],0,5),//省级代码  5位
            'startUpTime'=>$startUpTime,
            'shutDownTime'=>$shutDownTime,
            'shopName'=>$shopData['name']
        ];
        return self::screenRedisOperate('create',$data);
    }

    public function checkScreenIsInScreen($soft_number){
        if(!$soft_number){ return true; }
        if(is_string($soft_number)){
            return self::find()->where(['software_number'=>$soft_number])->count();
        }
        if(is_array($soft_number)){
            $flag = 0;
            foreach ($soft_number as $v){
                if($this->checkScreenIsInScreen($v)){
                    $flag = 1;
                    break;
                }
            }
            return $flag;
        }
    }
    public function scenes(){
        return [
            //新版换屏提交
            'change-post-new-check' => [
                'id' => [],
                'new_images' => [],
                'install_software_number' => [],
                'remove_device_number' => [],
                'replace_id' => [],
            ],
            //拆屏时提交（带验证）
            'remove-screen-check' => [
                'remove_device_number' => [
                    'required' => '1',
                    'result' => 'REPLACE_ID_CAN_NOT_EMPTY',
                ],
                'shop_id' => [
                    'required' => '1',
                    'result' => 'REPLACE_SHOP_ID_CAN_NOT_EMPTY',
                ],
            ],
        ];
    }
}
