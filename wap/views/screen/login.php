<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>设备安装反馈</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" type="text/css" href="/static/css/reset.css"/>
    <link rel="stylesheet" type="text/css" href="/static/css/logo_install.css"/>
</head>
<body>
<div>
    <div class="yx_logo">
        <p>
            <img src="/static/image/logo.png">
            <span>玉龙传媒</span>
        </p>
    </div>
    <div class="yx_denglu">
        <form id="idform">
            <dl>
                <dt>订单号</dt>
                <dd><input class="yx_srnr" type="text" id="apply" placeholder="请输入订单号" datatype="*" sucmsg="inputcorrectly" nullmsg="ordernumber" autocomplete="off" ></dd>
            </dl>

            <dl>
                <dt>动态码</dt><!--Dynamic code-->
                <dd><input class="yx_srnr" type="text" id="dynamic" placeholder="请输入动态码" datatype="*" sucmsg="inputcorrectly" nullmsg="dynamiccode" autocomplete="off"></dd>
                <span class="sy_addtip">订单号或动态码错误</span>
            </dl>
            <p class="yx_djdl"><button type="submit" class="yx_submit">查　询</button></p>
        </form>
    </div>
</div>
<!--完成安装提示-->
<p class="sy-installed-ts">提交成功</p>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script type="text/javascript" src="/static/js/jQuery.validate.min.js" ></script>
<script>
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    //验证信息
    function tippanel(){
        $('.sy-installed-ts').show();
        setTimeout(function(){$('.sy-installed-ts').hide()},2000);
    }
    $("#idform").Validform({
//	  tiptype:3,
        tiptype:function(msg,o,cssctl){
            if(msg=='ordernumber'){
                $('.sy-installed-ts').text('订单号不能为空');
                tippanel();
            };
            if(msg=='dynamiccode'){
                $('.sy-installed-ts').text('动态码不能为空');
                tippanel();

            };
        },
        beforeSubmit:function(curform){
            var apply=$("#apply").val();
            var dynamic=$("#dynamic").val();
            var dev = "<?=$dev?>";
            var token = "<?=$token?>";
            $.ajax({
                url:baseApiUrl+"/screeninstall/existence",
                type:"get",
                dataType:"json",
                data:{apply_code:apply,dynamic_code:dynamic,token:token},
                success:function(data) {
                    if(data.status==200&&data.data!=null){
                        // if(data.data.status==2){
                             location.href="/screen/check?number="+data.data.id+"&apply_code="+apply+"&dynamic_code="+dynamic+"&dev=<?=$dev?>&token="+token;
//                         }else{
//                             location.href="/screen/wait?number="+data.data.id+"&apply_code="+apply+"&dynamic_code="+dynamic+"&token="+token;
//                         }

                    }else{
                        $('.sy-installed-ts').text(data.message);
                        tippanel();
                    }
                }
            });
            return false;
        },
        datatype:{
            "phone":/^0?(13[0-9]|15[012356789]|17[013678]|18[0-9]|14[57])[0-9]{8}$/
        }

    })
</script>
</body>
</html>
