<?php
namespace common\libs;
class PublicClass
{
    /*
     * 验证手机号
     * */
    public static function checkMobileFormat($mobile)
    {
        $regex1 = '/^[5|6|8|9]\\d{7}$/';
        $regex2 = '/^(1)[3-9]\\d{9}$/';
        return preg_match($regex1, $mobile) || preg_match($regex2, $mobile);

    }
    /*
     * 验证验证码
     * */
    public static function authVerify($params){
        $resultCurl = ToolsClass::curl(\Yii::$app->params['memberServerUrl']."/Verificationcode/rest",[
            "mobile"=>$params[0],
            "code"=>$params[1]
        ],'POST',1);
        $resultCurl = json_decode($resultCurl,true);
        return $resultCurl['status'] == 600;
    }
}