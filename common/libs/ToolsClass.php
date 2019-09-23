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

class ToolsClass
{
    public static function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile',
                'okhttp',
                'com.16lao.ylmedia'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
    }
    //获取真实IP
    public static function getIp() {
        if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
            $ip = getenv("HTTP_CLIENT_IP");
        else
            if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
                $ip = getenv("HTTP_X_FORWARDED_FOR");
            else
                if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
                    $ip = getenv("REMOTE_ADDR");
                else
                    if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
                        $ip = $_SERVER['REMOTE_ADDR'];
                    else
                        $ip = "unknown";
        return ($ip);
    }

    /*
     * 计算地区出售状态的key
     * */
    public static function reduceBigMapKey($advertKey,$advertTime,$rateNumber,$date){
        $advertKey = strtolower($advertKey);
        $advertRateList = [
            'a1_60' => 0,
            'a1_120' => 1,
            'a1_150' => 2,
            'a1_180' => 3,
            'a1_240' => 4,
            'a1_300' => 5,
            'a2_5' => 6,
            'a2_10' => 7,
            'a2_15' => 8,
            'a2_20' => 9,
            'a2_25' => 10,
            'a2_30' => 11,
            'a2_60' => 12,
            'b_30' => 13,
            'c_60' => 14,
            'd_60' => 15,
        ];
        if(!isset($advertRateList[$advertKey.'_'.$advertTime])){
            return false;
        }
        $system_start_at = "2019-01-01";
        $offset = self::timediff($system_start_at,$date);
        $bigMapKey = ($offset * 200) + ($advertRateList[$advertKey.'_'.$advertTime] * 10 + $rateNumber);
        return $bigMapKey;
    }

    /*
     * 分钟转秒
     * */
    public static function minuteCoverSecond($minute){
        return strstr($minute,"分钟") ? str_replace("分钟","",$minute) * 60 : str_replace("秒","",$minute);
    }
    /*
     * 获取用户提交的参数
     * */
    public static function getParams($field){
        if(Yii::$app->request->isGet){
            return Yii::$app->request->get($field);
        }else{
            return Yii::$app->request->post($field);
        }
    }

    /*
     * 将数据的key值转为对于的汉字
     * */
    public static function getCommonStatus($array_name,$key){
        $data = DataClass::$array_name();
        if(isset($data[$key])){
            return $data[$key];
        }
    }
    /*
     * 腾讯云上传地址转换
     * */
    public static function replaceCosUrl($url){
        if(empty($url)){
            return $url;
        }
        $url = str_replace('http://yulongchuanmei-1255626690.file.myqcloud.com','https://i1.bjyltf.com',$url);
        return str_replace('http://yulongchuanmei-1255626690.cossh.myqcloud.com','https://i1.bjyltf.com',$url);
    }
    /*
     * 时间转换
     * */
    public static function timeConvert($time){
        if(empty($time)){
            return $time;
        }
        if(!is_numeric($time)){
            $time = strtotime($time);
        }

        $nowTime = time();
        $nowDateTime = strtotime(date('Y-m-d'));
        $dateTime = strtotime(date('Y-m-d',$time));
        $year = date('Y',$time);
        $nowYear = date('Y',$nowTime);
        if($year < $nowYear){
            return date('Y年m月d',$time);
        }
        $diffTime = $nowTime - $time;

        //小于60秒
        if($diffTime < 60) {
            $resultTime = '刚刚';
        }elseif($diffTime < 3600){
            //小于60分钟
            $resultTime = ceil($diffTime / 60).'分钟前';
        }elseif($nowDateTime == $dateTime){
            //小于一天
            $hour = date('H',$time);
            $minute = date('m',$time);
            if($hour < 12){
                $prefix = '上午';
            }else{
                $prefix = '下午';
            }
            $resultTime = $prefix.' '.$hour.':'.$minute;
        }else{
            //大于一周
            $resultTime = date('m月d日',$time);
        }
        return $resultTime;
    }

    /*
     * 验证验证码
     * @param string mobile 手机号
     * @param string verify 验证码
     * @param string country_code 國家區號
     * */
    public static function checkVerify($mobile,$verify){
        $resultCurl = ToolsClass::curl(\Yii::$app->params['memberServerUrl']."/Verificationcode/rest",[
            "mobile"=>$mobile,
            "code"=>$verify,
        ],'POST',1);
        $resultCurl = json_decode($resultCurl,true);
        return $resultCurl['status'] == 600;
    }
    /*
     * 加密手机号
     * @param mobile int 手机号
     * */
    public static function encryptMobile($mobile){
        return substr($mobile,0,3).'****'.substr($mobile,7,11);
    }
    /*
     * 获取delete提交过来的数据
     * */
    public static function getParamsByDelete(){
        try{
            if(Yii::$app->request->get('token')){
                return Yii::$app->request->get();
            }
            $putData = file_get_contents("php://input");
            $resultData = json_decode($putData,true);
            if(is_array($resultData)){
                //解析IOS提交的PUT数据
                return $resultData;
            }
            if(!strstr($putData,"\r\n")){
                //解析本地测试工具提交的PUT数据
                parse_str($putData,$putData);
                return $putData;
            }
            //解析PHP CURL提交的PUT数据
            $putData = explode("\r\n",$putData);
            $resultData = [];
            foreach($putData as $key=>$data){
                if(substr($data,0,20) == 'Content-Disposition:'){
                    preg_match('/.*\"(.*)\"/',$data,$matchName);
                    $resultData[$matchName[1]] = $putData[$key+2];
                }
            }
            return $resultData;
        }catch (Exception $e){
            return [];
        }
    }
    /*
     * 获取put提交过来的数据
     * */
    public static function getParamsByPut(){
        try{
            if(Yii::$app->request->get('token')){
                return Yii::$app->request->get();
            }
            $putData = file_get_contents("php://input");
            $resultData = json_decode($putData,true);
            if(is_array($resultData)){
                //解析IOS提交的PUT数据
                return $resultData;
            }
            if(!strstr($putData,"\r\n")){
                //解析本地测试工具提交的PUT数据
                parse_str($putData,$putData);
                return $putData;
            }
            //解析PHP CURL提交的PUT数据
            $putData = explode("\r\n",$putData);
            $resultData = [];
            foreach($putData as $key=>$data){
                if(substr($data,0,20) == 'Content-Disposition:'){
                    preg_match('/.*\"(.*)\"/',$data,$matchName);
                    $resultData[$matchName[1]] = $putData[$key+2];
                }
            }
            return $resultData;
        }catch (Exception $e){
            return [];
        }
    }

    /*
     * 模拟curl登陆
     * @param url string url地址
     * @param method sting 请求方式
     * @param param array 请求挈带的参数
     * */
    public static function curl($url,$param=[],$method='POST',$encrypt=0){
        if($encrypt == 1){
            $param = ['data'=>\Yii::$app->openssl->encode(json_encode($param))];
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($method == 'PUT'){
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "put");
        }elseif($method == 'POST'){
            curl_setopt($ch, CURLOPT_POST, 1);
        }
        if($param){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_TIMEOUT,5);
        curl_setopt($ch, CURLOPT_HEADER,0);
        $resultCurl = curl_exec($ch);
        curl_close($ch);
        return $resultCurl;
    }
    /*
     * 检查调用用户系统结果
     * @param json string 要解析的数据
     * @param type string 类型
     * @param verify_type int 短信类型(获取验证码时才会用到次参数)
     * */
    public static function checkCurlMemberResult($json,$type,$verify_type=0){

        $resultCodeArray = [
            'register' => [
                200 => 'SUCCESS',//注册成功
                -1021 => 'REGISTER_ERROR',//注册失败
                -29 => 'VERIFY_TIME_OUT',//验证码已过期
                -28 => 'VERIFY_ERROR',//手机验证码不正确
                -27 => 'REGISTER_MOBILE_EXIST',//该手机号已被使用
                -26 => 'PASSWORD_FORMAT_ERROR',//密码为6~16位字母数字符号
                -25 => 'USER_NAME_FORMAT_ERROR',//用户名为4~16位大小字母、数字、下划线组合
                -24 => 'MOBILE_IS_EXIST',//用户名已存在
                -23 => 'VERIFY_EMPTY',//验证码为空
                -1022  => 'MOBILE_IS_EXIST', //手机与邮箱不能同时为空
                -22 => 'PASSWORD_EMPTY'//密码为空
            ],
            'login' => [
                407 => 'MOBILE_EMPTY',//用户名不能为空！
                408	=> 'PASSWORD_EMPTY',//密码不能为空！
                -13	=> 'NAME_PASSWORD_ERROR',//用户名不存在或密码不正确!
                -400 => 'NAME_PASSWORD_ERROR',//用户名不存在或密码不正确!
                10	=> 'SUCCESS',//登录成功
            ],
            'password' => [
                400	=> 'PASSWORD_UPDATE_NO_MESSAGE',//未接收到消息!
                -23	=> 'VERIFY_EMPTY',//验证码为空
                -28	=> 'VERIFY_ERROR',//手机验证码不正确
                -29	=> 'VERIFY_TIME_OUT',//验证码已过期
                200	=> 'SUCCESS',//登录密码修改成功
                -400 => 'PASSWORD_UPDATE_ERROR',//登录密码修改失败
                -455=> 'NEW_PASSWORD_SAME',
                -30=> 'REGISTER_MOBILE_EXIST',//	账号没有改变！
                -31=> 'REGISTER_MOBILE_EXIST',//	该账号已注册！
                -401=> 'PASSWORD_UPDATE_ERROR',//账号修改失败！
                -402=> 'PASSWORD_ERROR',//	密码不正确！
            ],
            'verify' => [
                -131 => 'MOBILE_EMPTY',//手机号为空！
                -132 => 'VERIFY_TYPE_EMPTY',//短信类型为空！
                -133 => 'VERIFY_SEND_ERROR',//短信发送失败
                -135 => 'VERIFY_MOBILE_IS_NOT_EXIST',//手机号未注册,
                -1352=> 'MOBILE_IS_EXIST',//手机号已注册,
                -136 => 'VERIFY_REPEAT_SEND',
                130 => 'SUCCESS',//短信发送成功!
            ],
        ];
        $json = json_decode($json,true);
        if($json['data']){
            $data = Yii::$app->openssl->decode($json['data']);
            $data = json_decode($data,true);
            if(!empty($data['data'])){
                $data['data'] = json_decode($data['data'],true);
                $data = array_merge($data,$data['data']);
                unset($data['data']);
            }
            $json['data'] = $data;
        }
        if($type == 'verify' && $verify_type == 1 && $json['status'] == -135){
            $json['status'] = -1352;
        }
        $resultCodeArray = $resultCodeArray[$type];
        if (isset($resultCodeArray[$json['status']])) {
            $json['status'] = $resultCodeArray[$json['status']];
        } else {
            Yii::error(json_encode($json));
            $json['status'] = 'ERROR';
        }
        return $json;
    }

    /*
     * 将货币面值由分转成元
     * @param price int 钱
     * @param price int(1、分转元 2、元转分)
     * */
    public static function priceConvert($price,$type=1){
        if($type == 2){
            return ceil($price * 100);
        }
        return round($price / 100,2);
    }

    /*
     * 计算某个日期距离现在的天数
     * @param date string 日期
     * */
    public static function diffTimeByToDay($date){
        $diffDay = ceil((strtotime(date('Y-m-d')) - strtotime(date('Y-m-d',strtotime($date)))) / 86400);
        return $diffDay;
    }

    /*
     * 获取随机数
     * */
    public static function randNumber($len,$type=1){
        if($type == 1){
            $randNumber = '1234567890';
        }elseif($type == 2){
            $randNumber = '1234567890qwertyuioplkjhgfdsazxcvbnm';
        }elseif($type == 3){
            $randNumber = '!@#$%^&*()_+<>,.1234567890qwertyuioplkjhgfdsazxcvbnm';
        }elseif($type == 4){
            $randNumber = '1234567890qwertyuioplkjhgfdsazxcvbnmABCDEFGHIJKLMNOPQRSTUVWSYZ';
        }
        $randNumberLen = strlen($randNumber)-1;
        $resultNumber = '';
        for($i=0;$i<$len;$i++){
            $resultNumber .= $randNumber[mt_rand(0,$randNumberLen)];
        }
        return $resultNumber;
    }

    /*
     * 计算店主佣金
     * */
    public static function getKeeperBrokerageToken($price,$month_price=''){
        return md5("http//bjyltfcom{$price}123as{$month_price}d+");
    }

    /*
     * 根据开始和结束时间生成一个时间段
     * @param return_type int 返回数据类型(1、只有key的数据 2、有key和value的数据)
     * */
    public static function generateDateList($start_at,$end_at,$format='Y-m-d',$return_type = 1,$result_data = 0){
        if($start_at == $end_at || $start_at > $end_at){
            if ($result_data == 1) {
                return [date($format,strtotime($start_at))];
            } else {
                return [date($format,strtotime($start_at)) => $result_data];
            }
        }
        $endTimeStamp = strtotime($end_at);
        $startTimeStamp = strtotime($start_at);

        $diffDay = ceil(($endTimeStamp - $startTimeStamp) / 86400);
        if ($return_type == 1) {
            for($i = 0;$i <= $diffDay;$i++){
                $resultDate[] = date($format,strtotime("+{$i} day",$startTimeStamp));
            }
        } else {
            for($i = 0;$i <= $diffDay;$i++){
                $resultDate[date($format,strtotime("+{$i} day",$startTimeStamp))] = $result_data;
            }
        }
        return $resultDate;
    }

    /*
     * 拆分字符串
     * */
    public static function explode($delimiter,$string){
        if(empty($string)){
            return [];
        }
        if(strstr($string,",")){
            return explode($delimiter,$string);
        }else{
            return [$string];
        }
    }
    //功能：计算两个时间戳之间相差的日时分秒
    //$begin_time 开始时间戳
    //$end_time 结束时间戳
    public static function timediff($begin_time, $end_time)
    {
        if($begin_time < $end_time){
            $starttime = $begin_time;
            $endtime = $end_time;
        }else{
            $starttime = $end_time;
            $endtime = $begin_time;
        }
        //计算天数
        $timediff = strtotime($endtime)-strtotime($starttime);
        $days = ceil(intval($timediff)/86400);
        return $days;

    }

    /**
     * @param $date
     * @param bool $year
     * @param bool $word
     * @return bool|string
     */
    public static function judgeDate($date, $year = false, $word = true){
        if(!$date){
            return false;
        }
        $year = date('Y');
        $day = date('Y',strtotime($date));
        $a=date("Y",strtotime($date));
        $b=date("m",strtotime($date));
        if($word){
            return $a.'年'.$b.'月';
        }
        return $a.'-'.$b;
    }
    /*
    * 获取某日期的年月
    */
    public static function getYearMonth($date){
        return date('Y-m', strtotime($date));
    }
    /*
     * 获取日期
     */
    public static function getDate($word = false, $hour = false){
        if(!$word && !$hour){
            return date('Y-m-d');
        }elseif ($word && !$hour){
            return date('Y').'年'.date('m').'月'.date('d').'日';
        }elseif ($word && $hour){
            return date('Y').'年'.date('m').'月'.date('d').'日'.' '.date('H:i:s');
        }elseif (!$word && $hour){
            return date('Y-m-d H:i:s');
        }
    }

    /**将连续的中文字符转化成数组
     * @param $str
     * @return array
     */
    public static function ch2arr($str){
        $length = mb_strlen($str, 'utf-8');
        $array = [];
        for ($i=0; $i<$length; $i++)
            $array[] = mb_substr($str, $i, 1, 'utf-8');
        return $array;
    }
    /**
     * 导出CSV格式文件
     * @param $data
     * @param $title
     * @param $file_name
     */
    public static function Getcsv($data,$title,$file_name)
    {

        header ( 'Content-Type: application/vnd.ms-excel' );
        header ( 'Content-Disposition: attachment;filename='.$file_name );
        header ( 'Cache-Control: max-age=0' );
        $file = fopen('php://output',"a");
        $limit=1000;
        $calc=0;
        foreach ($title as $v){
            $tit[]=iconv('UTF-8', 'GB2312//IGNORE',$v);
        }
        fputcsv($file,$tit);
        foreach ($data as $v){
            $calc++;
            if($limit==$calc){
                ob_flush();
                flush();
                $calc=0;
            }
            foreach ($v as $t){
                /*$t=is_numeric($t)?$t."\t":$t;*/
                $tarr[]=iconv('UTF-8', 'GB2312//IGNORE',$t);
            }
            fputcsv($file,$tarr);
            unset($tarr);
        }
        unset($list);
        fclose($file);
        exit();
    }
}
