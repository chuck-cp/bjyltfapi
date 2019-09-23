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

class CookieClass
{
    public static function get($key) {
        if (isset($_COOKIE[$key])) {
            return $_COOKIE[$key];
        }
    }

    public static function set($key,$value,$time = 108720,$path = '/') {
        return setcookie($key, $value,time() + $time, $path);
    }

    public static function del($key) {
        return setcookie($key, '',-1,'/');
    }
}
