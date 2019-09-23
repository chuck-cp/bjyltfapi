<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use common\libs\ToolsClass;
use Yii;

/**
 * 广告剩余量统计
 */
class AdvertSpace extends ApiActiveRecord
{

    public static function tableName()
    {
        return '{{%advert_space}}';
    }

    /*
     * 获取指定时间段内最小的广告剩余量
     * */
    public function getMinSpaceTime($advert_id,$area_id,$start_at,$end_at){
        $spaceTime = self::find()->where(['and',['advert_id'=>$advert_id],['area_id'=>$area_id],['date','>=',$start_at],['date','<=',$end_at]])->select('date,space_time')->asArray()->all();
        if($spaceTime){
            $spaceTime = array_column($spaceTime,'date','space_time');
        }
        $dateList = ToolsClass::generateDateList($start_at,$end_at);
        $resultTime = 600;
        foreach($dateList as $date){
            $dateTime = isset($spaceTime[$date]) ? $spaceTime[$date] : 600;
            if($resultTime < $dateTime){
                $resultTime = $dateTime;
            }
        }
        return $resultTime;
    }

    /*
     * 计算库存
     * */
    public function reduceTime($advert_id,$time,$start_at,$end_at){
        $dateList = ToolsClass::generateDateList($start_at,$end_at);
        foreach($dateList as $date){
            if(!self::updateAllCounters(['time'=>'-'.$time],['and',['advert_id'=>$advert_id],['date'=>$date],['time','>=',$time]])){
                return 'REDUCE_TIME_ERROR';
            }
        }
    }


    public function scenes()
    {
        return [
            'payment'=>[
                'time'=>[
                    'type'=>'int'
                ],
                'date'=>[
                    'required'=>'1',
                    'result'=>'DATE_EMPTY'
                ],
                'area_id'=>[
                    'required'=>'1',
                    'result'=>'AREA_ID_EMPTY'
                ],
            ]
        ];
    }
}
