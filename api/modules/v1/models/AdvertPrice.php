<?php

namespace api\modules\v1\models;
use api\modules\v1\models\MemberAreaCount;
use common\libs\Redis;
use common\libs\RedisClass;
use common\libs\ToolsClass;
use api\modules\v1\models\SystemAddress;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * 广告价格配置表
 */
class AdvertPrice extends \api\core\ApiActiveRecord
{
    public $start_at;
    public $end_at;
    public $rate;
    const DEFAULT_LEVEL = 3;
    public static function tableName()
    {
        return '{{%advert_price}}';
    }

    /*
     * 获取可购买的街道ID
     * @param area_id_list string 地区ID
     * @param advert_key string 广告标识
     * @param rate int 频次
     * @param time string 时长
     * @param start_at date 开始时间
     * @param end_at date 结束时间
     * */
    public function getCanBuyStreetId($area_id_list,$advert_key,$rate,$time,$start_at,$end_at){
        $area_id_list = explode(",",$area_id_list);
        $area_type = SystemAddress::reduceAreaType($area_id_list[0]);
        if($area_type == 4) {
            //购买街道或乡镇广告
            $street_id = $this->getStreetAndScreenNumber($area_id_list,$advert_key,$time,$rate,$start_at,$end_at);
        }else{
            //购买省市区广告
            $street_id = $this->getStreetAndScreenNumber(SystemAddress::getAllStreetsByArea($area_id_list),$advert_key,$time,$rate,$start_at,$end_at);
        }
        return array_keys($street_id);
    }

    /*
     * 获取广告单价和屏幕数量
     * @param area_id_list string 地区ID
     * @param advert_key string 广告标识
     * @param rate int 频次
     * @param time string 时长
     * @param start_at date 开始时间
     * @param end_at date 结束时间
     * */
    public function getAdvertPriceAndScreenNumber($area_id_list,$advert_key,$advert_id,$rate,$time,$start_at,$end_at){
        $area_id_list = explode(",",$area_id_list);
        $area_type = SystemAddress::reduceAreaType($area_id_list[0]);
        if($area_type == 4) {
            //购买街道或乡镇广告
            $advertData = $this->getStreetAndScreenNumber($area_id_list,$advert_key,$time,$rate,$start_at,$end_at);
        }else{
            //购买省市区广告
            $advertData = $this->getStreetAndScreenNumber(SystemAddress::getAllStreetsByArea($area_id_list),$advert_key,$time,$rate,$start_at,$end_at);
        }
        if(empty($advertData)){
            return false;
        }
	//\Yii::error(json_encode($advertData),'db');
        //根据地区ID获取广告价格并和屏幕数量一起计算出总价格
        $advertPrice = $this->getAdvertPrice($advert_id,$time,array_keys($advertData));
        $advert_unit_price = 0;
        foreach($advertData as $key=>$value){
            $key = substr($key,0,9);
            $advert_unit_price += $value * $advertPrice[$key];
        }
        return ['unit_price'=>$advert_unit_price,'screen_number'=>array_sum($advertData)];
    }

    /*
     * 获取可购买的地区对应屏幕数量
     * @param area_id_list array 地区ID
     * @param advert_key string 广告标识
     * @param time string 时长
     * @param rate int 频次
     * @param start_at date 开始时间
     * @param end_at date 结束时间
     */
    private function getStreetAndScreenNumber($area_id_list,$advert_key,$time,$rate,$start_at,$end_at){
        if(empty($area_id_list)){
            return false;
        }
        $date_list = ToolsClass::generateDateList($start_at,$end_at,"Ymd");
        $advert_key = strtolower($advert_key);
        $time = ToolsClass::minuteCoverSecond($time);
        #可购买的屏幕总数量
        $buyScreenNumber = 0;
        #可购买街道
        $canStreetArr = [];
        foreach($area_id_list as $area_id){
            $screenKeyList[] = 'system_screen_number:'.$area_id;
            foreach($date_list as $key=>$date){
                $bigmap["advert_cell_status:{$area_id}"][] = ToolsClass::reduceBigMapKey($advert_key,$time,$rate,$date);
            }
        }
        $system_advert_space_rate = Redis::getBitMulti($bigmap);
        $redis = RedisClass::init(3);
        $system_screen_number = $redis->executeCommand('mget',$screenKeyList);
        $listK = 0;
        $area_screen_number = [];
        foreach($area_id_list as $aKey=>$area_id) {
            foreach ($date_list as $key => $date) {
                if (!$system_advert_space_rate[$listK]) {
                    if (empty($system_screen_number[$aKey])) {
                        $listK++;
                        continue;
                    }
                    $arrayScreenNumber = json_decode($system_screen_number[$aKey],true);
                    if(isset($area_screen_number[$area_id])){
                        $area_screen_number[$area_id] += $arrayScreenNumber['screen_number'];
                    }else{
                        $area_screen_number[$area_id] = $arrayScreenNumber['screen_number'];
                    }
                }
                $listK++;
            }
        }
        return $area_screen_number;
    }

