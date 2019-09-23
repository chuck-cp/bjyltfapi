<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\captcha\Captcha;
?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<title>玉龙传媒</title>
		<style type="text/css">
			*{padding: 0;margin: 0;font-size: 12px;font-family: "microsoft yahei";color: #323232;}
			.bodys{background: url(/static/image/login/ylcm_bg.jpg) no-repeat;}
			.layout_center{width: 1200px;margin: 0 auto;}
			.copy_info{text-align: center;}
			.copy_info p{color: #fff;font-size: 18px;}
			.login_img{float: left;margin-top: 120px;margin-left: 26px;}
			.login_con{float: right;margin-right: 26px;width: 360px;height: 320px;background:url(/static/image/login/login_shadow.png) left top;padding-top: 40px;padding-left: 40px;margin-top: 66px;}
			.login_box{height: 500px;background: url(/static/image/login/banner_shadow.png) no-repeat;margin-bottom: 180px;min-width: 1200px;}
			.login_tt{font-size: 24px; }
			.login_in input[type='text']{width: 270px;height: 50px;line-height: 50px;font-size: 16px;border: 0;padding-left: 50px;}
			.login_in input[type='password']{width: 270px;height: 50px;line-height: 50px;font-size: 16px;border: 0;padding-left: 50px;}
			.login_in input:focus{outline: none;}
			.login_btn{width: 320px;height: 50px;border: 0;color: #FFFFFF;font-size: 20px;background: -webkit-linear-gradient(left, #ff7d09 , #db5403); /* Safari 5.1 - 6.0 */ background: -o-linear-gradient(right, #ff7d09, #db5403); /* Opera 11.1 - 12.0 */ background: -moz-linear-gradient(right, #ff7d09, #db5403); /* Firefox 3.6 - 15 */ background: linear-gradient(to right, #ff7d09 , #db5403); /* 标准的语法 */;cursor: pointer;border-radius: 10px;background: #FF7D09\9;}
			.login_btn:focus{outline: none;}
			.login_in{margin-bottom: 20px;}
			.login_btn_box{margin-top: 30px;}
			.val_date,.login_validate  img{vertical-align: middle;}			
			.val_box{ background: #fff0f0; border: 1px solid #ff3c3c;width: 318px;    padding-bottom: 1px;margin-top: 4px; margin-bottom: 4px;}
			.login_in1{background: url(/static/image/login/user.png) #FFFFFF no-repeat 14px center ;}
			.login_in2{background: url(/static/image/login/password.png) #FFFFFF no-repeat 14px center;}
			.wrong_img{margin-left: 14px;margin-right: 16px;}
			.logo_box{padding-top: 60px;padding-bottom: 134px;}
			.login_wrap{overflow: hidden;}
			/* 验证 */
			.login_validate{visibility: hidden;}
		</style>
	</head>
	<body class="bodys">		
		<div class="layout_center logo_box">
			<img src="/static/image/login/logo.png" >
		</div>
		<div class="login_box">
			<div class="layout_center login_wrap" >
				<div class="login_img">
					<img src="/static/image/login/banner_tt.png" >
				</div>
				<!--<form id="form_login">-->
                <?php $form = ActiveForm::begin([ 'id' => 'form_login']); ?>
					<div class="login_con">					
						<p class="login_tt">用户登录</p>
						<div class="login_validate">
							<p class="val_box"><img class="wrong_img" src="/static/image/login/wrong.png" ><label class="val_date"></label></p>
						</div>
						<p class="login_in">
                            <label>
                                <!--<input class="login_in1" type="text" name="" id="mobi"  value="" autocomplete="off" placeholder="请输入手机号" />-->
                                <?php echo Html::activeTextInput($model, 'user_name',['class'=>'login_in1','placeholder'=>'请输入手机号','id'=>'mobi'])?>
                            </label>
                        </p>
						<p class="login_in">
                            <label>
                                <!--<input class="login_in2" type="text" name="" id="password"  value="" autocomplete="off" placeholder="请输入密码" />-->
                                <?php echo Html::activeTextInput($model, 'password',['class'=>'login_in2','type'=>'password','placeholder'=>'请输入登录密码','id'=>'password'])?>
                            </label>
                        </p>
						<p class="login_btn_box">
                            <button class="login_btn" type="submit">登录</button>
                        </p>
					</div>
                <?php ActiveForm::end(); ?>
			</div>
		</div>
		<div class="layout_center copy_info">
			<p>北京玉龙腾飞影视传媒有限公司&nbsp;&nbsp;电话：400-073-6688 </p>
			<p>Copyright©bjyltf.com版权所有&nbsp;&nbsp;京ICP备17074408号&nbsp;&nbsp;隐私声明</p>
		</div>
		<br><br>
		<!--<script src="js/jquery1.7.js" type="text/javascript" charset="utf-8"></script>
		<script src="js/jQuery.validate.min.js"></script>-->
        <script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
		<script type="text/javascript">
			$('.login_btn').click(function () {
				var user_name = $('#mobi').val();
				var user_pw = $('#password').val();
				if(user_name && user_pw){
					$('.login_validate').css('visibility','hidden');					
				}else if($.trim(user_name) == ''){
					$('.val_date').text('手机号不能为空');
					$('.login_validate').css('visibility','visible');
				}else if($.trim(user_pw) == ''){
					$('.val_date').text('密码不能为空');
					$('.login_validate').css('visibility','visible');
				}
			})
		</script>
	</body>
</html>