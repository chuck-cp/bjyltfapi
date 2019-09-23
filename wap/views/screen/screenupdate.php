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
    <link rel="stylesheet"  href="/static/css/sy_xgnewfeedback.css" />
    <script src="/static/js/jquery-1.7.2.min.js"></script>
</head>
<body class="sy-body">
<div class="sy_loadingpage" >
    <img src="/static/images/loading.gif">
    <p>正在加载...</p>
</div>
<div class="sy-newunchecked" style="display: none;">
    <form id="idform">
        <input  id="screenBB" type="hidden" >
        <div class="content">
            <div class="sy_onecont_list">

            </div>
            <!--请上传安装完成后的LED屏幕照片并填写设备编号-->
            <div class="sy-newuploadimg">
                <p class="tit">请上传安装完成后的LED屏幕照片并填写设备编号</p>
                <div class="con">
                    <!--                <div class="sy-new-list">-->
                    <!--                    <div class="sy_imgwraper">-->
                    <!--                        <div class="upload">-->
                    <!--                            <img class="imgspace" src="images/newuploadimgspacing.png">-->
                    <!--                            <img class="addimg" src="images/newuploadimg-add.png" >-->
                    <!--                            <input type="file" class="Upload-imginput">-->
                    <!--                        </div>-->
                    <!--                    </div>-->
                    <!--                    <div class="detail">-->
                    <!--                        <p class="number">设备编号</p>-->
                    <!--                        <div class='sy_gg_scan'>-->
                    <!--                            <input type="text" placeholder="请扫描或填写设备编码">-->
                    <!--                        </div>-->
                    <!--                        <!--错误提示语已隐藏  信息错误则显示visibility: visible-->-->
                    <!--                        <p class="tip">屏幕未出库或已被激活</p>-->
                    <!--                        <!--<a class='scaning' href='javascript:;'>扫码</a>-->-->
                    <!--                        <p class="scaning">-->
                    <!--                            <span><img src="images/sy_newscan.png"></span>-->
                    <!--                            扫码-->
                    <!--                        </p>-->
                    <!---->
                    <!--                    </div>-->
                    <!--                </div>-->


                </div>
            </div>
        </div>
        <button class="sy-newinstalled" type="submit" data-v="0">完成安装</button>
    </form>
</div>
<div class="mask"></div>
<!--提交成功等待审核-->
<div class="sy_waitins_choicetwo">
    <div class="img">
        <p>提交成功等待审核</p>
        <img class="bgimg" src="/static/images/sy_waitinstall_panelbgtwo.png">
        <span class="sy_waitins_close"><img src="/static/images/sy_waitinstall_close.png"></span>
    </div>
</div>
<div class="mask"></div>
<!--确定弹框-->
<div class="sy_examin_panel">
    <div class="img">
        <div class="examcon">
            <p class="con">提交成功等待审核</p>
            <p class="btn"><a href="javascript:;" onclick="closetankuan('sy_examin_panel')">确定</a></p>
        </div>
    </div>
