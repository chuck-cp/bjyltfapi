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

class Redis
{
    protected static $instance = null;

    /**
     * 构造函数
     * @param array $options 参数
     * @access public
     */
    public function __construct()
    {
        if (!extension_loaded('redis')) {
            throw new \BadFunctionCallException('not support: redis');
        }
        $redisConfig = Yii::$app->redis;
        self::$instance = new \Redis();
        self::$instance->connect($redisConfig->hostname,$redisConfig->port);
        if(isset($redisConfig->password)){
            self::$instance->auth($redisConfig->password);
        }
    }

    /*
     * 禁止clone
     */
    private function __clone(){

    }

    /**
     * 获取连接句柄
     * @return object Redis
     */
    public static function getInstance($db=0)
    {
        if (!is_object(self::$instance)) {
            new self();
        }
        self::$instance->select($db);
        return self::$instance;
    }

    public static function getBitMulti($list){
        try{
            $redis = self::getInstance(4);
            $pipe = $redis->multi(2);
            foreach($list as $k=>$v){
                foreach($v as $position){
                    $pipe->getbit($k,$position);
                }
            }
            return $pipe->exec();
        }catch (Exception $e){
            Yii::error($e->getMessage(),'db');
        }
    }
}
