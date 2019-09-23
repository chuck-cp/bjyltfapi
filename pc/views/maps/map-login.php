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
    <style>
        .yx_dl_Submit{vertical-align: middle}
        .yx_input_bj{float: none;margin-bottom: 26px;width: 346px;margin-left: 14px;    margin-top: 14px;}
        .yx_mc_mima_bj{width: 400px;    height: 300px;}
        .yx_mc_mima{width: 386px;margin-left: 186px;}
        .yx_input_bj span{position: relative; top: 8px;}
        .yx_dl_input{margin-top: 4px}
        .yx_dlerror{text-align: left; padding-left: 60px; position: relative; top: -94px;}
    </style>
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
                    <?php echo Html::activeDropDownList($model, 'user_name',[
                        '13901359098' => '胡总',
                        '13581948044' => '田雪',
                        '13691303382' => '赵亚琼',
                    ],['class' => 'yx_dl_input'])?>
                </div>
                <div class="yx_input_bj" style="position:relative;">
                    <span><img src="/static/images/yh_password.png"></span>
                    <?php echo Html::activeTextInput($model, 'password',['class'=>'yx_dl_input verify','placeholder'=>'请输入验证码'])?><button type="button" class="yx_dl_Submit yanzhengma" style="color: #fff;font-size: 14px;border: 0; text-align: center; width: 100px; height: 34px; background: #ff7d09; border-radius: 5px;position: absolute;right: 10px; top: 14px;">获取验证码</button>
                </div>
                <div style="margin-left: 14px;    margin-top: 33px;">
                    <button type="button" class="yx_dl_Submit tijiao" style="width: 360px; height: 60px; font-size: 24px; background: #ff7d09; color: #fff;    border-radius: 10px;" >登录</button>
                </div>
                <p class="blank5"></p>
                <?php if($login_error = \Yii::$app->session->getFlash('login_error')):?>
                    <div class="yx_dlerror " style="display: block"><?php echo $login_error;?></div>
                <?php endif?>
            </div>
            <span class="yx_mc_mima_bj"></span>
        </div>
        <!--登录 提交 -->
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
<script type="text/javascript" src="/static/js/layer/layer.js"></script>
<!--高度自适应-->
<script type="text/javascript" src="/static/js/sc_js.js"></script>
<script>
    $('.yanzhengma').on('click',function () {
        var mobile = $('#loginform-user_name option:selected').val();
        $.ajax({
            url:'<?=\yii\helpers\Url::to(['ajaxsend'])?>',
            type : 'GET',
            // ContentType: "application/json; charset=utf-8",
            dataType : 'json',
            data : {'mobile':mobile},
            success:function(phpdata){
                console.log(phpdata);
                layer.msg(phpdata.msg,{icon:phpdata.code});
            },
            error:function(){
               layer.msg('操作失败！');
            }
        })
    })
    $('.tijiao').on('click',function () {
        var verify = $('.verify').val();
        if(verify){
            $('#da-login-form').submit();
        }
    })
</script>
</html>