</div>
<!--完成安装提示-->
<p class="sy-installed-ts"><span>提交成功</span></p>
<script type="text/javascript" src="/static/js/jQuery.validate.min.js" ></script>
<script type="text/javascript" src="/static/js/cos-js-sdk-v4.js" ></script>
<script type="text/javascript" src="/static/js/mobile-adapt.js"></script>
<script type="text/javascript" src="/static/js/exif.js?v=1.2"></script>
<script type="text/javascript" src="/static/js/tankuangpub.js" ></script>
<script>
    var myFolder = '/install_check/<?//=date('Y-m-d')?>/';
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    $(function(){
        $.ajax({
            url:baseApiUrl+"/screeninstall/screenactivation",
            type:"get",
            dataType:"json",
            data:{id:<?=$shopid?>,token:'<?=$token?>'},
            success:function(data) {
                if(data.status==400&&data.data.shopData!=null){
                    var tablehtml=" <table>";
                    if(data.data.install_team_id>0){
                        tablehtml=tablehtml+"<tr>" +
                        "<td>组长</td> " +
                        "<td>"+data.data.team_member_name+"</td>" +
                        "</tr>" +
                        "<tr>" +
                        "<td>手机号</td>" +
                        "<td>"+data.data.team_member_mobile+"</td>" +
                        "</tr>";
                    }
                    tablehtml=tablehtml+" <tr> " +
                    "<td>安装人姓名</td> " +
                    "<td id='install_name'>"+data.data.install_member_name+"</td> " +
                    "</tr> " +
                    "<tr> " +
                    "<td>手机号</td> " +
                    "<td id='install_mobile'>"+data.data.install_mobile+"</td> " +
                    "</tr> " +
                    "</table>";
                    $(".sy_onecont_list").html(tablehtml);
                    $("#screenBB").val(JSON.stringify(data.data.shopData));
                    var  html="";
                    $.each(data.data.shopData,function(i,value){
                        var ua = navigator.userAgent.toLowerCase();
                        if (/iphone|ipad|ipod/.test(ua)) {
                            html=html+"<div class='sy-new-list'> " +
                            "<div class='sy_imgwraper'> " +
                            "<div class='upload' id='screen"+value.id+"'> " +
                            "<input class='update_input' id='panorama"+value.id+"' type='hidden' placeholder='请上传屏幕安装图' nullmsg='panorama_image' datatype='*'>"+
                            "<img class='imgspace' src='/static/images/newuploadimgspacing.png'> " +
                            "<img class='addimg' src='/static/images/newuploadimg-add.png' > " +
                            "<p type='file' id='up"+i+"' class='Upload-imginput' onclick='uploud(this)'></p> " +
                            "</div> " +
                            "</div> " +
                            "<div class='detail' id='sc"+value.id+"'> " +
                            "<p class='number'>设备编号</p> " +
                            "<div class='sy_gg_scan'> " +
                            "<input type='text' placeholder='请扫描或填写设备编码'  id='scan"+value.id+"' nullmsg='panorama_number' datatype='*'> " +
                            "</div>" +
                            "<p class='tip'>屏幕编号重复</p> " +
                            "<p class='scaning'> " +
                            "<span onclick='scan(this)'><img src='/static/images/sy_newscan.png'> 扫码</span>" +

                            "</p> " +
                            "</div> " +
                            "</div>";
                        }else if(/android/.test(ua)) {
                            html=html+"<div class='sy-new-list'> " +
                            "<div class='sy_imgwraper'> " +
                            "<div class='upload' id='screen"+value.id+"'> " +
                            "<input class='update_input' id='panorama"+value.id+"' type='hidden' placeholder='请上传屏幕安装图' nullmsg='panorama_image' datatype='*'>"+
                            "<img class='imgspace' src='/static/images/newuploadimgspacing.png'> " +
                            "<img class='addimg' src='/static/images/newuploadimg-add.png' > " +
                            "<input type='file' id='up"+i+"' class='Upload-imginput' onclick='uploud(this)'> " +
                            "</div> " +
                            "</div> " +
                            "<div class='detail' id='sc"+value.id+"'> " +
                            "<p class='number'>设备编号</p> " +
                            "<div class='sy_gg_scan'> " +
                            "<input type='text' placeholder='请扫描或填写设备编码'  id='scan"+value.id+"' nullmsg='panorama_number' datatype='*'> " +
                            "</div>" +
                            "<p class='tip'>屏幕编号重复</p> " +
                            "<p class='scaning'> " +
                            "<span onclick='scan(this)'><img src='/static/images/sy_newscan.png'> 扫码</span>" +

                            "</p> " +
                            "</div> " +
                            "</div>";
                        }

                    });
                    $(".con").html(html);
                    $('.sy_loadingpage').hide();
                    $('.sy-newunchecked').show();
                }else{
                    conftankuangparam("sy_examin_panel","数据有误,请重试！");
                    setTimeout(function(){location.href="/screen/screenshoplist?token=<?=$token?>"},2000);
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
                conftankuangparam("sy_examin_panel",'安装屏幕图片不能为空');
            };
            if(msg=='panorama_number'){
                conftankuangparam("sy_examin_panel",'设备编码不能为空');

            };
        },
        beforeSubmit:function(curform){
            var judedata=$('.sy-newinstalled').attr('data-v');
            if(judedata==0){
                $('.sy-newinstalled').addClass('set-grey');
                $('.sy-newinstalled').attr('data-v',1);
                $('.detail .tip').css('visibility','hidden');
                var install_name=$("#install_name").html();
                var install_mobile=$("#install_mobile").html();
                var install_images="[";
                var screen=$.parseJSON($("#screenBB").val());
                var  isstop=0;
                $.each(screen,function(i,value){
                    if(install_images.indexOf($("#scan"+value.id).val()) != -1){
                        isstop=1;
                        $('.detail .tip').css('visibility','hidden');
                        $('#sc'+value.id).find(".tip").css('visibility','visible');
                        $('.sy-newinstalled').removeClass('set-grey');
                        $('.sy-newinstalled').attr('data-v',0);
                        return false;
                    }
                    install_images=install_images+"{\"id\":\""+value.id+"\",\"screen_number\":\""+$("#scan"+value.id).val()+"\",\"image\": \""+$("#panorama"+value.id).val()+"\"},";
                });
                install_images = install_images.substring(0,install_images.length-1) + "]";
                if(isstop==1){
                    return false;
                }
                $.ajax({
                    url:baseApiUrl+"/screeninstall/screeninster",
                    type:"post",
                    dataType:"json",
                    data:{id:<?=$shopid?>,install_name:install_name,install_mobile:install_mobile,install_images:install_images,token:'<?=$token?>',isupdate:'<?=$isupdate?>'},
                    success:function(data) {
                        console.log(data);
                        if(data.status==200){
                            location.href="/screen/screenwait?shopid=<?=$shopid?>&token=<?=$token?>";
                        }else{
                            if(data.status==484)
                            {
                                $('.detail .tip').css('visibility','hidden');
                                //已安装在其它屏幕中
                                $('#sc'+data.data.id).find(".tip").html("屏幕编号与店铺不符");
                                $('#sc'+data.data.id).find(".tip").css('visibility','visible');
                                $('.sy-newinstalled').removeClass('set-grey');
                                $('.sy-newinstalled').attr('data-v',0);
                            }else if(data.status==635){
                                $('.detail .tip').css('visibility','hidden');
                                //仓库中不存在
                                $('#sc'+data.data.id).find(".tip").html("屏幕编号不存在或未出库");
                                $('#sc'+data.data.id).find(".tip").css('visibility','visible');
                                $('.sy-newinstalled').removeClass('set-grey');
                                $('.sy-newinstalled').attr('data-v',0);
                            }
                            else{
                                conftankuangparam("sy_examin_panel",data.message);
                                $('.sy-newinstalled').removeClass('set-grey');
                                $('.sy-newinstalled').attr('data-v',0);
                            }
                        }
                    }
                });
            }
            return false;
        }
    })

    function uploud(q){
      //  uploadimg('Upload-imginput'); //开发环境开启 以下关闭
        var ua = navigator.userAgent.toLowerCase();
        if (/iphone|ipad|ipod/.test(ua)) {
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
        var scanid=$(q).parent().siblings('.sy_gg_scan').find('input').attr('id');
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
<script type="text/javascript" src="/static/js/img-upload.js?v=1.3"></script>
</body>
</html>
