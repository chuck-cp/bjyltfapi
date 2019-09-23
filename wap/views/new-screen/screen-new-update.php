<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <?if($operate == 1):?>
        <title>新店安装</title>
    <?else:?>
        <title>屏幕新增</title>
    <?endif;?>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet"  href="/static/css/sy_xgnewfeedback.css?" />
    <script src="/static/js/jquery-1.7.2.min.js"></script>
    <style>
        .tit{text-align: left !important;}
    </style>
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
            <div class="sy-newuploadimg" style="padding-bottom: 0px;">
                <?if($operate == 1):?>
                    <p class="tit">请上传安装完成后的LED屏幕并填写设备编号</p>
                <div class="con">
                <?else:?>
                    <p class="tit">新增设备编号和安装照片</p>
                <div class="con">
                <?endif;?>
                </div>
            </div>						
            <div id="sjwt" class="sy-newuploadimg">
                <p class="tit">问题描述</p>
                <textarea name="" id="problem" rows="5" style="width: 100%;border: 1px solid #cccccc"></textarea>
            </div>
        </div>
        <button class="sy-newinstalled" type="submit" data-v="0">提交安装信息</button>
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
    var dev = "<?=$dev?>";
    var operate = "<?=$operate?>";
    $(function(){
        $.ajax({
            url:baseApiUrl+"/screen-new/screenimgshow/<?=$shopid?>?token=<?=$token?>&replace_id=<?=$replace_id?>",
            type:"get",
            dataType:"json",
            success:function(data) {
                if(data.status==200 && data.data!=null){
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
                    "<td>"+data.data.install_member_name+"</td> " +
                    "</tr> " +
                    "<tr> " +
                    "<td>手机号</td> " +
                    "<td>"+data.data.install_mobile+"</td> " +
                    "</tr> " +
                    "</table>";
                    if(operate == 1){
                        $(".sy_onecont_list").html(tablehtml);
                    }

                    $("#screenBB").val(JSON.stringify(data.data.screen));
                    var  html="";
                    $.each(data.data.screen,function(i,value){
                        var ua = navigator.userAgent.toLowerCase();
                        if (/iphone|ipad|ipod/.test(ua)) {
                            html=html+"<div class='sy-new-list'> " +
                            "<div class='sy_imgwraper'> " +
                            "<div class='upload' id='screen"+value.id+"'> " +
                            "<input class='update_input' id='panorama"+value.id+"' type='hidden' placeholder='请上传屏幕安装图' nullmsg='panorama_image' datatype='*' value='"+value.image+"'>"+
                            "<img class='imgspace' src='/static/images/newuploadimgspacing.png'> " +
                            "<img class='addimg' src='"+value.image+"' > " +
                            "<p type='file' id='up"+i+"' class='Upload-imginput' onclick='uploud(this)'></p> " +
                            "</div> " +
                            "</div> " +
                            "<div class='detail' id='sc"+value.id+"'> " +
                            "<p class='number'>设备编号</p> " +
                            "<div class='sy_gg_scan'> " +
                            "<input type='text' readonly='readonly' placeholder='请扫描或填写设备编码'  id='scan"+value.id+"' nullmsg='panorama_number' datatype='*' value="+value.software_number+"> " +
                            "</div>" +
                            "<p class='tip'>屏幕编号重复</p> " +
                            "<p class='scaning'> " +
                            "</p> " +
                            "</div> " +
                            "</div>";
//                            html=html+"<div class='sy-new-list'> " +
//                            "<div class='upload' id='screen"+value.id+"'> " +
//                            "<img class='imgspace' src='/static/images/blank.jpg'> " +
//                            "<input class='update_input' id='panorama"+value.id+"' type='hidden' placeholder='请上传屏幕安装图' nullmsg='panorama_image' datatype='*' value='"+value.image+"'>"+
//                            "<img class='addimg' src='"+value.image+"'> " +
//                            "<p id='up"+i+"' class='Upload-imginput' accept='image/*' onclick='uploud(this)'></p>"+
//                            "<div class='progress-bar'></div>"+
//                            "</div> " +
//                            "<div class='detail' id='sc"+value.id+"'> " +
//                            "<p class='number'>设备编号</p> " +
//                            "<div class='sy_gg_scan'> " +
//                            "<p class='paddfanxian'>"+value.software_number+"</p>" +
//                            "</div> " +
//                            "<p class='tip'>屏幕编号重复</p> " +
//                            "</div> " +
//                            "</div>";
                        }else{
                            html=html+"<div class='sy-new-list'> " +
                            "<div class='sy_imgwraper'> " +
                            "<div class='upload' id='screen"+value.id+"'> " +
                            "<input class='update_input' id='panorama"+value.id+"' type='hidden' placeholder='请上传屏幕安装图' nullmsg='panorama_image' datatype='*' value='"+value.image+"'>"+
                            "<img class='imgspace' src='/static/images/newuploadimgspacing.png'> " +
                            "<img class='addimg' src='"+value.image+"' > " +
                            "<input type='file' id='up"+i+"' class='Upload-imginput' onclick='uploud(this)'> " +
                            "</div> " +
                            "</div> " +
                            "<div class='detail' id='sc"+value.id+"'> " +
                            "<p class='number'>设备编号</p> " +
                            "<div class='sy_gg_scan'> " +
                            "<input type='text' readonly='readonly' placeholder='请扫描或填写设备编码'  id='scan"+value.id+"' nullmsg='panorama_number' datatype='*' value="+value.software_number+"> " +
                            "</div>" +
                            "<p class='tip'>屏幕编号重复</p> " +
                            "<p class='scaning'> " +
                            "</p> " +
                            "</div> " +
                            "</div>";
                        }

                    });
                    $(".con:first").html(html);
                    if(operate == 1){
                        $("#sjwt").remove();
                    }else{
                        $("#problem").val(data.data.problem_description);
                    }

                    $('.sy_loadingpage').hide();
                    $('.sy-newunchecked').show();
                }else{
                    $('.sy-installed-ts span').text('数据不存在');
                    $('.sy-installed-ts').show();
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
        },
        beforeSubmit:function(curform){
            var install_images="[";
            var screen=$.parseJSON($("#screenBB").val());
            $.each(screen,function(i,value){
                install_images=install_images+"{\"id\":\""+value.id+"\",\"screen_number\":\""+$("#scan"+value.id).val()+"\",\"image\": \""+$("#panorama"+value.id).val()+"\"},";
            });
            install_images = install_images.substring(0,install_images.length-1) + "]";
            $.ajax({
                url:baseApiUrl+"/screeninstall/screenimgupdate",
                type:"post",
                dataType:"json",
                data:{id:<?=$shopid?>,
                    install_images:install_images,
                    token:'<?=$token?>',
                    replace_id:'<?=$replace_id?>',
                },
                success:function(data) {
                    if(data.status==200){
                        setTimeout(function() {location.href="/screen/screenshoplist?token=<?=$token?>"},1000);
                    }else{
                        conftankuangparam("sy_examin_panel",data.message);
                    }
                }
            });
            return false;
        }
    })


    function uploud(q){
       //  uploadimg('Upload-imginput'); //开发环境开启 以下关闭
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