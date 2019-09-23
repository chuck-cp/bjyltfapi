<?php
namespace common\libs;
class QueueClass
{

    /*
     * 创建消息
     * */
    public static function push($queueKey,$content){
        $redis = RedisClass::init(1);
        if(is_array($content)){
            $content = json_encode($content);
        }
        return $redis->rpush($queueKey,$content);
    }
}