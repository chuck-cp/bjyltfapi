<?php

namespace api\modules\v1\models;

use common\libs\ArrayClass;
use common\libs\DataClass;
use common\libs\Redis;
use common\libs\RedisClass;
use common\libs\ToolsClass;
use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

/**
 * 用户的地区缓存
 */
class OrderAreaCache extends \api\core\ApiActiveRecord
{

    public $except_area_id;
    public $keyword;
    public $advert_id;
    public $advert_key;
    public $advert_time;
    public $rate;
    public $start_at;
    public $end_at;
    public $sort;
    public $page;
    public $date_page;
    public $type;
    public $request_types;
    public $order_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_area_cache}}';
    }

    public static function getAreaByToken(){
        return OrderAreaCache::find()->where(['token'=>ToolsClass::getParams('token')])->select('parent_area_id,area_id,cell_out_area_id,area_type')->asArray()->one();
    }

    public static function getArea(){
        $areaModel = OrderAreaCache::find()->where(['token'=>ToolsClass::getParams('token')])->select('area_id')->asArray()->one();
        if($areaModel){
            return $areaModel['area_id'];
        }
    }

    /*
     * 计算购买的时长是第几个
     * */
    public function reduceRateNumber($advert_key,$time){
        $time = ToolsClass::minuteCoverSecond($time);
        $defaultTimeList = ToolsClass::getCommonStatus('defaultTimeList',strtolower($advert_key));
        foreach($defaultTimeList as $key=>$sTime){
            if($sTime == $time){
                return $key;
            }
        }
        if(empty($rateNumber)){
            throw new Exception("广告位时长错误:广告位:{$advert_key},时长:{$time}");
        }
    }

    /*
     * 获取地图数据
     * */
    public function getMapData()
    {
        $advert = AdvertPrice::getAdvertPositionInfo($this->advert_id);
        if(empty($advert)){
            return false;
        }
        $advert_key = strtolower($advert['advert_key']);
        $selected_area_id = ToolsClass::explode(",",$this->area_id);
        $streetModel = SystemAddress::find()->where(['level' => 6,'is_buy'=>1])->select('id,name')->asArray()->all();
        if (empty($streetModel)) {
            return false;
        }
        $streetModel = array_column($streetModel,'id');
        $date_list = ToolsClass::generateDateList($this->start_at,$this->end_at,'Ymd');
        $this->advert_time = ToolsClass::minuteCoverSecond($this->advert_time);
        foreach ($date_list as $date) {
            # 查询该地区是否还有余量
            if (empty($streetModel)) {
                break;
            }
            $bigmap = [];
            foreach ($streetModel as $street_id) {
                $bigmap["advert_cell_status:{$street_id}"][] = ToolsClass::reduceBigMapKey($this->advert_key,$this->advert_time,$this->rate,$date);
            }
            $system_advert_space_rate = Redis::getBitMulti($bigmap);
            foreach ($system_advert_space_rate as $key => $value) {
                if (!$value) {
                    $spaceStreetId[] = $streetModel[$key];
                    unset($streetModel[$key]);
                }
            }
        }
        if (empty($spaceStreetId)) {
            return false;
        }
        # 查询店铺的坐标
        $shopModel = Shop::find()->where(['and',['status' => 5],['area' => $spaceStreetId],['>','screen_number',0]])->select('area,longitude,latitude')->asArray()->all();
        if (empty($shopModel)) {
            return false;
        }
        $result = [
            'selectedShop' => [],
            'shop' => []
        ];
        # 把店铺数据分为已加入购入车和未加入购物车
        if ($selected_area_id) {
            $selected_area_id_length = strlen($selected_area_id[0]);
            foreach ($shopModel as $shop) {
                if (empty($shop['longitude']) || empty($shop['latitude'])) {
                    continue;
                }
                if (in_array(substr($shop['area'],0,$selected_area_id_length),$selected_area_id)) {
                    $result['selectedShop'][] = [
                        'longitude' => $shop['longitude'],
                        'latitude' => $shop['latitude']
                    ];
                } else {
                    $result['shop'][] = [
                        'longitude' => $shop['longitude'],
                        'latitude' => $shop['latitude']
                    ];
                }
            }
        } else {
            foreach ($shopModel as $shop) {
                if (empty($shop['longitude']) || empty($shop['latitude'])) {
                    continue;
                }
                $result['shop'][] = [
                    'longitude' => $shop['longitude'],
                    'latitude' => $shop['latitude']
                ];
            }
        }

        return $result;
    }


    /*
     * 获取直接观看人数
     * */
    public function getWatchNumber($rate,$screen_number) {
        if ($rate == 1) {
            $watchNumber = $screen_number * 10;
        } else {
            $watchNumber = $screen_number * 20;
        }
        return $watchNumber;
    }

    /*
     * 获取不重复观看人数
     * */
    public function getNoRepeatWatchNumber($rate,$date,$order_start_at,$screen_number) {
        # 计算直接观看人数
        if ($rate == 1) {
            $watchNumber = $screen_number * 10;
        } else {
            $watchNumber = $screen_number * 20;
        }
        # 计算不重复广告人
        $diffDate = ((strtotime($date) - $order_start_at) / 86400)+1;
        if ($diffDate <= 30) {
            $watchNumber = ceil($watchNumber * 0.9);
        } elseif ($diffDate <= 60) {
            $watchNumber = ceil($watchNumber * 0.3);
        } else {
            $watchNumber = ceil($watchNumber * 0.2);
        }
        return $watchNumber;
    }

    /*
     * 订单确认地区
     * */
    public function getConfirmArea(){
        $advert = AdvertPrice::getAdvertPositionInfo($this->advert_id);
        if(empty($advert)){
            return false;
        }
        $advert_key = strtolower($advert['advert_key']);
        $page = (int)$this->page;
        if($page <= 0){
            $start = 0;
            $end = 20;
        }else{
            $end = 20;
            $start = ($page-1) * $end;
        }
        //购买的时长
        $time = ToolsClass::minuteCoverSecond($this->advert_time);
        //播放频次
        $rate = AdvertPosition::reduceRate($this->advert_id,$this->rate);
        if(empty($rate)){
            return false;
        }
        // 订单开始投放时间(时间戳)
        $start_at = strtotime($this->start_at);
        $date_list = ToolsClass::generateDateList($this->start_at,$this->end_at,'Ymd');
        if($this->area_id){
            $area_id_list =  ToolsClass::explode(",",$this->area_id);
            $area_type = SystemAddress::reduceAreaType($area_id_list[0]);
            OrderAreaCache::updateAll(['parent_area_id'=>$this->area_id,'area_type'=>$area_type],['token'=>ToolsClass::getParams('token')]);
        }else{
            $cacheModel = OrderAreaCache::find()->where(['token'=>ToolsClass::getParams('token')])->select('parent_area_id,area_type')->asArray()->one();
            if(empty($cacheModel)){
                return false;
            }
            $area_type = $cacheModel['area_type'];
            $area_id_list = ToolsClass::explode(",",$cacheModel['parent_area_id']);
        }
        $except_area_id = ToolsClass::explode(",",$this->except_area_id);
        $resultAreaDate = [];
        $page_array = [];
        $result_parent_area = [];
        foreach($area_id_list as $area_id){
            if(in_array($area_id,$except_area_id)){
                continue;
            }
            $parent_area_name = SystemAddress::getAreaNameById($area_id,'ONE');
            $buy_parent_area_id = substr($area_id,0,strlen($area_id)-2);
            if(strlen($buy_parent_area_id) >= 5){
                $grand_parent_area_name = SystemAddress::getAreaNameById($buy_parent_area_id,'ONE');
                $buy_parent_area_name = $grand_parent_area_name.'-'.$parent_area_name;
                $result_parent_area[$buy_parent_area_id] = [
                    'area_id'=>$buy_parent_area_id,
                    'area_name'=>$grand_parent_area_name
                ];
            }else{
                $buy_parent_area_name = $parent_area_name;
            }
            $child_area = SystemAddress::find()->where(['parent_id'=>$area_id,'is_buy'=>1])->select('id,name')->asArray()->all();
            if(empty($child_area)){
                continue;
            }

            foreach($child_area as $cArea){
                if(in_array($cArea['id'],$except_area_id)){
                    continue;
                }
                $select_area_name = $parent_area_name.$cArea['name'];
                if($this->keyword && !strstr($select_area_name,$this->keyword)){
                    continue;
                }
                $page_array[] = [
                    'buy_parent_area_name'=>$buy_parent_area_name,
                    'buy_area_name'=>$cArea['name'],
                    'area_name'=>$select_area_name,
                    'area_id'=>$cArea['id']
                ];
            }
        }
        //分页
        $page_array = array_slice($page_array,$start,$end);
        if(empty($page_array)){
            if(empty($this->except_area_id) && empty($this->keyword) && $this->page == 1){
                sort($result_parent_area);
                return ['ADVERT_AREA_SCREEN_ET_ZERO',$result_parent_area];
            }else{
                return ['SUCCESS',[]];
            }
        }
        // 不重复观看人数
        $noRepeatWatchNumber = [];
        $maxShopNumber = [];
        $totalScreenNumber = [];
        $maxScreenNumber = [];
        //获取广告的价格
        $area_type = SystemAddress::reduceAreaType($page_array[0]['area_id']);
        $advertPrice = AdvertPrice::getAdvertPrice($this->advert_id,$this->advert_time,array_column($page_array,'area_id'));
        if($area_type == 4){
            foreach($page_array as $area){
                $screenKeyList[] = 'system_screen_number:'.$area['area_id'];
                foreach($date_list as $key=>$date){
                    $bigmap["advert_cell_status:{$area['area_id']}"][] = ToolsClass::reduceBigMapKey($advert_key,$time,$rate,$date);
                }
            }
            $system_advert_space_rate = Redis::getBitMulti($bigmap);
            $redis = RedisClass::init(3);
            $system_screen_number = $redis->executeCommand('mget',$screenKeyList);
            $listK = 0;
            foreach($page_array as $aKey => $area){
                foreach($date_list as $key=>$date) {
                    if (!empty($system_screen_number[$aKey]) && empty($system_advert_space_rate[$listK])) {
                        $screenNumber = json_decode($system_screen_number[$aKey], true);
                    } else {
                        $screenNumber = [
                            'screen_number' => 0,
                            'shop_number' => 0,
                            'mirror_number' => 0
                        ];
                    }
                    if (!isset($totalScreenNumber[$area['area_id']])) {
                        $totalScreenNumber[$area['area_id']] = $screenNumber;
                        $maxScreenNumber[$area['area_id']] = $screenNumber;
                    } else {
                        if ($maxScreenNumber[$area['area_id']]['screen_number'] < $screenNumber['screen_number']) {
                            $maxScreenNumber[$area['area_id']] = $screenNumber;
                        }
                        $totalScreenNumber[$area['area_id']] = [
                            'shop_number' => $totalScreenNumber[$area['area_id']]['shop_number'] + $screenNumber['shop_number'],
                            'mirror_number' => $totalScreenNumber[$area['area_id']]['mirror_number'] + $screenNumber['mirror_number'],
                            'screen_number' => $totalScreenNumber[$area['area_id']]['screen_number'] + $screenNumber['screen_number']
                        ];
                    }
                    $listK++;
                    if (!isset($noRepeatWatchNumber[$area['area_id']])) {
                        $noRepeatWatchNumber[$area['area_id']] = 0;
                    }
                    $noRepeatWatchNumber[$area['area_id']] += $this->getNoRepeatWatchNumber($rate,$date,$start_at,$screenNumber['screen_number']);
                }
            }
        }else{
            foreach($page_array as $area){
                $screenKeyList[] = 'system_screen_number:'.$area['area_id'];
                foreach($date_list as $key=>$date){
                    $mget[] = "system_advert_space_rate_{$advert_key}:{$date}:{$area['area_id']}:{$time}";
                }
            }
            $redis = RedisClass::init(4);
            $system_space_rate = $redis->executeCommand('mget',$mget);
            $redis = RedisClass::init(3);
            $system_screen_number = $redis->executeCommand('mget',$screenKeyList);
            $parent_area_cant_buy_date = [];
            $listK = 0;
            foreach($page_array as $aKey=>$area) {
                $jsonNumber = json_decode($system_screen_number[$aKey],true);
                foreach ($date_list as $key => $date) {
                    if (!empty($system_space_rate[$listK])) {
                        $sRate = json_decode($system_space_rate[$listK],true);
                        if (!isset($sRate[$rate - 1])) {
                            $listK++;
                            continue;
                        }
                        # 计算所选时间段内剩余店铺数据最多的数量
                        $reduceScreenNumber = $jsonNumber['screen_number'] - $sRate[$rate - 1]['screen_number'];
                        $reduceShopNumber = $jsonNumber['shop_number'] - $sRate[$rate - 1]['shop_number'];
                        $reduceMirrorNumber = $jsonNumber['mirror_number'] - $sRate[$rate - 1]['mirror_number'];

                        #已卖完的街道数量大于等于该地区下的街道数量时,提示该地区当天没有余量
                        $parent_area_cant_buy_date[$area['area_id'].'_'.$date] = $reduceScreenNumber;
                    } else {
                        $reduceScreenNumber = $jsonNumber['screen_number'];
                        $reduceShopNumber = $jsonNumber['shop_number'];
                        $reduceMirrorNumber = $jsonNumber['mirror_number'];
                        unset($maxShopNumber[$area['area_id']]);
                    }
                    if (!isset($totalScreenNumber[$area['area_id']])) {
                        $totalScreenNumber[$area['area_id']] = [
                            'shop_number' => $reduceShopNumber,
                            'screen_number' => $reduceScreenNumber,
                            'mirror_number' => $reduceMirrorNumber
                        ];
                        $maxScreenNumber[$area['area_id']] = [
                            'shop_number' => $reduceShopNumber,
                            'screen_number' => $reduceScreenNumber,
                            'mirror_number' => $reduceMirrorNumber
                        ];
                    } else {
                        if ($maxScreenNumber[$area['area_id']]['screen_number'] < $reduceScreenNumber) {
                            $maxScreenNumber[$area['area_id']] = [
                                'shop_number' => $reduceShopNumber,
                                'screen_number' => $reduceScreenNumber,
                                'mirror_number' => $reduceMirrorNumber
                            ];
                        }
                        $totalScreenNumber[$area['area_id']] = [
                            'shop_number' => $totalScreenNumber[$area['area_id']]['shop_number'] + $reduceShopNumber,
                            'mirror_number' => $totalScreenNumber[$area['area_id']]['mirror_number'] + $reduceMirrorNumber,
                            'screen_number' => $totalScreenNumber[$area['area_id']]['screen_number'] + $reduceScreenNumber
                        ];
                    }
                    $listK++;
                    if (!isset($noRepeatWatchNumber[$area['area_id']])) {
                        $noRepeatWatchNumber[$area['area_id']] = 0;
                    }
                    $noRepeatWatchNumber[$area['area_id']] += $this->getNoRepeatWatchNumber($rate,$date,$start_at,$reduceScreenNumber);
                }
                # 没有计算出所选时间段内的最大值时,取当前街道的最大数量
                if (!isset($totalScreenNumber[$area['area_id']])) {
                    if (empty($jsonNumber)) {
                        $jsonNumber = [
                            'screen_number' => 0,
                            'shop_number' => 0,
                            'mirror_number' => 0
                        ];
                    }
                    $maxShopNumber[$area['area_id']] = $jsonNumber;
                }
            }
        }
        $listK = 0;
        // $rateNumber = $this->reduceRateNumber($advert_key,$time);
        foreach($page_array as $key=>$array){
            $cant_buy_date = [];
            $spaceNumber = 0;
            if($area_type == 4){
                #街道验证剩余频次
                foreach($date_list as $dKey=>$date){
                     if($system_advert_space_rate[$listK]){
                        $cant_buy_date[] = date('m-d',strtotime($date));
                    }
                    $listK++;
                }
            }else{
                #省市区验证剩余频次
                foreach($date_list as $dKey=>$date){
                    if(isset($parent_area_cant_buy_date[$array['area_id'].'_'.$date]) && $parent_area_cant_buy_date[$array['area_id'].'_'.$date] <= 0){
                        $cant_buy_date[] = date('m-d',strtotime($date));
                    }
                }
            }
            $cant_buy_date = array_flip(array_flip($cant_buy_date));
            $date_total = count($date_list);
            $cant_buy_date_total = count($cant_buy_date);
            if($cant_buy_date_total == $date_total){
                $cant_buy_date = '所选地区余量不足';
            }elseif(!empty($cant_buy_date)){
                if(count($cant_buy_date) > 2){
                    $cant_buy_date = implode(",",array_slice($cant_buy_date,0,2)).'等'.count($cant_buy_date).'天';
                }else{
                    $cant_buy_date = implode(",",$cant_buy_date);
                }
                $cant_buy_date .= '余量不足';
            }
            //获取该地区的广告价格
            $sub_area_id = substr($array['area_id'],0,9);
            if(!isset($advertPrice[$sub_area_id])){
                return false;
            }
            $cant_buy_date_length = 0;
            if (is_array($cant_buy_date)) {
                $cant_buy_date_length = count($cant_buy_date);
            }
            if (isset($totalScreenNumber[$array['area_id']])) {
                $total_price = $totalScreenNumber[$array['area_id']]['screen_number'] * $advertPrice[$sub_area_id] * $rate;
            } else {
                $total_price = 0;
            }
            if(empty($totalScreenNumber[$array['area_id']]['screen_number'])) {
                continue;
            }
            $resultAreaDate[] = [
                'image_url'=>'http://i1.bjyltf.com/system/default_area.jpg',
                'area_id'=>$array['area_id'],
                'area_name'=>$array['area_name'],
                'buy_area_name'=>$array['buy_area_name'],
                'buy_parent_area_name'=>$array['buy_parent_area_name'],
                'cant_buy_date'=>empty($cant_buy_date) ? '' : $cant_buy_date,
                'advert_name'=>$advert['advert_name'],
                'price'=>ToolsClass::priceConvert($advertPrice[$sub_area_id]).'/'.str_replace(['s','m'],['秒','分钟'],$this->advert_time),
                'total_price' =>(string)sprintf("%.2f",ToolsClass::priceConvert($total_price)),
                'advert_time'=>$this->advert_time,
                'shop_number' => (string)$maxScreenNumber[$array['area_id']]['shop_number'],
                'mirror_number' => (string)$maxScreenNumber[$array['area_id']]['mirror_number'],
                'screen_number' => (string)$maxScreenNumber[$array['area_id']]['screen_number'],
                'total_screen_number' => (string)$totalScreenNumber[$array['area_id']]['screen_number'],
//                'max_covered_number' => (string)($totalScreenNumber[$array['area_id']]['screen_number'] * 20 * $date_total * $rate),
                'covered_number' => (string)($noRepeatWatchNumber[$array['area_id']] * 3),
                'watch_number' => (string)$this->getWatchNumber($rate,$totalScreenNumber[$array['area_id']]['screen_number']),
                'no_repeat_watch_number' => (string)$noRepeatWatchNumber[$array['area_id']]
            ];
        }
        return ['SUCCESS',ArrayClass::sort($resultAreaDate,'screen_number',$this->sort)];
    }

    /*
     * 确认订单地区详情
     * */
    public function getConfirmAreaView(){
        $advert_key = AdvertPosition::getAdvertKey($this->advert_id);
        $date_list = ToolsClass::generateDateList($this->start_at,$this->end_at,'Ymd');
        $area_id = ToolsClass::explode(",",$this->area_id);
        $result = [];
        $rate = AdvertPosition::reduceRate($this->advert_id,$this->rate);
        $time = ToolsClass::minuteCoverSecond($this->advert_time);
        if(empty($this->page)){
            $start = 0;
            $end = 5;
        }else{
            $end = 5;
            $start = ($this->page - 1) * $end;
        }
        /***********如果是修改查询其修改之前的开始结束日期start*************/
        if($this->request_types == 'modify' && $this->order_id){
            $order_date = OrderDate::find()->where(['order_id'=>$this->order_id])->select('start_at, end_at')->asArray()->one();
        }
        if(!empty($order_date)){
            $old_date_list = ToolsClass::generateDateList($order_date['start_at'],$order_date['end_at'],'m-d');
        }
        /***********如果是修改查询其修改之前的开始结束日期 end*************/
        $date_list = array_slice($date_list,$start,$end);
        if(empty($date_list)){
            return false;
        }
        $area_type = SystemAddress::reduceAreaType($area_id[0]);
        if($area_type == 4) {
            $redis = RedisClass::init(4);
            foreach ($date_list as $date) {
                foreach ($area_id as $area) {
                    $bigmap["advert_cell_status:{$area}"][] = ToolsClass::reduceBigMapKey($advert_key,$time,$rate,$date);
                }
            }
            $system_advert_space_rate = Redis::getBitMulti($bigmap);

        }else{
            $parent_area_cant_buy_date = [];
            if($this->request_types == 'modify' && $this->order_id){
                $orderThrowData = OrderThrowOrderDate::getOrderData($this->order_id);
                #修改订单
                $areaModel = OrderArea::find()->where(['order_id'=>$this->order_id])->select('street_screen_number,street_area')->asArray()->one();
                $areaLength = strlen($area_id[0]);
                $streetArea = explode(",",$areaModel['street_area']);
                $streetScreenNumber = explode(",",$areaModel['street_screen_number']);
                $totalScreenNumber = [];
                $bitmap = [];
                foreach($streetArea as $key=>$areaId){
                    $sArea = substr($areaId,0,$areaLength);
                    if(!isset($totalScreenNumber[$sArea])){
                        $totalScreenNumber[$sArea] = 0;
                    }
                    $totalScreenNumber[$sArea] += $streetScreenNumber[$key];
                    $reformStreetArea[$sArea][$areaId] = $streetScreenNumber[$key];
                }

                foreach($area_id as $area){
                    if(!isset($reformStreetArea[$area])){
                        throw new NotFoundHttpException("没有找到订单所对应的街道ID");
                    }
                    foreach($date_list as $key=>$date){
                        foreach($reformStreetArea[$area] as $key=>$value){
                            if (in_array($date."_".$key,$orderThrowData)) {
                                continue;
                            }
                            $bitmap["advert_cell_status:{$key}"][] = ToolsClass::reduceBigMapKey($advert_key,$time,$rate,$date);
                        }
                    }
                }
                $listK = 0;
                $bitmap = Redis::getBitMulti($bitmap);

                foreach($area_id as $area){
                    foreach($reformStreetArea[$area] as $areaKey=>$value){
                        #存储每天街道每天原本有多少屏幕
                        foreach($date_list as $key=>$date){
                            if (!isset($spaceScreenNumber[$area][$date])){
                                $spaceScreenNumber[$area][$date] = $totalScreenNumber[$area];
                            }
                            if (in_array($date."_".$areaKey,$orderThrowData)) {
                                continue;
                            }
                            if($bitmap[$listK]){
                                #如果卖完了,减去对对应的屏幕
                                $spaceScreenNumber[$area][$date] -= $value;
                            }
                            $listK++;
                        }
                    }
                }
                foreach($spaceScreenNumber as $key=>$dateValue){
                    foreach($dateValue as $date=>$screenNumber){
                        if($screenNumber == 0){
                            $parent_area_cant_buy_date[$key.'_'.$date] = "无余量";
                        }elseif($screenNumber == $totalScreenNumber[$key]){
                            $parent_area_cant_buy_date[$key.'_'.$date] = "充足";
                        }else{
                            $parent_area_cant_buy_date[$key.'_'.$date] = round(($totalScreenNumber[$key] - $screenNumber) / $totalScreenNumber[$key] * 100,2).'%';
                        }
                    }
                }
            }else{
                #创建订单
                foreach($area_id as $area){
                    $screenKeyList[] = 'system_screen_number:'.$area;
                    foreach($date_list as $key=>$date){
                        $mget[] = "system_advert_space_rate_{$advert_key}:{$date}:{$area}:{$time}";
                    }
                }

                $redis = RedisClass::init(4);
                $system_space_rate = $redis->executeCommand('mget',$mget);
                $redis = RedisClass::init(3);
                $system_screen_number = $redis->executeCommand('mget',$screenKeyList);
                $listK = 0;
                foreach($area_id as $aKey=>$area) {
                    foreach ($date_list as $key => $date) {
                        if (empty($system_space_rate[$listK])) {
                            $listK++;
                            continue;
                        }
                        $sRate = json_decode($system_space_rate[$listK],true);
                        if (!isset($sRate[$rate - 1])) {
                            $listK++;
                            continue;
                        }
                        #已卖完的街道数量大于等于该地区下的街道数量时,提示该地区当天没有余量
                        $screenNumber = json_decode($system_screen_number[$aKey],true)['screen_number'];
                        if ($sRate[$rate - 1]['screen_number'] >= $screenNumber) {
                            $parent_area_cant_buy_date[$area.'_'.$date] = 1;
                        } else {
                            if($sRate[$rate - 1]['screen_number'] == 0){
                                $parent_area_cant_buy_date[$area.'_'.$date] = "充足";
                            }else{
                                $parent_area_cant_buy_date[$area.'_'.$date] =  '剩余'.round(($screenNumber - $sRate[$rate - 1]['screen_number']) / $screenNumber * 100,2).'%';
                            }
                            $listK++;
                            continue;
                        }
                        $listK++;
                    }
                }
            }
        }

        $listK = 0;
        // $rateNumber = $this->reduceRateNumber($advert_key,$time);
        foreach ($area_id as $key => $area) {
            $result_date = [];
            if($area_type == 4) {
                foreach($date_list as $date){
                    if ($system_advert_space_rate[$listK]) {
                        $result_date_list[date('m-d',strtotime($date))]['space_time_list'][] = '无余量';
                    } else {
                        $result_date_list[date('m-d',strtotime($date))]['space_time_list'][] = "充足";
                    }
                    $listK++;
                }
            }else{
                #修改订单
                foreach($date_list as $date){
                    if(!isset($parent_area_cant_buy_date[$area.'_'.$date])){
                        $result_date_list[date('m-d',strtotime($date))]['space_time_list'][] = '充足';
                    }elseif($parent_area_cant_buy_date[$area.'_'.$date] == 1){
                        $result_date_list[date('m-d',strtotime($date))]['space_time_list'][] = '无余量';
                    }else{
                        $result_date_list[date('m-d',strtotime($date))]['space_time_list'][] = $parent_area_cant_buy_date[$area.'_'.$date];
                    }
                }
            }
        }
        if (!empty($result_date_list)) {
            $reformData = [];
            foreach ($result_date_list as $key => $value) {
                $reformData[] = [
                    'date' => $key,
                    'space_time_list' => $value['space_time_list']
                ];
            }
            $result_date_list = $reformData;
        }
        foreach ($area_id as $area){
            $result_area[] = [
                'area_id' => $area,
                'area_name' => SystemAddress::getAreaNameById($area,'ONE'),
                //当前地区的上级
                'prev_name' => SystemAddress::getPrevAreaByIdLen($area),
            ];
        }
        if(!empty($old_date_list)){
            foreach ($result_date_list as $k => $v){
                foreach ($v['space_time_list'] as $kk => $vv){
                    if(in_array($v['date'], $old_date_list)){
                        $result_date_list[$k]['space_time_list'][$kk] = '充足';
                    }
                }
            }
        }
        $result['area'] = $result_area;
        $result['item'] = $result_date_list;
        return $result;
    }

    /*
     * 获取地区并转换成街道
     * */
    public static function getStreet(){
        $our_area_id = OrderAreaCache::getArea();
        $cache_area_id = ToolsClass::explode(",",$our_area_id);
        if(strlen($cache_area_id[0]) == 11){
            //如果提交的街道数据,不需要转换直接返回
            return $our_area_id;
        }
        $order_area_id = [];
        $cache_area_id = array_flip(array_flip($cache_area_id));
        foreach($cache_area_id as $area_id){
            $where = [
                'level'=>6,
                'left(id,5)'=>$area_id
            ];
            $order_area_id = array_merge($order_area_id,SystemAddress::find()->where($where)->select('id')->asArray()->all());
        }
        if(empty($order_area_id)){
            return false;
        }
        $order_area_id = array_column($order_area_id,"id");
        $order_area_id = array_flip(array_flip($order_area_id));
        return implode(",",$order_area_id);
    }

    /*
     * 获取第一个投放地区
     * */
    public static function getAreaAndDefaultAreaName(){
        $areaModel = OrderAreaCache::find()->where(['token'=>ToolsClass::getParams('token')])->select('area_id')->asArray()->one();
        if(empty($areaModel)){
            return false;
        }
        if(strstr($areaModel['area_id'],",")){
            $area_id = explode(',',$areaModel['area_id'])[0];
        }else{
            $area_id = $areaModel['area_id'];
        }
        return SystemAddress::getAreaNameById($area_id,'ALL');
    }

    /*
     * 创建地区缓存
     * */
    public function createAreaCache(){
        try{
            $defaultAreaName = '';
            $cell_out_area_id = '';
            $area_id = ToolsClass::explode(",",$this->area_id);
            $areaType = SystemAddress::reduceAreaType($area_id[0]);
            if ($areaType == 2) {
                $defaultAreaName = SystemAddress::getName($area_id[0]);
            } else {
                $defaultAreaName = SystemAddress::getAreaAndParentName($area_id[0]);
            }
            if(count($area_id) > 1){
                $defaultAreaName .= '...';
            }
            if(self::find()->where(['token'=>$this->token])->count()){
                if($this->type == 1){
                    #确认地区页面选择地区
                    $updateKey = 'area_id';
                    $cell_out_area_id = $areaType == 4 ? '' : $this->getCellOutAreaId($area_id);
                }else{
                    #广告购买页选择的地区
                    $updateKey = 'parent_area_id';
                }
                self::updateAll([$updateKey=>$this->area_id,'cell_out_area_id'=>$cell_out_area_id,'area_type'=>SystemAddress::reduceAreaType($area_id[0])],['token'=>$this->token]);
            }else{
                #确认地区页选择的地区
                $this->parent_area_id = $this->area_id;
                $this->area_id = '';
                $this->area_type = $areaType;
                $this->save();
            }
            return ['area_name'=>$defaultAreaName];
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return false;
        }
    }
    public function getCellOutAreaId($area_id){
        list($rate,$advert_key) = AdvertPosition::reduceRate($this->advert_id,$this->rate,1);
        if(empty($advert_key) || empty($rate)){
            Yii::error("没有找到广告位信息,advert_id:{$this->advert_id},rate:{$this->rate}",'db');
            return false;
        }
        $rate--;
        $advert_key = strtolower($advert_key);
        $advert_time = ToolsClass::minuteCoverSecond($this->advert_time);
        $length_area_id = strlen($area_id[0]);
        $cellOutAreaId = AdvertCellOutArea::find()->select('date,area_id')->where(['and',['advert_key'=>$advert_key],['>=','date',$this->start_at],['<=','date',$this->end_at],['rate'=>$rate],['advert_time'=>$advert_time],['left(area_id,'.$length_area_id.')'=>$area_id]])->asArray()->all();
        if(empty($cellOutAreaId)){
            return false;
        }
        $reformAreaId = [];
        foreach($cellOutAreaId as $area){
            $reformAreaId[$area['date']][] = $area['area_id'];
        }
        if(count($reformAreaId) == 1){
            return implode(",",current($reformAreaId));
        }
        $first_area = current($reformAreaId);
        array_shift($reformAreaId);
        foreach($reformAreaId as $key=>$area){
            $first_area = array_intersect($area,$first_area);
        }
        if(empty($first_area)){
            return false;
        }
        return implode(",",$first_area);
    }

    /*
     * 场景
     * */
    public function scenes()
    {
        return [
            'create' => [
                'token' => [
                    'required' => '1',
                    'result' => 'TOKEN_EMPTY'
                ],
                'area_id' => [
                    'required' => '1',
                    'result' => 'AREA_EMPTY'
                ],
                'advert_id'=>[
                    'type' => 'int',
                ],
                'advert_time'=>[],
                'rate'=>[],
                'start_at'=>[],
                'end_at'=>[],
                'type'=>[
                    'type'=>'int'
                ]
            ],
            'confirm-area'=>[
                'area_id'=>[
                    'type'=>'string',
                ],
                'keyword'=>[
                    'type' => 'string',
                ],
                'sort'=>[
                    'type' => 'string',
                    'default'=>'desc',
                ],
                'except_area_id'=>[
                    'type' => 'string',
                ],
                'advert_id'=>[
                    'required'=>'1',
                    'type' => 'int',
                    'result'=>'ADVERT_ID_EMPTY'
                ],
                'advert_time'=>[
                    'required'=>'1',
                    'type' => 'string',
                    'result'=>'ADVERT_TIME_EMPTY'

                ],
                'rate'=>[
                    'required'=>'1',
                    'type' => 'int',
                    'result'=>'ADVERT_RATE_EMPTY'
                ],
                'start_at'=>[
                    'required'=>'1',
                    'type' => 'string',
                    'result'=>'ADVERT_START_AT_EMPTY'
                ],
                'end_at'=>[
                    'required'=>'1',
                    'type' => 'string',
                    'result'=>'ADVERT_END_AT_EMPTY'
                ],
                'page'=>[
                    'type'=>'int',
                ]
            ],
            'map'=>[
                'advert_id' => [
                    'required'=>'1',
                    'result' => 'ADVERT_ID_EMPTY'
                ],
                'area_id'=>[
                    'type' => 'string',
                ],
                'advert_time'=>[
                    'required'=>'1',
                    'type' => 'string',
                    'result'=>'ADVERT_TIME_EMPTY'

                ],
                'rate'=>[
                    'required'=>'1',
                    'type' => 'int',
                    'result'=>'ADVERT_RATE_EMPTY'
                ],
                'start_at'=>[
                    'required'=>'1',
                    'type' => 'string',
                    'result'=>'ADVERT_START_AT_EMPTY'
                ],
                'end_at'=>[
                    'required'=>'1',
                    'type' => 'string',
                    'result'=>'ADVERT_END_AT_EMPTY'
                ],
            ],
            'confirm-area-view'=>[
                'area_id'=>[
                    'required'=>'1',
                    'result'=>'AREA_ID_EMPTY',
                ],
                'advert_id'=>[
                    'required'=>'1',
                    'type' => 'int',
                    'result'=>'ADVERT_ID_EMPTY'
                ],
                'advert_time'=>[
                    'required'=>'1',
                    'type' => 'string',
                    'result'=>'ADVERT_TIME_EMPTY'

                ],
                'rate'=>[
                    'required'=>'1',
                    'type' => 'int',
                    'result'=>'ADVERT_RATE_EMPTY'
                ],
                'start_at'=>[
                    'required'=>'1',
                    'type' => 'string',
                    'result'=>'ADVERT_START_AT_EMPTY'
                ],
                'end_at'=>[
                    'required'=>'1',
                    'type' => 'string',
                    'result'=>'ADVERT_END_AT_EMPTY'
                ],
                'page'=>[
                    'type'=>'int',
                ],
                'request_types'=>[
                    'type'=>'string',
                ],
                'order_id'=>[
                    'type'=>'int'
                ],

            ],
        ];
    }
}
