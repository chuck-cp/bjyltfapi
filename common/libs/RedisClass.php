<?php
/*
 * 小工具类
 * */
namespace common\libs;
use Yii;
use yii\base\Exception;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\web\Cookie;
use yii\web\UploadedFile;

class RedisClass
{
    public static function init($db=1){
        $redis = Yii::$app->redis;
        $redis->select($db);
        return $redis;
    }

    public static function set($key,$value,$db,$expire=0){
        $redis = self::init($db);

        $result = $redis->set($key,$value);
        if($result && $expire){
            $redis->expire($key,$expire);
        }
        return $result;
    }

    public static function publish($channel,$message,$db=0){
        $redis = self::init($db);
        return $redis->publish($channel,$message);
    }

    public static function sAddMulti($keys,$db){
        $redis = self::init($db);
        $redis->multi();
        foreach($keys as $key){
            $redis->sadd($key['key'],$key['value']);
        }
        return $redis->exec();
    }

    public static function getPipeline($keys,$db,$prefix=''){
        $redis = self::init($db);
        $redis->multi();
        foreach($keys as $key) {
            $redis->get($prefix.$key);
        }
        $resultData = $redis->exec();
        if(empty($resultData)){
            return false;
        }
        foreach($resultData as $key=>$data){
            $result[$keys[$key]] = $data;
        }
        return $result;
    }

    public static function get($key,$db=1){
        $redis = self::init($db);
        return $redis->get($key);
    }

    public static function del($key,$db=1){
        $redis = self::init($db);
        return $redis->del($key);
    }
    /*
     * 集合事务
     * */
    public static function setTrans($keys,$db=1){
        if(empty($keys)){
            return true;
        }
        $result = true;
        foreach($keys as $key){
            if(!self::sadd($key['key'],$key['value'])){
                $result = false;
                break;
            }
        }
        if(!$result){
            foreach($keys as $key){
                self::srem($key['key'],$key['value']);
            }
        }
        return $result;
    }

    public static function smembers($key,$db=1){
        $redis = self::init($db);
        return $redis->smembers($key);
    }

    public static function sadd($key,$value,$db=1){
        $redis = self::init($db);
        if($redis->sismember($key,$value)){
            return true;
        }
        return $redis->sadd($key,$value);
    }

    public static function srem($key,$value,$db=1){
        $redis = self::init($db);
        return $redis->srem($key,$value);
    }

    public static function rpush($key,$value,$db=1){
        $redis = self::init($db);
        return $redis->rpush($key,$value);
    }
}
