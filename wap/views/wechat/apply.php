<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>LED屏申请条件</title>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" type="text/css" href="/static/css/ylcmwx.css">
</head>
<body class="sy-body">
<div class="sq-install">
    <div class="top"><img src="/static/image/wx-led-sq.jpg"></div>
    <div class="content">
        <p>店铺要求：<br>
            1、店内支持wifi无线网络<br>
            2、店铺具有合法经营许可证<br>
            3、同意<a href="<?=\yii\helpers\Url::to(["agreement/install_agreement"])?>" style="color: #1a6cb6;">《视频播放设备安装协议》</a>相关内容</p>
    </div>
    <p class="btn"><a href="<?=\yii\helpers\Url::to(['shop/create','wechat_id'=>$wechat_id,'token'=>$token])?>">立即申请</a></p>
</div>
</body>
</html>
