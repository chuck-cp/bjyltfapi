<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>设备安装反馈</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <style>
        /*标签初始化*/
        /*标签初始化*/
        * {margin: 0;padding: 0 }
        table {border-collapse: collapse;border-spacing: 0}
        h1,h2,h3,h4,h5,h6 {font-size: 100%; font-weight: normal;}
        ul,ol,li {list-style: none}
        em,i {font-style: normal}
        img {border: 0;display:inline-block}
        input,img {vertical-align: middle; border:none; }
        a {color: #333;text-decoration: none;-webkit-tap-highlight-color:transparent;}
        input,button,textarea{-webkit-tap-highlight-color:transparent;outline: none;  }
        article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {display: block;}
        body{-webkit-overflow-scrolling: touch; color: #333; font-size: 14px;min-width: 300PX;max-width: 640PX; margin: 0 auto; font-family: '微软雅黑';}
        .install-wrapper{ padding: 50px 4% 0;}
        .install-box .title{ text-align: center; font-size: 28px; color: #31ae60; padding: 10px 0;}
        .install-box .con{ font-size: 18px; text-align: center; line-height: 30px; color: #999;}
        .install-box .time{ width:130px; margin: 20px auto; position: relative;border-radius:100%; overflow: hidden;}
        .install-box .time .space{ width: 130px; height: 130px;}
        #my_html{ position: absolute; left: 0; top: 0;;}
        .install-box .time p{ width: 100%; text-align: center; line-height: 130px; position: absolute; left: 0; top: 0;color: #ed8202; font-size: 24px; font-weight: bold;}
        .install-box a{ width: 65%; border: none; text-align: center; line-height: 45px;font-size: 16px;  height: 45px; /*background: #ed8102;*/color: #fff; letter-spacing: 5px; display:block; margin: 40px auto 0;}
        .grey{ /*background: #a7a1a1*/ !important;}
    </style>
</head>
<body>
<div class="install-wrapper">
    <!--安装信息提交-->
    <div class="install-box">
        <p class="title">安装信息提交成功</p>
        <p class="con">
            您的安装信息已提交成功，请耐心等待<br>
            此过程大约需要5分钟
            <em style="color: #ed8102;">等待过程中请勿离开此页面</em>
        </p>
        <div class="time">
            <img class="space" src="/static/image/space.jpg">
            <canvas id="my_html" width="130" height="130"></canvas>
            <p class="writetime"><span class="min">5</span>:<span class="second">00</span></p>
        </div>
        <a class="btn" href="javascript:;">刷新</a>
    </div>
</div>
<script src="/static/js/jquery.js"></script>
<script src="/static/js/drawcircle.js"></script>
<script>
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    /*设置时间间隔倒计时*/
    var timeinterval=300;
    /*请求间隔时间*/
    var  jiange=1;
    /*获取倒计时时间终点*/
    var timestamp = Date.parse(new Date())/1000+timeinterval;
    $.ajax({
        url:baseApiUrl+"/screeninstall/activation",
        type:"get",
        dataType:"json",
        data:{id:<?=$id?>,apply_code:'<?=$apply_code?>',dynamic_code:'<?=$dynamic_code?>'},
        success:function(data) {
            if(data.status==200){
                $('.writetime .second').html('00');
                $('.writetime .min').html('0');
                $('.install-box .btn').css('background','#ed8102');
                //倒计时完成后可点击
                $('.btn').click(function(){
                    $.ajax({
                        url:baseApiUrl+"/screeninstall/activation",
                        type:"get",
                        dataType:"json",
                        data:{id:<?=$id?>,apply_code:'<?=$apply_code?>',dynamic_code:'<?=$dynamic_code?>'},
                        success:function(data) {
                            if(data.status==200){
                                location.href="/screen/over?number=<?=$id?>&apply_code=<?=$apply_code?>&dynamic_code=<?=$dynamic_code?>&istrue=1&token=<?=$token?>";
                            }else{
                                location.href="/screen/over?number=<?=$id?>&apply_code=<?=$apply_code?>&dynamic_code=<?=$dynamic_code?>&istrue=0&token=<?=$token?>";
                            }
                        }
                    });
                })
            }else{
                settime()
                /*时间进度条*/
                //参数分别是 canvas元素id 绘制角度 变宽宽度 底圆颜色 动态圆颜色 文本颜色 字体大小 圆半径 倒计时终点值  倒计时时间时间长度
                var p1=drawcircle('my_html',360,6,'#e8e8e8','#ed8202','#fff',30,55,timestamp,timeinterval);

            }
        }
    });

    function settime(){
        //实时获取时间
        var  realtime=Date.parse(new Date())/1000;
        var  countdown=timestamp -realtime;
        if (countdown <= 0) {
            $('.writetime .min').html('0');
            $('.writetime .second').html('00');
            $('.install-box .btn').css('background','#ed8102');
            //倒计时完成后可点击
            $('.btn').click(function(){
                $.ajax({
                    url:baseApiUrl+"/screeninstall/activation",
                    type:"get",
                    dataType:"json",
                    data:{id:<?=$id?>,apply_code:'<?=$apply_code?>',dynamic_code:'<?=$dynamic_code?>'},
                    success:function(data) {
                        if(data.status==200){
                            location.href="/screen/over?number=<?=$id?>&apply_code=<?=$apply_code?>&dynamic_code=<?=$dynamic_code?>&istrue=1&token=<?=$token?>";
                        }else{
                            location.href="/screen/over?number=<?=$id?>&apply_code=<?=$apply_code?>&dynamic_code=<?=$dynamic_code?>&istrue=0&token=<?=$token?>";
                        }
                    }
                });
            })
            return false;
        }
        else{
            var min=parseInt(countdown/60);
            var sec=countdown-min*60;
            $('.writetime .min').html(min);
            if(sec<10){
                $('.writetime .second').html('0'+sec);
            }
            else{
                $('.writetime .second').html(sec);
            }
            $('.install-box .btn').css('background','#a7a1a1');
            //每秒检测一次  安装成功后直接跳转
            jiange=jiange+1;
            if(jiange>=6){
                jiange=1;
                $.ajax({
                    url:baseApiUrl+"/screeninstall/activation",
                    type:"get",
                    dataType:"json",
                    data:{id:<?=$id?>,apply_code:'<?=$apply_code?>',dynamic_code:'<?=$dynamic_code?>'},
                    success:function(data) {
                        if (data.status == 200) {
                            location.href="/screen/over?number=<?=$id?>&apply_code=<?=$apply_code?>&dynamic_code=<?=$dynamic_code?>&istrue=1&token=<?=$token?>";
                            return false;
                        }
                    }
                });
            }
        }
        setTimeout(function() { settime() },1000)
    }



</script>
</body>
</html>
