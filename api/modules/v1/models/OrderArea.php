<?php

namespace api\modules\v1\models;

use common\libs\DataClass;
use common\libs\Redis;
use common\libs\RedisClass;
use common\libs\ToolsClass;
use Yii;
use yii\base\Exception;
use yii\web\NotFoundHttpException;

/**
 * 订单地区
 */
class OrderArea extends \api\core\ApiActiveRecord
{
    public $member_id;
    public $start_at;
    public $end_at;
    public $page;
    public $advert_id;
    public static function tableName()
    {
        return '{{%order_area}}';
    }

    public function createOrderArea($order_id,$advert_id,$rate,$advert_time,$start_at,$end_at){
        try{
            $areaModel = OrderAreaCache::getAreaByToken();
            if(empty($areaModel)){
                return 'ORDER_AREA_EMPTY';
            }
            $this->area_type = $areaModel['area_type'];
            $this->area_id = $areaModel['area_id'];
            $this->order_id = $order_id;
            $advert_key = AdvertPosition::getAdvertKey($advert_id);
            //获取可购买的街道
            $street_id = (new AdvertPrice())->getCanBuyStreetId($areaModel['area_id'],$advert_key,$rate,$advert_time,$start_at,$end_at);
            $this->street_area = implode(',',$street_id);
            $this->save();
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
            return "ERROR";
        }
    }
    /*
     * 查询地区屏幕详情
     */
    public function getModifyView($order_id){
        //订单信息
        $order = Order::find()->where(['id'=>$order_id, 'member_id'=>Yii::$app->user->id])->asArray()->one();
        //订单地区信息
        $order_area = OrderArea::find()->where(['order_id'=>$order_id])->select('street_area,area_id,area_type')->asArray()->one();
        //原订单的时间
        $order_date = OrderDate::find()->where(['order_id'=>$order_id])->select('start_at, end_at')->asArray()->one();

        if(empty($order) || empty($order_area) || empty($order_date)){
            return false;
        }
        $advert_time = ToolsClass::minuteCoverSecond($order['advert_time']);
        $areaArr = explode(',',$order_area['area_id']);
        //分页
        $page = (int)$this->page;
        if($page <= 0){
            $start = 0;
            $end = 10;
        }else{
            $end = 10;
            $start = ($page-1) * $end;
        }

        $page_array = array_slice($areaArr,$start,$end);

        $redis = RedisClass::init(3);
        //若日期交叉则交叉的日期默认充足，不再验证
        $dateList = ToolsClass::generateDateList($this->start_at,$this->end_at,'Ymd');
        //购买的时长
        $time = ToolsClass::minuteCoverSecond($order['advert_time']);
        //播放频次
        $rate = AdvertPosition::reduceRate($order['advert_id'],$order['rate']);
        $returnArr = [];
        $reformStreetArea = [];
        if($order_area['area_type'] < 4){
            $areaLength = strlen($areaArr[0]);
            $streetArea = explode(",",$order_area['street_area']);
            foreach($streetArea as $areaId){
                $reformStreetArea[substr($areaId,0,$areaLength)][] = $areaId;
            }
        }
        $orderThrowData = OrderThrowOrderDate::getOrderData($order_id);
        foreach ($page_array as $k => $v){
            //名称
            $returnArr[$k]['advert_name'] = $order['advert_name'];
            //地区
            $returnArr[$k]['area_id'] = $v;
            $returnArr[$k]['area_name'] = SystemAddress::getAreaNameById($v,'one');
            //屏幕数量
            $screenNum = $redis->get('system_screen_number:'.$v);
            $screenNum = json_decode($screenNum,true);
            $returnArr[$k]['screen_number'] = $screenNum['screen_number'];
            $returnArr[$k]['mirror_number'] = $screenNum['mirror_number'];

            //图片
            $returnArr[$k]['image_url'] = 'http://i1.bjyltf.com/system/default_area.jpg';
            //每天的余量信息
            $street_area_id = isset($reformStreetArea[$v]) ? $reformStreetArea[$v] : [];
            list($cantBuyDateMessage,$cantBuyDateNumber) = $this->getMarginByAreaEveryDay($dateList,$order_area['area_type'],$v,$order['advert_key'],$advert_time,$order['number'],$screenNum,$street_area_id,$order['total_day'],$orderThrowData);
            $buyDayNumber = $order['total_day'] - $cantBuyDateNumber;
            $returnArr[$k]['screen_number'] = $screenNum['screen_number'] * $buyDayNumber;
            $returnArr[$k]['mirror_number'] = $screenNum['mirror_number'] * $buyDayNumber;
            $returnArr[$k]['cant_buy_date'] = $cantBuyDateMessage;
        }
        return ['SUCCESS', $returnArr];
    }

