<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>设备安装反馈</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/sy_xgnewfeedback.css"/>
</head>
<body class="sy-body">
<div class="sy_loadingpage" style="display: none;">
    <img src="/static/images/loading.gif">
    <p>正在加载...</p>
</div>
    <?php if($istrue==1){?>
        <!--安装成功提示-----------若安装成功则 -->
        <div class="sy_sc_sucess" >
            <div class="con">
                <img src="/static/images/sy_sc_sucess.png">
                <p>安装成功</p>
            </div>
            <a href="javascript:;"  class="abtn" id="confirm">确定</a>
        </div>
    <?php }else{?>
        <div class="sy_sc_fail">
            <div class="con">
                <img src="/static/images/sy_sc_fail.png">
                <p class="tip">安装失败</p>
                <div class="failcode clearfix">
                    <p class="lf">激活失败屏幕编号:</p>
                    <p class="rh" id="anzy">

                    </p>
                </div>
                <p class="sy_fail_tip">
                    请尝试重新开关机，重连网络等操作<br>
                    点击再次申请激活按键重新激活<br><br>
                    若多次尝试无效可点击更换屏幕<br>
                    更换以上故障屏幕<br><br>
                    点击返回工作页<br>
                    保存当前结果并返回工作页
                </p>
            </div>
            <div class="tjbtn">
                <!--按钮置灰色 set-grey-->
                <a href="/screen/screenwait?shopid=<?=$shopid?>&token=<?=$token?>&replace_id=<?=$replace_id?>"  class="reapplica" >重新申请激活</a>
                <a href="javascript:;"  class="replacefail" id="isupdate">更换故障屏幕</a>
                <a href="javascript:;"  class="abtn" id="confirm">返回工作页</a>
            </div>
        </div>
    <?php }?>
<script src="/static/js/jquery.js"></script>
<script>
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    var istrue = "<?=$istrue?>";
    var gengxinid="";
    var change = "<?=$change?>";
    $(function(){
        if(istrue==0){
            $.ajax({
                url:baseApiUrl+"/screeninstall/screenactivation",
                type:"get",
                dataType:"json",
                data:{id:<?=$shopid?>,token:'<?=$token?>',change:'<?=$change?>'},
                success:function(data) {
                    if(data.status==200){
                        location.href="/screen/screenover?shopid=<?=$shopid?>&token=<?=$token?>&istrue=1&replace_id=<?=$replace_id?>&operate=<?=$operate?>";
                    }else{
                        var  html="";
                        $.each(data.data.shopData,function(i,value){
                            html=html+value.software_number+"<br>";
                            gengxinid=gengxinid+value.id+",";
                        });
                        gengxinid=gengxinid.substring(0,gengxinid.length-1);
                        $("#anzy").html(html);
                    }
                }
            });
        }

    })
//    if(change == 'change'){
        $("#isupdate").click(function(){
            location.href="/screen/change-screen?shopid=<?=$shopid?>&replace_id=<?=$replace_id?>&token=<?=$token?>&isupdate="+gengxinid+"&operate=<?=$operate?>";
        })
//    }else{
//        $("#isupdate").click(function(){
//            location.href="/screen/screenupdate?shopid=<?//=$shopid?>//&token=<?//=$token?>//&isupdate="+gengxinid+"&operate=<?//=$operate?>//";
//        })
//    }

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
//    $("#again").click(function(){
//        alert("aa");
//        location.href="/screen/screenwait?shopid=<?//=$shopid?>//&token=<?//=$token?>//";
//    });
</script>
</body>
</html>
