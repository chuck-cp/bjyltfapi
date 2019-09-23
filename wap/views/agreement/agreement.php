<html>
<head>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title><?=$title?></title>
    <style>
        /*标签初始化*/
        * {margin: 0;padding: 0 }
        body{ background: #fff; -webkit-overflow-scrolling: touch; color: #333; font-size: 13px;min-width: 300PX;max-width: 640PX; margin: 0 auto; font-family: '微软雅黑';}
        em{ font-style: normal;}
        p{ margin: 0; padding: 0;}
        .protocol{
            padding: 10px 3.75% 0;
            color: #333;
            overflow: hidden;
            background-color: #fff;
        }
        .protocol h1{
            color: #000;
            font-size: 16px;
            font-weight: normal;
            padding: 3px 0;
        }
        .protocol p{
            padding: 5px 0;
            font-size: 14px;
            line-height: 22px;

        }
    </style>
</head>
<body>
<style>
    /*顶部返回首页*/
    .cp-return{ background: #fff; padding: 10px 0; height: 24px;border-bottom: 1px solid #DDD8CE;}
    .cp-return .icon{ display: inline-block; position: absolute; left: 5px; width: 20px; height: 20px;}
    .cp-return .icon img{ width: 24px; height: 24px;}
    .cp-return h3{ margin: 0 30px; line-height: 24px; text-align: center; font-size: 16px;}
</style>
<!--返回导航条-->
<div class="cp-return">
    <a href="javascript:history.go(-1);" class="icon"><img src="/static/image/fanhui.png"></a>
</div>
<?=$content?>
</body>
</html>