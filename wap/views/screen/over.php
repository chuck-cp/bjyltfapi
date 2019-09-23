<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>screen设备安装反馈</title>
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
        input,button,textarea{-webkit-tap-highlight-color:transparent;outline: none;   }
        article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {display: block;}
        body{-webkit-overflow-scrolling: touch; color: #333; font-size: 14px;min-width: 300PX;max-width: 640PX; margin: 0 auto; font-family: '微软雅黑';}
        .install-wrapper{ padding: 50px 4% 0;}
        /*安装成功样式*/
        .install-success{}
        .install-success .title{padding:60px 0 20px 0; color: #31ae60; font-size: 30px; text-align: center; }
        .install-success .suc-img{ text-align: center; padding:30px 0;}
        .install-success .suc-img img{ width: 43.13%;}
        .install-success .btn{ display:block;width: 65%; font-size: 16px; border: none; text-align: center; line-height: 45px; height: 45px; background: #ed8102;color: #fff; letter-spacing: 5px; margin: 40px auto 0;}
        /*安装失败样式*/
        /* .install-fail{display: none;}*/
         .install-fail .title{padding:60px 0 20px 0; color: #cf1e1e; font-size: 30px; text-align: center; }
         .install-fail .suc-img{ text-align: center; padding:30px 0;}
         .install-fail .suc-img img{ width: 43.13%;}
         .install-fail .indru{ color: #b6b6b6; font-size: 16px; text-align: center; padding: 10px 0;}
         .install-fail .lxkf{display:block;width: 65%; font-size: 16px; border: none; text-align: center; line-height: 45px; height: 45px; background: #c3c3c3;color: #fff; letter-spacing: 5px; margin: 20px auto 0;}
         .install-fail .reload{display:block;width: 65%; font-size: 16px; border: none; text-align: center; line-height: 45px; height: 45px; background: #e99898;color: #fff; letter-spacing: 5px; margin: 20px auto 0;}
     </style>
</head>
<body>
<div class="install-wrapper">
    <?php if($istrue==1){?>
    <!--安装成功提示-----------若安装成功则 -->
    <div class="install-success">
        <p class="title">安装成功</p>
        <p class="suc-img"><img src="/static/image/install-suc.png"></p>
        <a href="javascript:;" class="btn" id="confirm" >确定</a>
    </div>
    <?php }else{?>
    <!--安装失败提示-->
    <div class="install-fail">
        <p class="title">安装失败</p>
        <p class="suc-img"><img src="/static/image/install-fail.png"></p>
        <p class="indru">安装审核失败<br>若有问题请联系客服</p>
        <a href="javascript:;" id="tel" class="lxkf">联系客服</a>
        <a href="javascript:;" class="reload" id="again">重新申请</a>
    </div>
    <?php }?>
</div>
<script src="/static/js/jquery.js"></script>
<script>
     $("#tel").click(function(){
         var ua = navigator.userAgent.toLowerCase();
         if (/iphone|ipad|ipod/.test(ua)) {
             var result = {"action":"call","number":"4000736688"}
             webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
         }else if(/android/.test(ua)) {
             var result = {"action":"call","number":"4000736688"}
             window.jsObj.HtmlcallJava(JSON.stringify(result));
         }
     })
     $("#confirm").click(function(){
        var result = {"action":"closewebview"};
        var ua = navigator.userAgent.toLowerCase();
        if (/iphone|ipad|ipod/.test(ua)) {
              webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
        }else if(/android/.test(ua)) {
           window.jsObj.HtmlcallJava(JSON.stringify(result));
        }
     });
     $("#again").click(function(){
         location.href="/screen/wait?number=<?=$id?>&apply_code=<?=$apply_code?>&dynamic_code=<?=$dynamic_code?>&token=<?=$token?>";
     });
</script>
</body>
</html>
