<?php

namespace api\modules\v1\models;

use api\core\MongoActiveRecord;
use common\libs\ArrayClass;
use common\libs\Redis;
use common\libs\ToolsClass;
use Yii;
use yii\mongodb\ActiveRecord;


class MongoAdvert extends MongoActiveRecord
{
    public $advert;
    public $start_at;
    public $total_day;
    public $end_at;
    public $advert_id;
    public $rate;

    public static function tableName()
    {
        return '{{%advert}}';
    }

    public function attributes()
    {
        return ["_id","shop_id","area_id","advert_key","advert_time","space_time","date","loc","space_rate"];
    }

    /*
     * 根据范围查找
     * @param longitude
     * */
    public function selectByRange($longitude,$latitude,$range,$page_type = 'list')
    {
        $where = array(
            'advert_time' => $this->advert_time,
            'advert_key' => $this->advert['key'],
            'date' => [
                '$lte' => $this->end_at,
                '$gte' => $this->start_at,
            ],
            'space_rate' => [
                '$gte' => (int)$this->rate
            ],
            'loc' => array(
                '$nearSphere' => array(
                    '$geometry' => array(
                        'type' => 'Point',
                        'coordinates' => array(doubleval($longitude), doubleval($latitude)),
                    ),
                    '$maxDistance' => intval($range),
                )
            )
        );
        if ($page_type == 'list') {
            return \Yii::$app->mongodb->getCollection('advert_stock_list')->find($where,['shop_id','date']);
        } else {
            return \Yii::$app->mongodb->getCollection('advert_stock_list')->distinct("shop_id",$where);
        }
    }

    // 地区地区ID查询
    public function selectByAreaId($area_id,$page_type = 'list')
    {
        $where = [];
        foreach ($area_id as $key => $value) {
            if (!$value = (int)$value) {
                unset($area_id[$key]);
                continue;
            }
            $where[] = ["area_id" => ['$regex' => "^{$value}"]];
        }
        if (empty($where)) {
            return [];
        }
        $where = [
            '$or' => $where,
            'advert_time' => $this->advert_time,
            'advert_key' => $this->advert['key'],
            'date' => [
                '$lte' => $this->end_at,
                '$gte' => $this->start_at,
            ],
            'space_rate' => [
                '$gte' => (int)$this->rate
            ],
        ];
        if ($page_type == 'list') {
            return [\Yii::$app->mongodb->getCollection('advert_stock_list')->find($where,['area_id','date','shop_id']),$area_id];
        } else {
            return \Yii::$app->mongodb->getCollection('advert_stock_list')->distinct("shop_id",$where);
        }
    }

    // 根据店铺ID查找
    public function selectByShopId($shop_id,$page_type = 'list')
    {
        if (empty($shop_id)) {
            return [];
        }
        $shop_id = array_map(function($value){
            return (int)$value;
        },$shop_id);
        $where = array(
            'shop_id' => ['$in'=>$shop_id],
            'advert_time' => $this->advert_time,
            'advert_key' => $this->advert['key'],
            'date' => [
                '$lte' => $this->end_at,
                '$gte' => $this->start_at,
            ],
            'space_rate' => [
                '$gte' => (int)$this->rate
            ],
        );
        if ($page_type == 'list') {
            return \Yii::$app->mongodb->getCollection('advert_stock_list')->find($where,['shop_id','date']);
        } else {
            return \Yii::$app->mongodb->getCollection('advert_stock_list')->distinct("shop_id",$where);
        }
    }

