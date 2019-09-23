<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>合作方式之三</title>
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
    <div class="top"><img src="/static/image/wx_hezuo_topbg_3.jpg"></div>
    <div class="content">
        <h3>介绍【业务合作人】</h3>
        <p class="con">介绍业务合作人，可轻松赚取长期稳定的额外佣金，佣金是所介绍的业务合作人所拿直接佣金的10%。</p>
        <p class="btn"><a href="<?=\yii\helpers\Url::to(['install-third-view'])?>">了解详情</a></p>
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
