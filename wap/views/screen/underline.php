<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>安装信息上传</title>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet" href="/static/css/lsy-install.css" />
    <script src="/static/js/jquery-1.7.2.min.js"></script>
    <style>
        .sy-installed-ts{ width: 100%; height: 30px; line-height: 30px; text-align: center;  position: fixed;
            left: 0; top: 50%; margin-left:0px;margin-top: -15px; font-size: 14px; display: none; background: none;}
        .sy-installed-ts span{background: rgba(0,0,0,0.3); color: #fff;border-radius:5px; font-size: 14px; padding: 5px 10px;}
    </style>
</head>
<body class="sy-body">
<div class="sy-wrapper">
    <div class="sy-unchecked">
        <form id="idform">
            <div class="content">
                <!--填写信息-->
                <div class="sy-fillin-infor">
                    <div class="name">
                        <p>安装人姓名</p>
                        <input nullmsg="nameblank" id="install_name" type="text" datatype="*" placeholder="请填写真实姓名" sucmsg="inputcorrectly" autocomplete="off">
                        <input  id="screenBB" type="hidden" >
                    </div>
                    <div class="name">
                        <p>手机号码</p>
                        <div class="sy-sendyzm">
                            <input  maxlength="11" type="text" id="install_mobile" nullmsg="numbphone" datatype="phone" errormsg="errorphone" placeholder="请填写手机号码" sucmsg="inputcorrectly" autocomplete="off">
                            <input type="button" value="发送验证码"  class="send-yzm"  onclick="settime(this)">
                        </div>
                    </div>
                    <div class="name">
                        <p>验证码</p>
                        <input  maxlength="6" type="text" id="verify" nullmsg="numbyzm" datatype="*" errormsg="numbcorrect" sucmsg="inputcorrectly" placeholder="请填写验证码" autocomplete="off">
                    </div>
                    <!--错误提示语已隐藏  信息错误则显示visibility: visible-->
                    <p class="tip">订单号或动态码错误</p>
                </div>
                <!--请上传安装完成后的LED屏幕照片并填写设备编号-->
                <div class="sy-uploadimg">
                    <p class="tit">请上传安装完成后的LED屏幕照片并填写设备编号</p>
                    <div class="con">
                        <div class="sy-up-list">
                            <div class="upload">
                                <img class="imgspace" src="/static/image/blank.jpg">
                                <img class="addimg" src="/static/image/uploadimg-add.png">
                                <input type="file" class="Upload-imginput">
                            </div>
                            <div class="detail">
                                <p class="number">设备编号</p>
                                <input type="text" >
                                <!--错误提示语已隐藏  信息错误则显示visibility: visible-->
                                <p class="tip">该设备编号已被占用</p>
                            </div>
                        </div>


                    </div>
                </div>
            </div>
            <button class="sy-installed" type="submit">完成安装</button>
        </form>
    </div>
</div>
<!--完成安装提示-->
<p class="sy-installed-ts"><span>提交成功</span></p>
<script type="text/javascript" src="/static/js/jQuery.validate.min.js" ></script>
<script type="text/javascript" src="/static/js/cos-js-sdk-v4.js" ></script>
<script type="text/javascript" src="/static/js/mobile-adapt.js"></script>
<script type="text/javascript" src="/static/js/exif.js?v=1.2"></script>
<script>
    var myFolder = '/install_check/<?//=date('Y-m-d')?>/';
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    var dev = "<?=$dev?>";
    $(function(){
        $.ajax({
            url:baseApiUrl+"/screeninstall/screennumberunline/<?=$shopid?>?token=<?=$token?>",
            type:"get",
            dataType:"json",
            success:function(data) {
                if(data.status==200&&data.data!=null){
                    $("#screenBB").val(JSON.stringify(data.data));
                    var  html="";
                    $.each(data.data,function(i,value){
//                        html=html+"<div class=\"sy-up-list\" > <div class=\"upload\" id=\"screen"+value.id+"\"> <img class=\"imgspace\" src=\"/static/image/blank.jpg\">
// <input class=\"update_input\" id='panorama"+value.id+"' type=\"hidden\" placeholder=\"请上传屏幕安装图\" nullmsg=\"panorama_image\" datatype=\"*\"> <img class=\"addimg\" src=\"/static/image/uploadimg-add.png\"> <input type=\"file\" id=\"up"+i+"\" class=\"Upload-imginput\" accept=\"image/*\" onclick=\"uploud(this)\"><div class=\"progress-bar\"></div></div><div class=\"detail\" id=\"sc"+value.id+"\"> <p class=\"number\">设备编号</p> <input type=\"text\" id=\"number"+value.id+"\" autocomplete=\"off\" > <p class=\"tip\" nullmsg=\"panorama_number\">该设备编号不存在</p></div></div>";
                        if(dev == 'ios'){
                            html=html+"<div class='sy-up-list'> <div class='upload' id='screen"+value.id+"'> <img class='imgspace' src='/static/image/blank.jpg'> <input class='update_input' id='panorama"+value.id+"' type='hidden' placeholder='请上传屏幕安装图' nullmsg='panorama_image' datatype='*'> <img class='addimg' src='/static/image/uploadimg-add.png'> <p id='up"+i+"' class='Upload-imginput' accept='image/*' onclick='uploud(this)'></p><div class='progress-bar'></div></div><div class='detail' id='sc"+value.id+"'> <p class='number'>设备编号</p><div class='sy_gg_scan'><textarea type='text' id='scan"+value.id+"'/> <a class='scaning' href='javascript:;' onclick='scan(this)'>扫码</a></div> <p class='tip' nullmsg='panorama_number'>屏幕编号重复</p></div></div>";
                        }else{
                            html=html+"<div class='sy-up-list'> <div class='upload' id='screen"+value.id+"'> <img class='imgspace' src='/static/image/blank.jpg'> <input class='update_input' id='panorama"+value.id+"' type='hidden' placeholder='请上传屏幕安装图' nullmsg='panorama_image' datatype='*'> <img class='addimg' src='/static/image/uploadimg-add.png'> <input type='file' id='up"+i+"' class='Upload-imginput' accept='image/*' onclick='uploud(this)'><div class='progress-bar'></div></div><div class='detail' id='sc"+value.id+"'> <p class='number'>设备编号</p><div class='sy_gg_scan'><textarea type='text' id='scan"+value.id+"'/> <a class='scaning' href='javascript:;' onclick='scan(this)'>扫码</a></div> <p class='tip' nullmsg='panorama_number'>屏幕编号重复</p></div></div>";
                        }
                    });
                    $(".con").html(html);
                }else{
                    $('.sy-installed-ts span').text('数据不存在');
                    $('.sy-installed-ts').show();
                    setTimeout(function(){location.href="/inner-install?token=<?=$token?>"},2000);

                }

            }
        });
    });
    //验证信息
    function tippanel(){
        $('.sy-installed-ts').show();
        setTimeout(function(){$('.sy-installed-ts').hide()},2000);
    }
    /*表单验证*/
    $("#idform").Validform({
//	  tiptype:3,
        tiptype:function(msg,o,cssctl){
            if(msg=='nameblank'){
                $('.sy-installed-ts span').text('姓名不能为空');
                tippanel();
            };
            if(msg=='numbphone'){
                $('.sy-installed-ts span').text('手机号码不能为空');
                tippanel();
            };
            if(msg=='errorphone'){
                $('.sy-installed-ts span').text('请输入正确的手机号');
                tippanel();
            };
            if(msg=='numbyzm'){
                $('.sy-installed-ts span').text('验证码不能为空');
                tippanel();
            };
            if(msg=='panorama_image'){
                $('.sy-installed-ts span').text('安装屏幕图片不能为空');
                tippanel();
            };
            if(msg=='panorama_number'){
                $('.sy-installed-ts span').text('设备编码不能为空');
                tippanel();
            };
        },
        beforeSubmit:function(curform){
            $('.sy-up-list .tip').css('visibility','hidden');
            var install_name=$("#install_name").val();
            var install_mobile=$("#install_mobile").val();
            var verify=$("#verify").val();
            var install_images="[";
            var screen=$.parseJSON($("#screenBB").val());
            var  isstop=0;
            $.each(screen,function(i,value){
                if(install_images.indexOf($("#scan"+value.id).val()) != -1){
                    isstop=1;
                    $('#sc'+value.id).find(".tip").css('visibility','visible');
                    return false;
                }
                install_images=install_images+"{\"id\":\""+value.id+"\",\"screen_number\":\""+$("#scan"+value.id).val()+"\",\"image\": \""+$("#panorama"+value.id).val()+"\"},";
            });
            install_images = install_images.substring(0,install_images.length-1) + "]";
            if(isstop==1){
                return false;
            }
            $.ajax({
                url:baseApiUrl+"/screeninstallunline",
                type:"post",
                dataType:"json",
                data:{id:<?=$shopid?>,install_name:install_name,install_mobile:install_mobile,install_images:install_images,verify:verify,token:'<?=$token?>',isupdate:false},
                success:function(data) {
                    if(data.status==200){
                        location.href="/screen/underlinewait?shopid=<?=$shopid?>&token=<?=$token?>";

                    }else{
                        if(data.status==484)
                        {
                            //已安装在其它屏幕中
                            $('#sc'+data.data.id).find(".tip").html("屏幕编号与店铺不符");
                            $('#sc'+data.data.id).find(".tip").css('visibility','visible');
                        }else if(data.status==635){
                            //仓库中不存在
                            $('#sc'+data.data.id).find(".tip").html("屏幕编号不存在或未出库");
                            $('#sc'+data.data.id).find(".tip").css('visibility','visible');
                        }
                        else{
                            $('.sy-installed-ts span').text(data.message);
                            tippanel();
                        }

                    }
                }
            });
            return false;
        },
        datatype:{
            "phone":/^1[0-9]{10}$/
        }
    })

    //发送验证码倒计时
    var countdown=60;
    function settime(val){
        if (countdown == 0) {
            val.removeAttribute("disabled");
            val.value = "再次获取验证码";
            countdown = 60;
            return false;
        } else {
            if(countdown == 60){
                var mobile = $('#install_mobile').val();
                var reg = /^1[0-9]{10}$/;
                var install_name= $('#install_name').val();
              //姓名验证  var namereg=/^([\u4e00-\u9fa5]){2,7}$/; //只能是中文，长度为2-7位
                var re = new RegExp(reg);
                if(install_name == ''){
                    $('.sy-installed-ts span').text('安装姓名不能为空');
                    tippanel();
                    return false;
                }
                if (!re.test(mobile)) {
                    $('.sy-installed-ts span').text('请输入正确的手机号');
                    tippanel();
                    return true;
                }
                if(mobile == ''){
                    $('.sy-installed-ts span').text('安装人手机号不能为空');
                    tippanel();
                    return false;
                }

                //类型为4的是电工证验证
                $.ajax({
                    type: "GET",
                    url: baseApiUrl+"/verify?type=4&mobile="+mobile+"&token=<?=$token?>&name="+install_name,
                    success:function(data){
                        console.log(data.status);
                        if(data.status == 200){
                            $('.sy-installed-ts span').text(data.message);
                            tippanel();
                        }else{
                            $('.sy-installed-ts span').text(data.message);
                            tippanel();
                        }
                        return false;
                    }
                });
            }
            val.setAttribute("disabled", "disabled");
            val.value = "("+countdown+")秒后重新获取";
            countdown--;
        }
        setTimeout(function() { settime(val) },1000)
    }
    function uploud(q){
       // uploadimg('Upload-imginput'); 开发环境开启 以下关闭
        var ua = navigator.userAgent.toLowerCase();
        if (/iphone|ipad|ipod/.test(ua)) {
            //uploadimg('Upload-imginput');
            var input_id = $(q).parent().attr('id');
            var result = {"action":input_id};
            webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
        }else if(/android/.test(ua)) {
            var input_id = $(q).parent().attr('id');
            var result = {"action":input_id}
            window.jsObj.HtmlcallJava(JSON.stringify(result));
        }
    }

    function scan(q){
        var scanid=$(q).siblings().attr('id');
        var ua = navigator.userAgent.toLowerCase();
        if (/iphone|ipad|ipod/.test(ua)) {
            var result = {"action":"scan","scanid":scanid}
            webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
        }else if(/android/.test(ua)) {
            var result = {"action":"scan","scanid":scanid}
            window.jsObj.HtmlcallJava(JSON.stringify(result));
        }
    }
    function scan_end(id,data){
        $('#'+id).val(data);
    }

</script>
<script type="text/javascript" src="/static/js/img-upload.js?v=1.5"></script>
</body>
</html>