    /*
     * 按日期查询余量信息
     */
    public function getMarginByAreaEveryDay($dateList,$area_type,$area,$advert_key,$time,$rate,$screenNum,$street_area_id,$total_day,&$orderThrowData = []){
        if (!$dateList || $area_type===false || !$area){
            return false;
        }
        $advert_key = strtolower($advert_key);
        $marginMsg = [];
        if($area_type == 4){
            foreach ($dateList as $date){
                if (in_array($date."_".$area,$orderThrowData)) {
                    continue;
                }
                #print_r($advert_key.','.$time.','.$rate.','.$date);
                $bigmap["advert_cell_status:{$area}"][] = ToolsClass::reduceBigMapKey($advert_key,$time,$rate,$date);
            }
            $bigmap = Redis::getBitMulti($bigmap);
            $listKey = 0;
            foreach ($dateList as $key=>$date){
                if (in_array($date."_".$area,$orderThrowData)) {
                    continue;
                }
                if($bigmap[$listKey]){
                    $marginMsg[] = $dateList[$key];
                }
                $listKey++;
            }
        }else{
            if(empty($street_area_id)){
                return ['',0];
                throw new NotFoundHttpException("没有找到订单所对应的街道ID");
            }
            $resultDate = [];
            foreach ($dateList as $date){
                foreach($street_area_id as $area_id){
                    if (in_array($date."_".$area_id,$orderThrowData)) {
                        continue;
                    }
                    $bitmap["advert_cell_status:{$area_id}"][] = ToolsClass::reduceBigMapKey($advert_key,$time,$rate,$date);
                }
            }
            $bitmap = Redis::getBitMulti($bitmap);
            $listK = 0;
            foreach($street_area_id as $area_id){
                foreach ($dateList as $date){
                    if (in_array($date."_".$area_id,$orderThrowData)) {
                        continue;
                    }
                    if($bitmap[$listK]){
                        $resultDate[$date] = 1;
                    }
                    $listK++;
                }
            }
            $marginMsg = array_keys($resultDate);
        }
        $word = '余量不足';
        if(!empty($marginMsg)){
            foreach($marginMsg as $key=>$date){
                $marginMsg[$key] = date('m-d',strtotime($date));
                if($key == 1){
                    break;
                }
            }
            $num = count($marginMsg);
            if($num == $total_day){
                return ['所选地区'.$word,$num];
            }
            if($num > 2){
                return [implode(",",array_slice($marginMsg,0,2)).'等'.$num.'天'.$word,$num];
            }
            $str = implode(",", $marginMsg);
            return [$str.$word,$num];
        }
        return ['',0];
    }

    /*
      * 场景
      * */
    public function scenes()
    {
        return [
            'create'=>[
                'area_id'=>[
                    'required'=>'1',
                    'result'=>'ORDER_AREA_EMPTY'
                ],
            ],
            'order-modify-view'=>[
                'order_id'=>[
                    'required'=>'1',
                    'result'=>'ORDER_ID_EMPTY'
                ],
                'member_id'=>[
                    'required'=>'1',
                    'result'=>'MEMBER_ID_EMPTY'
                ],
                'start_at'=>[
                    'required'=>'1',
                    'result'=>'START_AT_EMPTY'
                ],
                'end_at'=>[
                    'required'=>'1',
                    'result'=>'END_AT_EMPTY'
                ],
            ],
        ];
    }
}
