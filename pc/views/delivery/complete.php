<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>玉龙传媒</title>
    <link rel="icon" type="image/x-icon" href="/static/images/icon.ico" />
    <link rel="stylesheet" type="text/css" href="/static/css/public.css">
    <link href="//imgcache.qq.com/open/qcloud/video/tcplayer/tcplayer.css " rel="stylesheet">
    <style>
        .sy_sucess_tip{ color: #555f90; font-size: 24px; text-align: center; padding-top: 185px;}
        .sy_success_btn{margin:50px auto 0; display:block;width:230px; text-align: center; line-height: 60px; height:60px;font-size:24px; font-family:"黑体"; color:#fff; border:none;background:url(/static/images/tjcq.png);}}
    </style>
</head>
<body>
<div class="head">
    <div class="wrap">
        <div class="yx_logo">
            <img src="/static/images/logo_name.png">
        </div>
    </div>
</div>
<div class="wrap">
    <!--左侧菜单-->
    <div class="yx_left_menu fl">
        <!--用记信息-->
        <div class="yx_exit">
            <p class="yx_tcdl"><a href="/index/logout">退出登录</a></p>
            <p class="yx_photo"><img src="<?=\Yii::$app->user->identity->avatar;?>"></p>
            <p class="yx_name"><a href="javascript:;"><?=\Yii::$app->user->identity->name;?></a></p>
        </div>
        <!--菜单内容-->
        <ul class="yx_menu_nr">
            <li class="yx_gaoliang"><span><img src="/static/images/tfgl.png"></span><a href="/delivery/index">投放素材</a></li>
            <li><span><img src="/static/images/cqgl.png"></span><a href="/property/index">产权管理</a></li>
            <li><span><img src="/static/images/lsgl.png"></span><a href="/history/delivery">历史管理</a></li>
        </ul>
    </div>
    <!--右侧菜单-->
    <div class="yx_right_menu fr">
        <p class="sy_sucess_tip">您的素材已提交成功，我们的工作人员将在3-5个工作日审核您提交的内容，请您耐心等待。</p>
        <a href="/delivery/index" class="sy_success_btn">确定</a>
    </div>

</div>

<!-- 提示框 -->
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script type="text/javascript" src="/static/js/sc_js.js" ></script>
</body>
</html>