    /*
     * 按地区购买时重组数据
     * @param result_type string 返回类型(1、返回所有数据 2、只返回价格)
     * @param unsetData array 要删除的日期(该日期不用于统计已售完的天数)
     * */
    public function reformAdvertDataByArea($advertData,$areaLength,$resultType = 1,$unsetData = [])
    {
        if ($resultType == 1) {
            $resultData = [];
        }else {
            $resultData = [0,0];
        }
        if (empty($advertData)) {
            return $resultData;
        }
        $reformData = [];
        $shopData = [];
        $reformShopData = [];
        $selectType = (int)Yii::$app->request->get('select_type');
        $except_area_id = explode(",",Yii::$app->request->get('except_area_id'));
        $keyword = Yii::$app->request->get('keyword');
        if ($areaLength == 7) {
            // 按市购买，返回区
            foreach ($advertData as $key => $value) {
                $area_id = substr($value['area_id'],0,9);
                if (in_array($area_id,$except_area_id)) {
                    continue;
                }
                if (!isset($reformData[$area_id])) {
                    $reformData[$area_id] = [];
                }
                if (!isset($reformData[$area_id][$value['date']])) {
                    $reformData[$area_id][$value['date']] = [
                        'screen_number' => 0,
                        'shop_number' => 0,
                        'mirror_number' => 0
                    ];
                }
                if (!isset($shopData[$value['shop_id']])) {
                    $sData = $this->getShopData($value['shop_id']);
                    if (empty($sData)) {
                        continue;
                    }
                    $shopData[$value['shop_id']] = $sData;
                } else {
                    $sData = $shopData[$value['shop_id']];
                }
                if ($keyword && !strstr($sData['shop_city'].$sData['shop_area'],$keyword)) {
                    continue;
                }
                $reformShopData['shop_id'][] = $value['date'];
                $reformData[$area_id][$value['date']]['screen_number'] += $sData['screen_number'];
                $reformData[$area_id][$value['date']]['shop_number'] += 1;
                $reformData[$area_id][$value['date']]['mirror_number'] += $sData['mirror_account'];
            }
        } else {
            // 按区购买，返回街道
            foreach ($advertData as $key => $value) {
                if (in_array($value['area_id'],$except_area_id)) {
                    continue;
                }
                if (!isset($shopData[$value['shop_id']])) {
                    $sData = $this->getShopData($value['shop_id']);
                    if (empty($sData)) {
                        continue;
                    }
                    $shopData[$value['shop_id']] = $sData;
                } else {
                    $sData = $shopData[$value['shop_id']];
                }
                if ($keyword && !strstr($sData['shop_area'].$sData['shop_street'],$keyword)) {
                    continue;
                }
                if (!isset($reformData[$value['area_id']])) {
                    $reformData[$value['area_id']] = [];
                }
                if (!isset($reformData[$value['area_id']][$value['date']])) {
                    $reformData[$value['area_id']][$value['date']] = [
                        'screen_number' => 0,
                        'shop_number' => 0,
                        'mirror_number' => 0
                    ];
                }
                $reformShopData['shop_id'][] = $value['date'];
                $reformData[$value['area_id']][$value['date']]['screen_number'] += $sData['screen_number'];
                $reformData[$value['area_id']][$value['date']]['shop_number'] += 1;
                $reformData[$value['area_id']][$value['date']]['mirror_number'] += $sData['mirror_account'];
            }
        }
        if ($resultType == 1) {
            // 计算出剩余屏幕数量最多的一天和所有屏幕数量总和
            foreach ($reformData as $key => $value) {
                $soldOutDate = []; // 已售完的日期
                $maxScreenNumber = [
                    'screen_number' => 0,
                    'shop_number' => 0,
                    'mirror_number' => 0
                ];
                $totalScreenNumber = 0;
                foreach ($value as $date => $dateValue) {
                    if (empty($dateValue['screen_number']) && !empty($unsetData) && !in_array($date,$unsetData)) {
                        // 屏幕数量为0时，该日期计入已售完地区
                        $soldOutDate[] = $date;
                    }
                    if ($dateValue['screen_number'] > $maxScreenNumber['screen_number']) {
                        $maxScreenNumber = $dateValue;
                    }
                    $totalScreenNumber += $dateValue['screen_number'];
                }
                $areaData = $this->getAreaData($key);
                if ($areaLength == 7) {
                    $buy_parent_area_name = $areaData['area_name'][1];
                } else {
                    $buy_parent_area_name = $areaData['area_name'][1].'-'.$areaData['area_name'][2];
                }
                $price = $this->advert['price']['price_'.$areaData['area_level']];
                $resultData[] = [
                    'image_url'=>'http://i1.bjyltf.com/system/default_area.jpg',
                    'area_id'=> (string)$key,
                    'area_name'=> $areaData['area_name'][1].$areaData['area_name'][2],
                    'buy_parent_area_name' => $buy_parent_area_name,
                    'buy_area_name' => $areaData['area_name'][2],
                    'cant_buy_date'=> $this->reduceCantButDate($soldOutDate),
                    'advert_name'=> $this->advert['name'],
                    'price'=> $price,
                    'total_price' =>(string)sprintf("%.2f",ToolsClass::priceConvert($totalScreenNumber * $price)),
                    'advert_time'=> $this->advert_time,
                    'shop_number' => (string)$maxScreenNumber['shop_number'],
                    'mirror_number' => (string)$maxScreenNumber['mirror_number'],
                    'screen_number' => (string)$maxScreenNumber['screen_number'],
                    'total_screen_number' => (string)$totalScreenNumber,
                    'max_covered_number' => (string)($totalScreenNumber * 20 * $this->total_day * $this->rate),
                    'covered_number' => (string)($totalScreenNumber * 60),
                    'watch_number' => (string)($totalScreenNumber * 20),
                ];
            }
            $sort = Yii::$app->request->get('sort');
            if ($sort != 'asc') {
                $sort = 'desc';
            }
            return ArrayClass::sort($resultData,'screen_number',$sort);
        } else {
            $result_price = 0;
            $result_screen_number = 0;
            foreach ($reformData as $key => $value) {
                $areaData = $this->getAreaData($key);
                $screen_number = array_column($value,"screen_number");
                $screen_number = array_sum($screen_number);
                $result_screen_number += $screen_number;
                $result_price += $screen_number * $this->advert['price']['price_'.$areaData['area_level']];
            }
            $result_shop = [];
            foreach ($reformShopData as $key => $value) {
                $result_shop[] = [
                    'shop_id' => $key,
                    'date' => $value,
                    'software_number' => Screen::find()->where(['shop_id' => $key])->select('software_number')->asArray()->all()
                ];
            }
            return [$result_screen_number,$result_price,$result_shop];
        }
    }

