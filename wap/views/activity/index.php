<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>玉龙传媒</title>
    <link rel="stylesheet" type="text/css" href="static/css/mreset.css"/>
		<link rel="stylesheet" type="text/css" href="static/css/reset.css?v=20180804">
		<link rel="stylesheet" type="text/css" href="static/css/swiper-4.3.5.min.css"/>
    <script src="static/js/cookie.js"></script>
    <style type="text/css">
        img{display: block;}
        .top_nav{text-align: center; overflow: hidden;     margin-top: 3%;    height: 21px;}
        .top_nav img{display: inline-block;}
        .top_banner_box{position: relative;}
        .top_banner{padding-left: 3%;padding-right: 3%;width: 94%;position: absolute;top: 0;}
        .state{color: #fff;}
        .tel_tt{color: #ffd559;}
        .radio_broadcast{margin-top: 4%; margin-left: 12%;}
        .radio_broadcast span{color: #ffd559;}
        .radio_broadcast b{color: #fff;font-weight: normal;}
        .yl_tt{color: #fff;font-size: 16px;}
        .activity_rule{width: 25%; float: right; margin-top: 9.5%; margin-right: 10.5%;}
        .account_state{float: right;margin-top: 56%;}
        .content_area{padding-left: 3%;padding-right: 3%;}
        .commission_area{margin-top: 5%;position: relative;}
        .commission_area_post{position: absolute;top: 0;width: 100%;}
        .commission_total{text-align: center; margin-top: 18%;}
        .commission_total_con{font-size: 40px;color: #ed4b4e;vertical-align: sub;font-weight: normal;}
        .commission_btn{width: 46%; margin: 0 auto;margin-top: 15%;}
        .we_advantages{margin-top: 4%;margin-bottom: 4%;}
        .login_show{background: rgba(0,0,0,.6);height: 100%;width: 100%;position: fixed;top: 0;z-index:9;display: none;}
        .login_layout{width: 75%;margin: 0 auto;margin-top: 14%;position: relative;background: #fff;border-radius: 10px;}
        .mobi_tts{width: 67%; position: absolute; top: -2%; left: 17%;}
        .tel_no{padding-left: 10%;position: relative;}
        .tel_input{border-bottom: 1px solid #BBBBBB;width: 88%;padding-bottom: 8px;}
        .yanzhenma{display: inline-block; border: 1px solid #ff7d09; color: #ff7d09; border-radius: 5px; padding: 2px; position: absolute; top: -5px; right: 10%; font-size: 12px;background: #fff;}
        .yanzhems{padding-left: 10%;margin-top: 8%; margin-bottom: 13%;}
        .yanzhen_input{border-bottom: 1px solid #BBBBBB;width: 88%;padding-bottom: 8px;}
        .login_confirm{font-size: 16px;color: #fff;background: #f25607; border: 0;width: 100%;padding-top: 9px; padding-bottom: 9px;border-radius: 0 0 10px 10px;}
        .close_btn{width: 9%;margin: 0 auto;    margin-top: 2%;}
				.sy-installed-ts{top: 25%;}
				.commission_award{color: #fff;  position: absolute;  bottom: 10%;  width: 86%; margin: 0 auto; font-size: 12px; left: 50%; margin-left: -43%;}
				/*滚动奖励金*/
				.swiper-container{height: 5vh;}
				/* 苹果登录框 */
				.tel_input,.yanzhen_input{line-height: 1.5;border-radius:0 ;}
    </style>
</head>
<body style="background: #df2d30;">
<img src="static/images/bg.jpg" style="visibility:hidden;height:0;width:0;" />
<div class="top_banner_box">
    <img src="static/images/shop_banner.png?z=20190520" width="100%">
    <div class="top_banner" >
        <div class="top_nav" >
            <!-- <img src="static/images/left_arrow.png" style="width: 3%;float: left;">
            <span class="yl_tt">玉龙传媒</span>
            <img src="static/images/share_btn.png" style="width: 5%;float: right;"> -->
        </div>
        <div class="activity_rule" >
            <a href="javascript:" id="activityRule"><img src="static/images/activity_rules.png" width="100%"></a>
        </div>
        <div class="" style="clear: both;"></div>
        <div class="account_state" >
            <a class="state login" >【未登录】</a>
        </div>
    </div>
		<div class="commission_award" >
			每成功联系一家理发店安装玉龙传媒LED屏幕，即可获得奖励金<span id="configPrice"> </span>元，分享给您的好友一起赚奖励金！
		</div>
</div>
<div class="content_area" >
    <a href="javascript:" id="createShop"><img src="static/images/commission_btn.png" width="100%"></a>
    <div class="commission_area" >
        <img src="static/images/commission_ranking.png" width="100%">
        <div class="commission_area_post" >
            <!-- 奖励金轮播S -->
            <div class="swiper-container">
            	<ul class="swiper-wrapper radio_broadcast" >
            	</ul>
            </div>
            <!-- 奖励金轮播E -->
            <div class="commission_total">
                <b style="color: #ed4b4e;">￥</b> <b class="commission_total_con" >0</b>
            </div>
            <div class="" >
                <a href="javascript:" id="shopDetail"><img class="commission_btn"  src="static/images/reward_gold.png" ></a>
            </div>
        </div>
    </div>
    <img class="we_advantages" src="static/images/we_advantage.png" width="100%">
    <img class="activity_step" src="static/images/activity_steps.png" width="100%">
    <br>
</div>
<!-- 账户登录 -->
<form id="idform">
<div class="login_show" >
    <div class="login_layout" >
        <img class="mobi_tts" src="static/images/mobi_tt.png?z=20181102"  >
        <div class="" style="padding-top: 22%;">
            <p class="tel_no" >
                <input   autocomplete="off" class="tel_input" type="tel" maxlength="11" name="mobile" id="mobile" value="" placeholder="请输入您的手机号" nullmsg="numbphone" datatype="phone" errormsg="errorphone" />
                <input type="button" value="发送验证码" class="yanzhenma" onclick="settime(this)">
            </p>
            <p class="yanzhems" >
                <input class="yanzhen_input" type="tel" maxlength="6" name="verify" id="verify" value="" autocomplete="off" placeholder="请输入您的验证码" nullmsg="numbyzm"
 datatype="*" errormsg="numbcorrect" />
            </p>
            <p style="text-align: center; ">
                <button class="login_confirm" type="submit" style="">登录</button>
            </p>
        </div>
    </div>
    <div class="" >
        <img class="close_btn" id="close" src="static/images/win_close.png" >
    </div>
</div>
</form>
<!--提交失败提示-->
<p class="sy-installed-ts">提交失败</p>
<script src="static/js/jquery-1.7.2.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="/static/js/jQuery.validate.min.js"></script>
<script src="static/js/swiper-4.3.5.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";		
    // 获取系统消息
    $.ajax({
        url:baseApiUrl+'/activity/notice',
        type:'GET',
        async:true,
        success:function (data) {
            if (data.status == 200) {
                var resultData = data.data;
                //console.log(resultData.configPrice);
                $('#configPrice').html(resultData.configPrice);
                $.each(resultData.noticeList,function (index,item) {
                    $('.radio_broadcast').append('<li class="swiper-slide"><span>'+item.mobile+'</span> <b>获得奖励金'+item.price+'元</b></li>');
                });
                swiper_bonus();
            }
        },error:function (data) {
            $('.sy-installed-ts').text('获取系统消息失败');
            tippanel();
        }
    });
    //发送验证码倒计时
    var countdown=60;
    function settime(val){
        if (countdown == 0) {
                val.removeAttribute("disabled");
                val.value = "再次获取验证码";
                countdown = 60;
                return false;
        } else {
            if(countdown == 60){
                var reg = /^1[0-9]{10}$/;
                var mobile = $('#mobile').val();
                var re = new RegExp(reg);
                if (!re.test(mobile)) {
                    $('.sy-installed-ts').text('请输入正确的手机号');
                    tippanel();
                    return true;
                }
                if(mobile == ''){
                    $('.sy-installed-ts').text('申请人手机号不能为空');
                    tippanel();
                    return false;
                }
                var token = getCookie('token');
                if (token == '') {
                    token = getCookie('wechatToken');
                }
                $.ajax({
                    type: "GET",
                    url:baseApiUrl+'/system/verify?type=3&token='+token+'&mobile='+mobile+'&wechat_id='+getCookie('wechatId'),
                    success:function(data){
                        if(data.status == 200){
                            $('.sy-installed-ts').text('发送成功');
                            tippanel();
                        }else{
                            $('.sy-installed-ts').text('发送验证码失败');
                            tippanel();
                        }
                        return false;
                    }
                });
            }
            val.setAttribute("disabled", "disabled");
            val.value = "("+countdown+")秒后重新获取";
            countdown--;
        }
        setTimeout(function() { settime(val) },1000)
    }
    //表单验证
     function tippanel(){
        $('.sy-installed-ts').show();
        setTimeout(function(){$('.sy-installed-ts').hide()},2000);
    }
		            
    $("#idform").Validform({
        tipSweep:true,
        tiptype:function(msg,o,cssctl){
            if(msg=='numbphone'){
                $('.sy-installed-ts').text('手机号不能为空');
                tippanel();
            };
            if(msg=='errorphone'){
                $('.sy-installed-ts').text('请输入正确的手机号');
                tippanel();
            };
            if(msg=='numbyzm'){
                $('.sy-installed-ts').text('验证码不能为空');
                tippanel();
            };
        },
        beforeSubmit:function(curform){
            var verify = $('#verify').val();
            var mobile = $('#mobile').val();
            $.ajax({
                url:baseApiUrl+'/activity/login',
                type:'POST',
                data:{verify:verify,member_mobile:mobile},
                async:true,
                success:function (data) {
                    if (data.status == 200) {
                        var member = data.data;
                        setCookie('activity_token',member['activity_token'],108720)
                        $('.commission_total_con').html(member.price);
                        $('.account_state').html('<span class="tel_tt">'+member.member_mobile+'</span><a class="state out" href="/activity/logout">【退出】</a>');
                        $('body').css({ "overflow-x":"auto", "overflow-y":"auto" });//启用页面滚动
                        $('.login_show').hide()
                    } else {
                        alert(data.message);
                    }
                },error:function (data) {
                    $('.sy-installed-ts').text('服务器错误');
                    tippanel();
                }
            });
            return false;
        },
        datatype:{
            "IDcard":/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[0-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/i,
            "phone":/^1[34578]\d{9}$/,
            'j_cont':/^[1-9]$|^[1-9]\d$|^[1-9]\d{2}$|^[1-9]\d{3}$/
        }
    })
    // 登录显示/隐藏
    $('.login').click(function () {
        $('body').css({ "overflow-x":"hidden", "overflow-y":"hidden" });//禁止页面滚动
        $('.login_show').show();
    });
    $('#close').click(function () {
        $('body').css({ "overflow-x":"auto", "overflow-y":"auto" });//启用页面滚动
        $('.login_show').hide()
    });
		

    if (getCookie('activity_token') != "") {
       $.ajax({
           url:baseApiUrl+'/activity/member?activity_token='+getCookie('activity_token'),
           type:'GET',
           async:true,
           success:function (member_data) {
               if (member_data.status == 200) {
                   var member = member_data.data;
                   $('.commission_total_con').html(member.price);
                   $('.account_state').html('<span class="tel_tt">'+member.member_mobile+'</span><a class="state out" href="/activity/logout">【退出】</a>');
               }
           },error:function (data) {
               $('.sy-installed-ts').text('服务器错误');
               tippanel();
           }
       });
    }

    // 活动规则
    $('#activityRule').click(function () {
        window.location.href = '/activity/rule?token='+getCookie('token');
    })
    // 收益明细
    $('#shopDetail').click(function () {
        if (getCookie('activity_token') == '') {
            $('body').css({ "overflow-x":"hidden", "overflow-y":"hidden" });//禁止页面滚动
            $('.login_show').show();
        } else {
            window.location.href = '/activity/detail';
        }
    })
    // 创建店铺
    $('#createShop').click(function () {
        if (getCookie('activity_token') == '') {
            $('body').css({ "overflow-x":"hidden", "overflow-y":"hidden" });//禁止页面滚动
            $('.login_show').show();
        } else {
            window.location.href = '/activity/shop';
        }
    })
		//奖励金滚动播放
		function swiper_bonus() {		
			var swiperL1 = new Swiper('.swiper-container', {				
					autoplay : {
						delay: 3000,
                        stopOnLastSlide: false,
                        disableOnInteraction: false  //用户操作swiper之后，是否禁止autoplay
					},
					direction: 'vertical'
				});
		}
		
</script>
</body>
</html>