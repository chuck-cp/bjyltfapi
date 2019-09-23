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
        .sy-fillin-infor .name p span{color: #757575;}
        .paddfanxian{width: 100%;padding: 5px 0;font-size: 13px; color: #757575;}
        .sy-uploadimg {
          padding-top: 0px;
        }
    </style>
</head>
<body class="sy-body">
<div class="sy-wrapper">
    <div class="sy-unchecked">
        <form id="idform">
            <input  id="screenBB" type="hidden" >
            <div class="content">
                <!--填写信息-->
                <div class="sy-fillin-infor">
                        <div class="name">
                            <p>安装人姓名&nbsp;:&nbsp;<span id="install_name"></span></p>
                        </div>
                        <div class="name">
                            <p>手机号码&nbsp;:&nbsp;<span id="install_mobile"></span></p>
                        </div>
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
            url:baseApiUrl+"/screeninstall/underlineimgshow/<?=$shopid?>?token=<?=$token?>",
            type:"get",
            dataType:"json",
            success:function(data) {
                if(data.status==200&&data.data!=null){
                    console.log(data.data.screen);
                    $("#install_name").html(data.data.install_name);
                    $("#install_mobile").html(data.data.install_mobile);
                    $("#screenBB").val(JSON.stringify(data.data.screen));
                    var  html="";
                    $.each(data.data.screen,function(i,value){
//                        html=html+"<div class='sy-up-list'> <div class='upload' id='screen"+value.id+"'> <img class='imgspace' src='/static/image/blank.jpg'> <input class='update_input' id='panorama"+value.id+"' type='hidden' placeholder='请上传屏幕安装图' nullmsg='panorama_image' datatype='*' value='"+value.image+"'> <img class='addimg' src='"+value.image+"'> <input type='file' id='up"+i+"' class='Upload-imginput' accept='image/*' onclick='uploud(this)'><div class='progress-bar'></div></div><div class='detail' id='sc"+value.id+"'> <p class='number'>设备编号</p><p class='paddfanxian'>"+value.software_number+"</p></div></div>";
                        if(dev == 'ios'){
                            html=html+"<div class='sy-up-list'> <div class='upload' id='screen"+value.id+"'> <img class='imgspace' src='/static/image/blank.jpg'> <input class='update_input' id='panorama"+value.id+"' type='hidden' placeholder='请上传屏幕安装图' nullmsg='panorama_image' datatype='*' value='"+value.image+"'> <img class='addimg' src='"+value.image+"'><p id='up"+i+"' class='Upload-imginput' accept='image/*' onclick='uploud(this)'></p><div class='progress-bar'></div></div><div class='detail' id='sc"+value.id+"'> <p class='number'>设备编号</p><p class='paddfanxian'>"+value.software_number+"</p></div></div>";
                        }else{
                            html=html+"<div class='sy-up-list'> <div class='upload' id='screen"+value.id+"'> <img class='imgspace' src='/static/image/blank.jpg'> <input class='update_input' id='panorama"+value.id+"' type='hidden' placeholder='请上传屏幕安装图' nullmsg='panorama_image' datatype='*' value='"+value.image+"'> <img class='addimg' src='"+value.image+"'> <input type='file' id='up"+i+"' class='Upload-imginput' accept='image/*' onclick='uploud(this)'><div class='progress-bar'></div></div><div class='detail' id='sc"+value.id+"'> <p class='number'>设备编号</p><p class='paddfanxian'>"+value.software_number+"</p></div></div>";
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
            if(msg=='panorama_image'){
                $('.sy-installed-ts span').text('安装屏幕图片不能为空');
                tippanel();
            };
        },
        beforeSubmit:function(curform){
            var install_name=$("#install_name").text();
            var install_mobile=$("#install_mobile").text();
            var install_images="[";
            var screen=$.parseJSON($("#screenBB").val());
            $.each(screen,function(i,value){
                install_images=install_images+"{\"id\":\""+value.id+"\",\"screen_number\":\""+$("#scan"+value.id).val()+"\",\"image\": \""+$("#panorama"+value.id).val()+"\"},";
            });
            install_images = install_images.substring(0,install_images.length-1) + "]";
            $.ajax({
                url:baseApiUrl+"/screeninstall/underlineimgupdate",
                type:"post",
                dataType:"json",
                data:{id:<?=$shopid?>,install_name:install_name,install_mobile:install_mobile,install_images:install_images,token:'<?=$token?>'},
                success:function(data) {
                    if(data.status==200){
                        setTimeout(function() {location.href="/inner-install?token=<?=$token?>"},1000);
                    }else{
                            $('.sy-installed-ts span').text(data.message);
                            tippanel();
                    }
                }
            });
          return false;
        }
    })


    function uploud(q){
        // uploadimg('Upload-imginput'); //开发环境开启 以下关闭
        var ua = navigator.userAgent.toLowerCase();
        if (/iphone|ipad|ipod/.test(ua)) {
            //uploadimg('Upload-imginput');
            var input_id = $(q).parent().attr('id');
            var result = {"action":input_id};
            webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
        }else if(/android/.test(ua)) {
            var input_id = $(q).parent().attr('id');
            var result = {"action":input_id};
            window.jsObj.HtmlcallJava(JSON.stringify(result));
        }
    }


</script>
<script type="text/javascript" src="/static/js/img-upload.js?v=1.5"></script>
</body>
</html>