    // 计算已售完的地区
    public function reduceCantButDate($cant_buy_date)
    {
        if (empty($cant_buy_date)) {
            return '';
        }
        if (count($cant_buy_date) == $this->total_day) {
            return '所选地区余量不足';
        } elseif (count($cant_buy_date) > 2) {
            return implode(",",array_slice($cant_buy_date,0,2)).'等'.count($cant_buy_date).'天';
        }else {
            return implode(",",$cant_buy_date);
        }
    }

    /*
     * 按范围或店铺购买时重组数据
     * @param resultType string 返回类型(1、返回所有数据 2、只返回价格)
     * @param unsetData array 要删除的日期(该日期不用于统计已售完的天数)
     * */
    public function reformAdvertDataByShopId($advertData,$resultType = 1,$unsetData = [])
    {
        if ($resultType == 1) {
            $resultData = [];
        }else {
            $resultData = [0,0];
        }
        if (empty($advertData)) {
            return $resultData;
        }
        $orderDate = ToolsClass::generateDateList($this->start_at,$this->end_at,'Y-m-d',2);
        if ($unsetData) {
            foreach ($unsetData as $value) {
                unset($orderDate[$value]);
            }
        }
        $orderDate = array_keys($orderDate);
        $reformData = [];
        foreach ($advertData as $key => $value) {
            $reformData[$value['shop_id']][] = $value['date'];
        }
        foreach ($reformData as $key => $value) {
            $shopModel = Shop::find()->where(['id' => $key])->select('area,name,mirror_account,screen_number')->asArray()->one();
            if (empty($shopModel)) {
                continue;
            }
            if (!isset($areaData[$shopModel['area']])) {
                $areaData[$shopModel['area']] = $this->getAreaData($shopModel['area']);
            }
            $aData = $areaData[$shopModel['area']];
            $price = $this->advert['price']['price_'.$aData['area_level']];
            if ($resultType == 1) {
                $resultData[] = [
                    'shop_id' => $key,
                    'shop_name' => $shopModel['name'],
                    'buy_parent_area_name' => $aData['area_name'][0].'-'.$aData['area_name'][1],
                    'image_url' => 'http://i1.bjyltf.com/system/default_area.jpg',
                    'cant_buy_date'=> $this->reduceCantButDate(array_diff($orderDate,$value)),
                    'advert_name'=> $this->advert['name'],
                    'price'=> $price,
                    'total_price' =>(string)sprintf("%.2f",ToolsClass::priceConvert($shopModel['screen_number'] * $price)),
                    'advert_time'=> $this->advert_time,
                    'mirror_number' => $shopModel['mirror_account'],
                    'screen_number' => $shopModel['screen_number'],
                    'total_screen_number' => (string)$shopModel['screen_number'],
                    'max_covered_number' => (string)($shopModel['screen_number'] * 20 * $this->total_day * $this->rate),
                    'covered_number' => (string)($shopModel['screen_number'] * 60),
                    'watch_number' => (string)($shopModel['screen_number'] * 20),
                ];
            } else {
                $resultData[0] += $shopModel['screen_number'];
                $resultData[1] += $price;
                $resultData[2][] = [
                    'shop_id' => $key,
                    'date' => $value,
                    'software_number' => Screen::find()->where(['shop_id' => $key])->select('software_number')->asArray()->all()
                ];
            }
        }
        return $resultData;
    }

