<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>合作详情三</title>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" type="text/css" href="/static/css/ylcmwx.css">
</head>
<body class="sy-body">
<div class="detail-cooperate-one">
    <div class="top"><img src="/static/image/3_00.png"></div>
    <div class="content">
        <h3>【介绍业务合作人】详情介绍</h3>
        <p class="title">1.【介绍业务合作人】的条件：</p>
        <p>任何人都有介绍业务合作人的权利，你所介绍的业务合作人将记录在案，但只有当你自己联系安装6家以上（含）理发店的LED屏，也就是你已经成为玉龙传媒业务合作人后，才能享受介绍业务合作人的相应佣金。</p>
        <p class="title">2. 【介绍业务合作人】的好处：</p>
        <p>玉龙传媒将额外拿出一部分佣金奖励介绍业务合作人者，介绍业务合作人者将可轻松赚取长期稳定的额外佣金，其佣金是你所介绍的业务合作人所拿直接佣金的10%，以上是税后净得的佣金，玉龙传媒已经为你承担了相应的税金。</p>
        <p>需要说明的是你所介绍的业务合作人也可能再介绍业务合作人，他（她）也可享受介绍业务合作人应拿的额外佣金，他（她）的这部分佣金不计算在你的10%的佣金基数中，即你所拿的10%的额外佣金的计算基数只是你介绍的业务合作人自己直接承揽业务所拿的佣金，玉龙传媒没有设立多级享受业务提成的政策。</p>
        <p class="title">3.查看介绍的业务合作人：</p>
        <p>在玉龙传媒APP内，进入工作模块，点击“伙伴列表”按钮，可查看自己所介绍的业务合作人详细信息。</p>
        <p class="title" style="padding: 10px 0;"><img src="/static/image/3_2.png" width="100%"></p>

        <p class="title">4、说明</p>
        <p>玉龙传媒更详细的介绍业务合作人的合作政策见<a href="<?=\yii\helpers\Url::to(["agreement/concurrent_post_agreement"])?>">《玉龙传媒业务合作政策》</a>（在玉龙传媒APP的“业务合作政策”模块），以上内容与<a href="<?=\yii\helpers\Url::to(["agreement/concurrent_post_agreement"])?>">《玉龙传媒业务合作政策》</a>有矛盾之处以<a href="<?=\yii\helpers\Url::to(["agreement/concurrent_post_agreement"])?>">《玉龙传媒业务合作政策》</a>为准。玉龙传媒合作政策的解释权在北京玉龙腾飞影视传媒有限公司。</p>
    </div>
    <div class="down-app"><a href="javascript:">下载APP</a></div>
</div>
<div class="mask"></div>
<div class="ti_img"><img src="/static/image/wx_downloada.png"></div>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script>
    var is_weixin = (function(){return navigator.userAgent.toLowerCase().indexOf('micromessenger') !== -1})();
    if (!is_weixin) {
        var ua = navigator.userAgent.toLowerCase();
        //ios下载地址
        if (/iphone|ipad|ipod/.test(ua)) {
            window.location.href ='<?=$ios?>'
        }else{
            window.location.href ='<?=$android?>'
        }

    }
    var  ios='<?=$ios?>';
    var  android='<?=$android?>';
    var ua = navigator.userAgent.toLowerCase();
    //ios下载地址
    if (/iphone|ipad|ipod/.test(ua)) {
        if(ios==''){
            $(".down-app").hide();
        }
    }else{
        if(android==''){
            $(".down-app").hide();
        }
    }
    $(function(){
        $(".down-app a").click(function(){
            var ua = navigator.userAgent.toLowerCase();
            //ios下载地址
            if (/iphone|ipad|ipod/.test(ua)) {
                window.location.href ='<?=$ios?>'
            } else{
                var is_weixin = (function(){return navigator.userAgent.toLowerCase().indexOf('micromessenger') !== -1})();
                if (is_weixin) {
                    tis_mask();
                }else{
                    window.location.href ='<?=$android?>'
                }
            }
        })
    })
</script>
<script>
    function tis_mask(){
        $(".mask").css('opacity','0.3').show();
        var ymheight=$(document).height()+ "px";
        $(".mask").css("height",ymheight);
        $('.ti_img').show();
    }
    $('.mask,.ti_img').click(function(){
        $('.ti_img').hide();
        $(".mask").hide();
    })
</script>
</body>
</html>
