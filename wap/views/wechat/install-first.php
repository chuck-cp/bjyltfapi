<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>合作方式之一</title>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" type="text/css" href="/static/css/ylcmwx.css">
</head>
<body class="sy-body">
<div class="mode-of-cooperat">
    <div class="top"><img src="/static/image/wx_hezuo_topbg.png"></div>
    <div class="content">
        <h3>成为【安装LED屏的联系人】</h3>
        <p class="con">成为安装LED屏的联系人，在理发店免费安装LED屏，每安装一家理发店，领红包100元，同时店家另有付费。</p>
        <p class="btn"><a href="<?=\yii\helpers\Url::to(['install-first-view'])?>">了解详情</a></p>
    </div>
</div>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script>
    $(function(){
        function ht(){
            var winh=$(window).height();
            var toph=$('.top').height();
            var seth=winh-toph;
            $('.mode-of-cooperat .content').css('height',seth);
        }ht()
    })
</script>
</body>
</html>