    // 重组地图页面数据
    public function reformAdvertDataByMap($shop_id)
    {
        $result = [
            'selectedShop' => [],
            'shop' => []
        ];
        $select_area_id = Yii::$app->request->get('select_area_id');
        $select_shop_id = Yii::$app->request->get('select_shop_id');
        $shopModel = Shop::find()->where(['id' => $shop_id])->select('longitude,latitude,id,area')->asArray()->all();
        if (empty($shopModel)) {
            return $result;
        }
        if (empty($select_shop_id) && empty($select_area_id)) {
            foreach ($shopModel as $shop) {
                $result['shop'][] = [
                    'id' => $shop['id'],
                    'longitude' => $shop['longitude'],
                    'latitude' => $shop['latitude']
                ];
            }
        } elseif ($select_shop_id) {
            foreach ($shopModel as $shop) {
                if (in_array($shop['id'],$select_shop_id)) {
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
            $area_id_length = strlen($select_area_id[0]);
            foreach ($shopModel as $shop) {
                $area_id = substr($shop['area'],0,$area_id_length);
                if (in_array($area_id,$select_area_id)) {
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
        }
        return $result;
    }
    // 查询确认地区页面的地区数据
    public function selectConfirmOrderArea()
    {
        list($select_area_id,$select_shop_id) = OrderAreaCache::getArea(3,2);
        if (empty($select_shop_id) && empty($select_area_id)) {
            return [];
        } else if ($select_area_id) {
            list($advertData,$area_id) = $this->selectByAreaId($select_area_id);
        } else {
            $advertData = $this->selectByShopId($select_shop_id);
        }
        $result = [];
        $length_1 = 5;
        $length_2 = 7;
        if ($select_area_id && strlen($select_area_id[0]) == 12) {
            $length_1 = 7;
            $length_2 = 9;
        }
        foreach ($advertData as $value) {
            $city_id = substr($value['area_id'],0,$length_1);
            $area_id = substr($value['area_id'],0,$length_2);
            if (!isset($result[$city_id])) {
                $result[$city_id] = [
                    'area_name' => SystemAddress::getAreaNameById($city_id,'one'),
                    'shop_number' => 0,
                    'item' => []
                ];
            }
            $result[$city_id]['shop_number']++;
            if (!isset($result[$city_id]['item'][$area_id])) {
                $result[$city_id]['item'][$area_id] = [
                    'area_name' => SystemAddress::getAreaNameById($city_id,'one'),
                    'shop_number' => 0
                ];
            }
            $result[$city_id]['item'][$area_id]['shop_number']++;
        }
        foreach ($result as $key => $value) {
            sort($result[$key]['item']);
        }
        sort($result);
        return $result;
    }

    // 查询确认地区页面的数据
    public function selectConfirmOrderData()
    {
        $positionModel = AdvertPosition::find()->where(['id'=>$this->advert_id])->select('rate,name,format,size,spec,key')->asArray()->one();
        if(empty($positionModel)){
            return 'ADVERT_PRICE_CONFIG_ERROR';
        }
        list($select_area_id,$select_shop_id) = OrderAreaCache::getArea(3,2);
        if (empty($select_shop_id) && empty($select_area_id)) {
            return [];
        } else if ($select_area_id) {
            list($advertData,$area_id) = $this->selectByAreaId($select_area_id);
            list($result_screen_number,$result_price,$select_shop_data) = $this->reformAdvertDataByArea($advertData,strlen($area_id[0]),2);
            $area_name = SystemAddress::getAreaNameById($area_id[0],'ALL');
        } else {
            $advertData = $this->selectByShopId($select_shop_id);
            list($result_screen_number,$result_price,$select_shop_data) = $this->reformAdvertDataByShopId($advertData,2);
            $area_name = '';
            if ($shopData = $this->getShopData($select_shop_id[0])) {
                $area_name = $shopData['shop_city'].$shopData['shop_area'];
            }
        }
        $order_token  = md5($result_price.$area_name.$this->advert_id.$this->advert_time.$this->rate.$this->start_at.$this->end_at.Yii::$app->params['systemSalt'].time());
        $token = Yii::$app->request->post('token');
        OrderAreaCache::updateAll(['order_token' => $order_token],['token' => $token]);
        if ($select_shop_data) {
            Redis::getInstance(3)->set("order_shop_data:{$token}",json_encode([
                'advert_id' => $this->advert_id,
                'advert_time' => $this->advert_time,
                'advert_key' => $this->advert_key,
                'rate' => $this->rate,
                'shop_data' => $select_shop_data
            ]));
        }
        return [
            'order_token' => $order_token,
            'order_price' => (string)sprintf("%.2f",$result_price),
            'prepayment_ratio' => SystemConfig::getConfig('prepayment_ratio'),
            'area_name' => $area_name,
            'name' => $positionModel['name'],
            'format' => $positionModel['format'],
            'size' => $positionModel['size'],
            'spec' => $positionModel['spec'],
            'total_time' => $this->advert_time,
            'screen_number' => (string)$result_screen_number,
            'system_order_price' => SystemConfig::getConfig('system_order_price'),
        ];
    }

    // 查询广告地区
    public function selectAdvertArea()
    {
        $parent_id = (int)Yii::$app->request->get('parent_id');
        if ($parent_id) {
            $where = ['parent_id' => $parent_id,'is_buy' => 1];
        } else {
            $area_id = OrderAreaCache::getParentAreaId();
            if (empty($area_id)) {
                return [];
            }
            $where = ['id' => $area_id,'is_buy' => 1];
        }
        return SystemAddress::find()->where($where)->select('id,name')->asArray()->all();
    }

    // 查询广告日历
    public function selectAdvertNumber()
    {
        $page = (int)Yii::$app->request->get('page');
        if(empty($page)){
            $start = 0;
            $end = 5;
        }else{
            $end = 5;
            $start = ($page - 1) * $end;
        }
        $date_list = ToolsClass::generateDateList($this->start_at,$this->end_at);
        $date_list = array_slice($date_list,$start,$end);
        if(empty($date_list)){
            return false;
        }
        $this->start_at = $date_list[0];
        $this->end_at = $date_list[count($date_list) - 1];
        $area_id = Yii::$app->request->get('area_id');
        $shop_id = Yii::$app->request->get('shop_id');
        $result_area = [];
        if (empty($area_id) && empty($shop_id)) {
            return [];
        } elseif (empty($shop_id)) {
            // 按地区查询
            list($advertData,$area_id) = $this->selectByAreaId($area_id);
            // 获取地区ID长度
            $area_id_length = strlen($area_id[0]);
            $result_date = $this->getDefaultResultData($area_id,$date_list);
            foreach ($advertData as $value) {
                $sub_area_id = substr($value['area_id'],0,$area_id_length);
                $result_date[$value['date']][$sub_area_id]++;
            }
            if (empty($start)) {
                # 第一页时返回地区信息
                if ($area_id_length == 12) {
                    foreach ($area_id as $value) {
                        $sData = $this->getAreaData($value);
                        $result_area[] = [
                            'id' => $value,
                            'name' => $sData['area_name'][2]
                        ];
                    }
                } else {
                    foreach ($area_id as $value) {
                        $sData = $this->getAreaData($value);
                        $result_area[] = [
                            'id' => $value,
                            'name' => $sData['area_name'][1].$sData['area_name'][2]
                        ];
                    }
                }
            }
        } else {
            // 按店铺查询
            $shop_id = explode(",",$shop_id);
            $advertData = $this->selectByShopId($shop_id);
            $result_date = $this->getDefaultResultData($shop_id,$date_list,'无余量');
            foreach ($advertData as $value) {
                $result_date[$value['date']][$value['shop_id']] = '充足';
            }
            if (empty($start)) {
                $shopModel = Shop::find()->where(['id' => $shop_id,'status' => 5])->select('id,name')->indexBy('id')->asArray()->all();
                if ($shopModel) {
                    foreach ($shop_id as $value) {
                        $result_area[] = [
                            'id' => $value,
                            'name' => isset($shopModel[$value]) ? $shopModel[$value]['name'] : ''
                        ];
                    }
                }
            }
        }
        return [
            'area' => $result_area,
            'item' => $result_date
        ];
    }

    // 获取广告日历时,初始化默认返回的结果
    public function getDefaultResultData($idList,$dateList,$content = 0)
    {
        $idArray = [];
        foreach ($idList as $id){
            $idArray[$id] = $content;
        }
        $result = [];
        foreach ($dateList as $date) {
            $result[$date] = $idArray;
        }
        return $result;
    }

    // 修改订单页面查看广告日历
    public function selectAdvertNumberModify()
    {
        $page = (int)Yii::$app->request->get('page');
        if(empty($page)){
            $start = 0;
            $end = 5;
        }else{
            $end = 5;
            $start = ($page - 1) * $end;
        }
        $date_list = ToolsClass::generateDateList($this->start_at,$this->end_at);
        $date_list = array_slice($date_list,$start,$end);
        if(empty($date_list)){
            return false;
        }

        $orderModel = Order::find()->where(['id' => $this->order_id])->select('number,advert_key,advert_name,advert_time,advert_from')->asArray()->one();
        if (empty($orderModel)) {
            return 'ORDER_ID_ERROR';
        }
        $shop_id = \Yii::$app->mongodb->getCollection('order_shop_list')->distinct("shop_id",[
            'order_id' => $this->order_id
        ]);
        $unsetData = ToolsClass::generateDateList($orderModel['start_at'],$orderModel['end_at']);
        $where = array(
            'shop_id' => ['$in'=>$shop_id],
            'advert_time' => $orderModel['advert_time'],
            'advert_key' => $orderModel['advert_key'],
            'date' => [
                '$lte' => $date_list[$start + 4],
                '$gte' => $date_list[0],
            ],
            'space_rate' => [
                '$gte' => $orderModel['number']
            ],
        );
        $advertData = \Yii::$app->mongodb->getCollection('advert_stock_list')->find($where,['shop_id','date']);
        $result_area = [];

        if ($orderModel['advert_from'] == 3) {
            // 按店铺查询
            $result_date = $this->getDefaultResultData($shop_id,$date_list,'无余量');
            foreach ($advertData as $value) {
                $result_date[$value['date']][$value['shop_id']] = '充足';
            }
            if (empty($start)) {
                $shopModel = Shop::find()->where(['id' => $shop_id,'status' => 5])->select('id,name')->indexBy('id')->asArray()->all();
                if ($shopModel) {
                    foreach ($shop_id as $value) {
                        $result_area[] = [
                            'id' => $value,
                            'name' => isset($shopModel[$value]) ? $shopModel[$value]['name'] : ''
                        ];
                    }
                }
            }
        } else {
            if ($orderModel['advert_from'] == 2) {
                $area_id_length = 12;
            }else {
                $area_id_length = 9;
            }
            $area_id = \Yii::$app->mongodb->getCollection('order_shop_list')->distinct("area_id",[
                'order_id' => $this->order_id
            ]);
            // 获取地区ID长度
            $result_date = $this->getDefaultResultData($area_id,$date_list);
            foreach ($advertData as $value) {
                $sub_area_id = substr($value['area_id'],0,$area_id_length);
                $result_date[$value['date']][$sub_area_id]++;
            }
            if (empty($start)) {
                # 第一页时返回地区信息
                if ($area_id_length == 12) {
                    foreach ($area_id as $value) {
                        $sData = $this->getAreaData($value);
                        $result_area[] = [
                            'id' => $value,
                            'name' => $sData['area_name'][2]
                        ];
                    }
                } else {
                    foreach ($area_id as $value) {
                        $sData = $this->getAreaData($value);
                        $result_area[] = [
                            'id' => $value,
                            'name' => $sData['area_name'][1].$sData['area_name'][2]
                        ];
                    }
                }
            }
        }
        return [
            'area' => $result_area,
            'item' => $result_date
        ];
    }

    // 修改订单页面查看广告列表
    public function selectAdvertPriceModify()
    {
        $orderModel = Order::find()->where(['id' => $this->order_id])->select('number,advert_key,advert_name,advert_time,advert_from')->asArray()->one();
        if (empty($orderModel)) {
            return 'ORDER_ID_ERROR';
        }
        $shop_id = \Yii::$app->mongodb->getCollection('order_shop_list')->distinct("shop_id",[
            'order_id' => $this->order_id
        ]);
        $unsetData = ToolsClass::generateDateList($orderModel['start_at'],$orderModel['end_at']);
        $where = array(
            'shop_id' => ['$in'=>$shop_id],
            'advert_time' => $orderModel['advert_time'],
            'advert_key' => $orderModel['advert_key'],
            'date' => [
                '$lte' => $this->end_at,
                '$gte' => $this->start_at,
            ],
            'space_rate' => [
                '$gte' => $orderModel['number']
            ],
        );
        if ($orderModel['advert_from'] == 3) {
            // 按店铺名称或范围购买
            $advertData = \Yii::$app->mongodb->getCollection('advert_stock_list')->find($where,['shop_id','date']);
            return $this->reformAdvertDataByShopId($advertData,1,$unsetData);
        } else {
            // 按区或市购买
            $advertData = \Yii::$app->mongodb->getCollection('advert_stock_list')->find($where,['area_id','date']);
            if ($orderModel['advert_from'] == 1) {
                return $this->reformAdvertDataByArea($advertData,7,1,$unsetData);
            } else {
                return $this->reformAdvertDataByArea($advertData,9,1,$unsetData);
            }
        }
    }

    // 查询广告价格
    public function selectAdvertPrice()
    {
        // select_type 搜索条件(1、市 2、区 3、范围 4、店铺名称)
        $selectType = (int)Yii::$app->request->get('select_type');
        // page_type 用于区分是列表页还是地图页面，列表页返回完整数据，地图页只返回店铺坐标
        $pageType = Yii::$app->request->get('page_type');
        $resultData = [];
        if ($pageType != 'map') {
            $pageType  = 'list';
        }
        if ($selectType == 1 || $selectType == 2) {
            // 时或区
            $area_id = OrderAreaCache::getArea(1,2);
            if (empty($area_id)) {
                return [];
            }
            list($advertData,$area_id) = $this->selectByAreaId($area_id,$pageType);
            if ($pageType == 'list') {
                $resultData = $this->reformAdvertDatabyArea($advertData,strlen($area_id[0]));
            } else {
                $resultData = $this->reformAdvertDataByMap($advertData);
            }
        } elseif ($selectType == 3) {
            // 区域半径
            $longitude = Yii::$app->request->get('longitude');
            if (empty($longitude)) {
                $resultData = 'LONGITUDE_NO_EMPTY';
            }
            $latitude = Yii::$app->request->get('latitude');
            if (empty($latitude)) {
                $resultData = 'LATITUDE_NO_EMPTY';
            }
            $range = Yii::$app->request->get('range');
            if (empty($range)) {
                $resultData = 'RANGE_NO_EMPTY';
            }
            $advertData = $this->selectByRange($longitude,$latitude,$range,$pageType);
            if ($pageType == 'list') {
                $resultData = $this->reformAdvertDataByShopId($advertData);
            } else {
                $resultData = $this->reformAdvertDataByMap($advertData);
            }
        } elseif ($selectType == 4) {
            // 店铺名称
            $shop_name = Yii::$app->request->get('shop_name');
            if (empty($shop_name)) {
                $resultData = 'SHOP_NAME_NO_EMPTY';
            }
            $shopModel = Shop::find()->select('id')->where(['and',['like','name',$shop_name],['status' => 5],['>','screen_number',0]])->asArray()->all();
            if (!empty($shopModel)) {
                $shop_id = array_column($shopModel,'id');
                $advertData = $this->selectByShopId($shop_id);
                if ($pageType == 'list') {
                    $resultData =  $this->reformAdvertDataByShopId($advertData);
                } else {
                    $resultData = $this->reformAdvertDataByMap($advertData);
                }
            }
        } else {
            $resultData = 'SELECT_TYPE_ERROR';
        }
        return $resultData;
    }

    public function checkAdvertId($advert_id)
    {
        $reformAdvertKey = [
            'A1' => 1,
            'A2' => 2,
            'B' => 3,
            'C' => 4,
            'D' => 5
        ];
        $advertModel = AdvertPosition::find()->where(['id' => $advert_id])->select('name,key')->asArray()->one();
        $priceModel = AdvertPrice::find()->where(['advert_id' => $advert_id,'time' => $this->advert_time])->select('price_1,price_2,price_3')->asArray()->one();
        if (empty($advertModel) || empty($priceModel)) {
            return false;
        }
        $this->advert = [
            'name' => $advertModel['name'],
            'key' => isset($reformAdvertKey[$advertModel['key']]) ? $reformAdvertKey[$advertModel['key']] : 0,
            'price' => $priceModel
        ];
        return true;
    }

    public function checkAdvertTime($advert_time)
    {
        return $this->advert_time = ToolsClass::minuteCoverSecond($advert_time);
    }

    /*
     * 获取该店铺的地区、镜面数量、屏幕数量
     * */
    public function getShopData($shop_id)
    {
        $shopModel = Shop::find()->where(['id' => $shop_id])->select('shop_city,shop_area,shop_street,mirror_account')->asArray()->one();
        if (empty($shopModel)){
            return [];
        }
        return $shopModel;
    }

    // 获取地区等级
    public function getAreaData($area_id) {
        $screenData = Redis::getInstance(3)->get("system_screen_number:{$area_id}");
        if (!empty($screenData)){
            $screenData = json_decode($screenData,true);
            $screenData['area_name'] = explode("-",$screenData['area_name']);
            if ($screenData['area_level'] > 3) {
                $screenData['area_level'] = 3;
            }
        } else {
            if (strlen($area_id) == 12) {
                $area_name = [
                    SystemAddress::getAreaNameById(substr($area_id,0,7),'one'),
                    SystemAddress::getAreaNameById(substr($area_id,0,9),'one'),
                    SystemAddress::getAreaNameById($area_id,'one')
                ];
            } else {
                $area_name = [
                    SystemAddress::getAreaNameById(substr($area_id,0,5),'one'),
                    SystemAddress::getAreaNameById(substr($area_id,0,7),'one'),
                    SystemAddress::getAreaNameById($area_id,'one')
                ];
            }
            $screenData = [
                'area_name' => $area_name,
                'area_level' => 3,
                'screen_number' => 0,
                'shop_number' => 0,
                'mirror_number' => 0
            ];
        }
        return $screenData;
    }

    //转换购买的频次
    public function checkRate($rate)
    {
        $position = AdvertPosition::find()->where(['id' => $this->advert_id])->select('rate')->asArray()->one();
        $r = explode(",",$position['rate'])[0];
        $this->rate = (int)($rate / $r);
        return true;
    }

    // 计算要购买的总天数
    public function reduceTotalDay()
    {
        return $this->total_day = ((strtotime($this->end_at) - strtotime($this->start_at)) / 86400) + 1;
    }

    public function scenes()
    {
        return [
            'select'=>[
                'advert_id'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'ORDER_ADVERT_ID_EMPTY'
                        ],
                        [
                            'function' => "this::checkAdvertId",
                            'result'=>'ORDER_ADVERT_ID_ERROR'
                        ]
                    ]
                ],
                'rate'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'ORDER_RATE_EMPTY'
                        ],
                        [
                            'function' => 'this::checkRate',
                        ]
                    ],

                ],
                'advert_time'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'ORDER_ADVERT_TIME_EMPTY'
                        ],
                        [
                            'function' => "this::checkAdvertTime",
                            'result'=>'ORDER_ADVERT_TIME_ERROR'
                        ]
                    ]
                ],
                'start_at'=>[
                    'required'=>'1',
                    'result'=>'START_AT_EMPTY'
                ],
                'end_at'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'END_AT_EMPTY'
                        ],
                        [
                            'function::reduceTotalDay'
                        ]
                    ]
                ],
            ],
            'select-modify'=>[
                'order_id' => [
                    'required'=>'1',
                    'result'=>'ORDER_ID_EMPTY'
                ],
                'start_at'=>[
                    'required'=>'1',
                    'result'=>'START_AT_EMPTY'
                ],
                'end_at'=>[
                    [
                        [
                            'required'=>'1',
                            'result'=>'END_AT_EMPTY'
                        ],
                        [
                            'function::reduceTotalDay'
                        ]
                    ]
                ],
            ]
        ];
    }
}
