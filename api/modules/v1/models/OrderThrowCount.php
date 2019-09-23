<?php

namespace api\modules\v1\models;

use api\core\ApiActiveRecord;
use common\libs\RedisClass;
use Yii;
use yii\base\Exception;
use yii\base\Model;

/**
 * 订单投放统计
 */
class OrderThrowCount extends Model
{
    public function createRecord($play_number){
        $playNumber = json_decode($play_number,true);
        $successNumber = 0;
        if(!is_array($playNumber)){
            return $successNumber;
        }
        $redis = RedisClass::init(1);
        Yii::error('[THROW_COUNT_INFO]'.$play_number,'db');
        foreach($playNumber as $play) {
            try {
                if (empty($play['play_number'])) {
                    continue;
                }
                if(!isset($play['material_name'])){
                    throw new Exception("数据格式错误");
                }
                if(!strstr($play['material_name'],"_")){
                    throw new Exception("数据格式错误");
                }
                $order = explode("_",$play['material_name']);
                if ($order[0] != 'ad') {
                    throw new Exception("不是订单数据");
                }
                if(count($order) < 3){
                    throw new Exception("数据格式错误");
                }
                $order_id = $order[1];
                $redis->rpush("system_throw_count_list",json_encode(['order_id'=>$order_id,'device_number'=>$play['device_number'],'play_number'=>$play['play_number'],'count'=>$play['count_at']]));
                $successNumber++;
            } catch (Exception $e) {
                Yii::error('[THROW_COUNT_ERROR]'. $e->getMessage().' data:'.json_encode($play),'db');
            }
        }
        return $successNumber;
    }

    /*
     * 场景
     * */
    public function scenes(){
        return [
            'create'=>[
                'play_number'=>[
                    'required'=>'1',
                    'result'=>'PLAY_NUMBER_EMPTY'
                ],
            ],
        ];
    }
}
