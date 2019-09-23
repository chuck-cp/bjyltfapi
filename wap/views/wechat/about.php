<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>关于我们</title>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" type="text/css" href="/static/css/ylcmwx.css">
</head>
<body class="sy-body">
<div class="about-us">
    <div class=" clearfix">
        <span class="title">北京玉龙腾飞影视传媒有限公司</span>
    </div>
    <div class="content">
        <p>玉龙传媒即北京玉龙腾飞影视传媒有限公司，是主要从事各类公共场所LED屏深度建设和拓展的影视广告媒体公司。</p>
        <p>玉龙传媒作为国内唯一一家将LED屏影视广告媒体内容深入到了各大中小城市以及乡镇的传媒公司，大到北京、上海、广州、深圳等一线城市，小到各地乡镇，在理发店、在楼宇、在机场、在车站、在各类公共场所，玉龙传媒将LED影视广告屏广泛地铺设到了各个角落，受众群体十分广泛。</p>
        <span>我们的优势</span>
        <p>玉龙传媒最大的优势就是受众群体广泛，不论大小城市还是乡镇都有玉龙传媒的LED影视广告屏，玉龙传媒抓住了受众群体美容美发、回家、上班、等电梯、出行这些不可避免的日常活动，将LED屏资源精准投放到了理发店、商业中心、写字楼、住宅区、车站、机场等场所。成为国内唯一一家真正全面、广泛的媒体公司。</p>
        <span>我们的愿景</span>
        <p>玉龙传媒致力于提供国际一流标准的LED屏影视广告传播服务，为国内外用户提供优质的媒体内容，成为全球用户终身信赖的媒体资源，并努力使我们的用户在我们的服务中真正获得所期望的经济效益和社会效益。</p>
        <span>我们的目标</span>
        <p>广泛广泛再广泛，深入深入再深入，将LED屏从每一个城市拓展到每一个乡镇，从中国拓展到世界，成为全世界覆盖最广泛的LED屏影视广告媒体企业，为全世界最广泛的用户提供第一流的影视广告服务就是玉龙传媒的目标。</p>
    </div>
</div>

<!--底部-->
<div class="web-bottom">
    <p>了解公司更多详情<br>请下载玉龙传媒app</p>
    <p><a href="javascript:" class="">下载app 赚大钱</a></p>
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
             var url = '<?=$ios?>'
        }else{
            var url ='<?=$android?>'
        }
        if(url != ''){
            window.location.href = url
        }
    }

    $(function(){
        $(".web-bottom a").click(function(){
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