    /*
    * 计算广告价格
     * @param advert_id int 广告ID
     * @param time string 广告时长
     * @param area int 地区ID
     * @param rate int 播放频率
     * @param formatPrice int 是否格式化金额(1、格式化 0、不格式化)
    * */
    public function selectAdvertPrice($advert_id=0,$time=null,$area=null,$rate=null,$formatPrice=1,$rate_number=0,$start_at=null,$end_at=null){

        $advert_id = empty($advert_id) ? $this->advert_id : $advert_id;
        $time = empty($time) ? $this->time : $time;
        $area = empty($area) ? OrderAreaCache::getArea() : $area;
        $rate = empty($rate) ? $this->rate : $rate;
        $start_at = empty($start_at) ? $this->start_at : $start_at;
        $end_at = empty($end_at) ? $this->end_at : $end_at;
        if(empty($area)){
            return ['AREA_ID_EMPTY',0,0,0,0];
        }
        $positionModel = AdvertPosition::find()->where(['id'=>$advert_id])->select('rate,name,format,size,spec,key')->asArray()->one();
        if(empty($positionModel)){
            return ['ADVERT_PRICE_CONFIG_ERROR',0,0,0,0];
        }
        if(empty($rate_number)){
            //计算频次
            $rate = $rate / explode(",",$positionModel['rate'])[0];
            if(!is_int($rate)){
                return ['ADVERT_RATE_ERROR',0,0,0,0];
            }
        }else{
            $rate = $rate_number;
        }
        //获取广告单价和屏幕数量
        $advertNumber = $this->getAdvertPriceAndScreenNumber($area,$positionModel['key'],$advert_id,$rate,$time,$start_at,$end_at);
        if(empty($advertNumber)){
            return ['ORDER_ADVERT_NO_RATE',0,0,0,0];
        }
        //订单总价
        $resultPrice = $advertNumber['unit_price'] * $rate;
        if($resultPrice <= 0){
            return ['ADVERT_PRICE_ERROR',0,0,0,0];
        }
        if($formatPrice){
            unset($positionModel['rate']);
            return ['SUCCESS',ToolsClass::priceConvert($resultPrice),$positionModel,$rate,$advertNumber['screen_number']];
        }else{
            return ['SUCCESS',$resultPrice,$advertNumber['unit_price'],$advertNumber['screen_number']];
        }
    }

    /*
     * 获取广告价格
     * @param advert_id int 广告ID
     * @param time string 广告时长
     * @param area_id array 地区ID
     * */
    public static function getAdvertPrice($advert_id,$time,$area_id){
        $priceModel = AdvertPrice::find()->where(['advert_id'=>$advert_id,'time'=>$time])->select('price_1,price_2,price_3')->asArray()->one();
        if(empty($priceModel)){
            return false;
        }
        $area_id_length = strlen($area_id[0]);
        $result = [];
        if($area_id_length <= 7){
            //计算市级广告价格
            $addressLevel = Redis::getInstance(3)->hmget("system_config_by_advert_price",$area_id);
            foreach($area_id as $key=>$val){
                $p = isset($addressLevel[$val]) ? $addressLevel[$val] : self::DEFAULT_LEVEL;
                $result[$val] = $priceModel['price_'.$p];
            }
        }else{
            //计算区县或乡镇级别广告的价格
            if($area_id_length == 12){
                //乡镇广告截取地区ID长度,按照区县计算价格
                foreach($area_id as $key=>$val){
                    $area_id[$key] = substr($val,0,9);
                }
                $area_id = array_flip(array_flip($area_id));
            }
            $addressLevel = SystemAddressLevel::find()->where(['area_id'=>$area_id,'type'=>1])->select('area_id,level')->asArray()->all();
            if(empty($addressLevel)){
                foreach($area_id as $key=>$val){
                    $result[$val] = $priceModel['price_'.self::DEFAULT_LEVEL];
                }
            }else{
                $addressLevel = ArrayHelper::map($addressLevel,'area_id','level');
                foreach($area_id as $key=>$val){
                    $p = isset($addressLevel[$val]) ? $addressLevel[$val] : self::DEFAULT_LEVEL;
                    $result[$val] = isset($priceModel['price_'.$p]) ? $priceModel['price_'.$p] : $priceModel['price_'.self::DEFAULT_LEVEL];
                }
            }
        }
        return $result;
    }

    public static function getAdvertPositionInfo($advert_id){
        $positionModel = AdvertPosition::find()->where(['id'=>$advert_id])->select('name,key')->asArray()->one();
        if(empty($positionModel)){
            return false;
        }
        return [
          'advert_name'=>$positionModel['name'],
          'advert_key'=>$positionModel['key']
        ];
    }

    /*
     * 场景
     * */
    public function scenes(){
        return [
            'select'=>[
                'start_at'=>[
                    'required'=>'1',
                    'result'=>'START_AT_EMPTY'
                ],
                'end_at'=>[
                    'required'=>'1',
                    'result'=>'END_AT_EMPTY'
                ],
                'advert_id'=>[
                    'required'=>'1',
                    'type'=>'int',
                    'result'=>'ADVERT_ID_EMPTY'
                ],
                'time'=>[
                    'required'=>'1',
                    'type'=>'string',
                    'result'=>'ADVERT_TIME_EMPTY'
                ],
                'rate'=>[
                    'required'=>'1',
                    'type'=>'int',
                    'result'=>'ADVERT_RATE_EMPTY'
                ],
            ],
        ];
    }
}
