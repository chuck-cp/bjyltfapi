<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>玉龙传媒</title>
    <link rel="stylesheet" type="text/css" href="/static/css/public.css">
    <link rel="stylesheet" type="text/css" href="/static/css/login.css">
    <link rel="icon" type="image/x-icon" href="/static/images/icon.ico" />
</head>
<body>
<div class="yx_dl_bj">
<!--登录信息-->
	<div class="yx_dl_xx">
        <?php $form = ActiveForm::begin([ 'id' => 'da-login-form']); ?>
    	<!--登录 logo -->
    	<h2><img src="/static/images/dl_logo.png"></h2>
        <p class="yx_ledp_ad"><img src="/static/images/ledp_ad.png"></p>
        <!--登录 名称  密码 -->
        <div class="yx_mc_mima">
        	<div class="yx_dlxx_mc_mm">
                <div class="yx_input_bj"> 
                    <span><img src="/static/images/yh_name.png"></span>
                    <?php echo Html::activeTextInput($model, 'user_name',['class'=>'yx_dl_input','placeholder'=>'请输入用户名'])?>
                </div>
                <div class="yx_input_bj">
                    <span><img src="/static/images/yh_password.png"></span>
                    <?php echo Html::activeTextInput($model, 'password',['class'=>'yx_dl_input','type'=>'password','placeholder'=>'请输入登录密码'])?>
                </div>
                <p class="blank5"></p>
                <?php if($login_error = \Yii::$app->session->getFlash('login_error')):?>
                    <div class="yx_dlerror " style="display: block"><?php echo $login_error;?></div>
                <?php endif?>

            </div>
            <span class="yx_mc_mima_bj"></span>
        </div>
        <!--登录 提交 -->
         <button type="submit" class="yx_dl_Submit" ><img src="/static/images/dl_lv.png"></button>
        <?php ActiveForm::end(); ?>
    </div>
<!--版权-->
<div class="yx_banquan">
<p>北京玉龙腾飞影视传媒有限公司      电话：<?php echo $service_phone;?></p>
<p>Copyright © bjyltf.com 版权所有   <a href="http://www.miitbeian.gov.cn" target="_blank" style="color: white">京ICP备17074408号</a> <a style="color: white" href="/site/privacy" target="_blank">隐私声明</a></p>
</div>

</div>
</body>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<!--高度自适应-->
<script type="text/javascript" src="/static/js/sc_js.js"></script>
<script>
/*$(function(){
    var cwts=$(".yx_dlerror").html();
    if(cwts=="用户名或密码错误"){
        $(".yx_mc_mima_bj").addClass("yx_mc_mima_bj_error")

    }
})*/
</script>


</html>