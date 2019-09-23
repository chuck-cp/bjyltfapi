<?
use yii\helpers\Html;
use yii\helpers\Url;
?>
<!DOCTYPE html>
<!-- saved from url=(0093)http://wap.bjyltf.com/inner-install/choose?token=OS5Nevjk7WxpSFvE1zNcZ3rdZc6i-0VB&wechat_id=0 -->
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <title>选择申请或者安装</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <link rel="stylesheet" href="/static/css/sy_azywlb.css?v=0925">
</head>
<body class="zhj_body">
<!--<div class="header">-->
<!--    <a href="javascript:history.back(-1)"><img src="/static/image/sy_bngoback.png"></a>-->
<!--    <h3>选择申请或安装</h3>-->
<!--</div>-->
<div class="sy_nbbox">
    <div class="sy_nbselected zhj_nbselected">
        <p class="sy_nbsctlist"><a href="javascript:void(0);"><img class="tp" src="/static/image/more_apply/shop_apply.png"></a></p>
        <p class="sy_nbsctlist"><a href="javascript:void(0);"><img class="tp" src="/static/image/more_apply/building_apply.png"></a></p>
        <p class="sy_nbsctlist"><a href="javascript:void(0);"><img class="tp" src="/static/image/more_apply/park_apply.png"></a></p>
        <?php if($inside):?>
            <p class="sy_nbsctlist"><a href="javascript:void(0);"><img class="tp" src="/static/image/more_apply/waiting_signature.png"></a></p>
        <?php endif;?>
<!--        --><?php //if($inside):?>
<!--            <p class="sy_nbsctlist"><a href="javascript:void(0);"><img class="tp" src="/static/images/zhj_achievement.png"></a></p>-->
<!--        --><?php //endif;?>

    </div>
</div>
<div class="mask"></div>
<div class="sy_nbtkpanel">
    <p class="txt">很抱歉，您没有查看此模块的权限!</p>
    <p class="lik"><a id="goback" href="javascript:void (0);">返回</a></p>
</div>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script>
    $(function(){
        //弹框显示
        function tkxs(){
            $(".mask").css('opacity','0.3').show();
            var ymheight=$(document).height()+ "px";
            $(".mask").css("height",ymheight);
            $('.sy_nbtkpanel').show()
        }
        $("#goback").click(function () {
            var result = {"action":"closewebview"};
            var ua = navigator.userAgent.toLowerCase();
            if (/iphone|ipad|ipod/.test(ua)) {
                webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
            }else if(/android/.test(ua)) {
                window.jsObj.HtmlcallJava(JSON.stringify(result));
            }
        })
        $('.tp').click(function () {
            var __this = $(this);
            var member_id = "<?=Html::encode($member_id)?>";
            var token = "<?=Html::encode($token)?>";
            var rote = "<?=Url::to(['check-inner'])?>"+"?token="+token;
            var dev = "<?=Html::encode($dev)?>";
            $.ajax({
                url:rote,
                type:'POST',
                async:true,
                data:{
                    'member_id':member_id,
                    'token':token,
                },
                dataType:'json',
                success:function (phpdata) {
                    if(phpdata !== 1){
                        tkxs();
                    }else if(phpdata == 1){
                        var num = __this.parents('p').index();
                        if(num == 0){
                            window.location.href = "/shop/choose-shop-type"+'?token='+token+'&dev='+dev;
                        }else if(num == 1){
                            //window.location.href = "/inner-install/index"+'?token='+token+'&dev='+dev; 原安装任务
                            window.location.href = "/build"+'?token='+token+'&dev='+dev;
                        }else if(num == 2){
                            window.location.href = "/park"+'?token='+token+'&dev='+dev;
                        }else if(num == 3){
                            window.location.href = "/inner-install/wait-sign"+'?token='+token+'&dev='+dev;
                        }
                    }
                }
            })
        })
    })
</script>


</body></html>
