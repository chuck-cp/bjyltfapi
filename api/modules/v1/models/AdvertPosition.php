<?php

namespace api\modules\v1\models;

use common\libs\DataClass;
use common\libs\ToolsClass;
use Yii;

/**
 * 广告位表
 */
class AdvertPosition extends \api\core\ApiActiveRecord
{
    public static function tableName()
    {
        return '{{%advert_position}}';
    }

    //获取订单详情页广告信息
    public static function findByOrderView($advert_id){
        if(!$result = AdvertPosition::find()->where(['id'=>$advert_id])->select('format,size,spec')->asArray()->one()){
            $result = [
                'format'=>'',
                'size'=>'',
                'spec'=>'',
            ];
        }
        return $result;
    }

    public static function getAdvertKey($id){
        $positionModel = AdvertPosition::find()->where(['id'=>$id])->select('key')->asArray()->one();
        if(empty($positionModel)){
            return false;
        }
        return strtolower($positionModel['key']);
    }

    public function getAdvertPosition(){
        $advertPosition = self::find()->select('id,key,type,name,rate,format,size,spec,time')->orderBy('sort DESC')->asArray()->all();
        if(empty($advertPosition)){
            return [];
        }
        $advertPrice = AdvertPrice::find()->select('advert_id,time')->asArray()->all();
        if(empty($advertPrice)){
            return $advertPosition;
        }
        $reformPrice = [];
        foreach($advertPrice as $price){
            $reformPrice[$price['advert_id']][ToolsClass::minuteCoverSecond($price['time'])] = $price['time'];
        }
        foreach($advertPosition as $key=>$position){
            $advertPosition[$key]['time'] = str_replace(',','、',$position['time']);
            $advertPosition[$key]['spec'] = str_replace(',','、',$position['spec']);
            //$advertPosition[$key]['rate'] = str_replace(',','、',$position['rate']);
            $advertPosition[$key]['format'] = str_replace(',','、',$position['format']);
            switch (strtolower($position['key'])){
                case 'a1':
                    $advertPosition[$key]['describe'] = '内容广告为植入在故事中的广告，具有一定情节和观赏性';
                    break;
                case 'a2':
                    $advertPosition[$key]['describe'] = '传统广告，时长较短，让消费者迅速清晰了解商品';
                    break;
                default:
                    $advertPosition[$key]['describe'] = '';
            }
            $advertPosition[$key]['rate_list'] = strstr($position['rate'],",") ? explode(",",$position['rate']):[$position['rate']];
            if(isset($reformPrice[$position['id']])){
                ksort($reformPrice[$position['id']]);
                foreach($reformPrice[$position['id']] as $p){
                    $advertPosition[$key]['time_list'][] = $p;
                }
            }else{
                $advertPosition[$key]['time_list'] = [];
            }
        }
        return $advertPosition;
    }

    public function getAllAdvert(){
        $advertPosition = self::find()->select('id,name,key,time,rate,spec,format')->orderBy('sort DESC')->asArray()->all();
        if(empty($advertPosition)){
            return [];
        }
        foreach ($advertPosition as $k => $v){
            $advertPosition[$k]['time'] = '广告播放时长为：'.$v['time'];
            $advertPosition[$k]['rate'] = '广告购买频次：'.str_replace(',','次/每天、',$v['rate']).'次/每天';
            $advertPosition[$k]['spec'] = '广告素材尺寸：'.$v['spec'];
            $advertPosition[$k]['format'] = '支持的广告素材格式：'.$v['format'];
            switch (strtolower($v['key'])){
                case 'a1':
                    $advertPosition[$k]['describe'] = '内容广告为植入在故事中的广告，具有一定情节和观赏性';
                    break;
                case 'a2':
                    $advertPosition[$k]['describe'] = '传统广告，时长较短，让消费者迅速清晰了解商品';
                    break;
                default:
                    $advertPosition[$k]['describe'] = '';
            }
        }
        return $advertPosition;
    }
    /*
     * 计算频次
     * */
    public static function reduceRate($advert_id,$rate,$returnKey=0){
        $positionModel = AdvertPosition::find()->where(['id'=>$advert_id])->select('key,rate')->asArray()->one();
        if(empty($positionModel)){
            Yii::error("广告ID错误:{$advert_id}");
            return false;
        }
        //计算频次
        if(strstr($positionModel['rate'],",")){
            $mainRate = explode(",",$positionModel['rate'])[0];
        }else{
            $mainRate = $positionModel['rate'];
        }
        $rate = $rate / $mainRate;
        if(!is_int($rate)){
            Yii::error("频次计算错误:{$rate}/{$mainRate}");
            return false;
        }
        if($returnKey == 1){
            return [$rate,$positionModel['key']];
        }
        return $rate;
    }

    public function getAdvertPositionById($id){
        $positionModel = self::find()->select('id,type,name,rate,format,size,spec,time')->asArray()->one();
        $positionModel['rate_list'] = strstr($positionModel['rate'],",") ? explode(",",$positionModel['rate']):[$positionModel['rate']];
        $positionModel['time_list'] = AdvertPrice::find()->select('time')->where(['advert_id'=>$id])->asArray()->all();
        return $positionModel;
    }

}
