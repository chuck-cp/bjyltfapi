<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>请填写安装反馈</title>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet"  href="/static/css/sy_xgnewfeedback.css" />
    <script src="/static/js/jquery-1.7.2.min.js"></script>
    <style>
		.sy-body{    overflow-x: hidden;}
        .sy-installed-ts{ width: 100%; height: 30px; line-height: 30px; text-align: center;  position: fixed;
            left: 0; top: 50%; margin-left:0px;margin-top: -15px; font-size: 14px; display: none; background: none;}
        .sy-installed-ts span{background: rgba(0,0,0,0.3); color: #fff;border-radius:5px; font-size: 14px; padding: 5px 10px;}
        .text_area{margin-top: 14px; border: 1px solid #cccccc; min-height: 80px; width: 100%;}
        .lf{text-align: left !important;}
        .add{display: none;}
		.sy-newinstalled{left: 0;margin-left: 0;    width: 100%;}
		.sy-newuploadimg .tit{   color: inherit;background:none;border-left: 4px solid #ff7d09; padding:0 10px;}
		.sy_imgwraper{background: none;}
		.fen_ge{background: #f0f0f0; width: 120%; margin-left: -6%;height: 10px;}
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
				<br />
        <div class="sy-newuploadimg">
            <p class="tit lf xz">请上传安装完成后的LED屏幕照片并填写设备编号</p>
            <div class="con">
            </div>
			<br />
			<!-- <p class="fen_ge">&nbsp;</p> -->
			<br />
            <p class="tit lf add">问题描述</p>
            <div class="problem add">

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
    var myFolder = '/install_check/<?=date('Y-m-d')?>/';
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    var dev = "<?=$dev?>";
    var replace_id = "<?=$replace_id?>";
    var operate = "<?=$operate?>";
    $(function(){
        $.ajax({
            url:baseApiUrl+"/screen-new/number/<?=$shopid?>?token=<?=$token?>&replace_id="+replace_id,
            type:"get",
            dataType:"json",
            success:function(data) {
                if(data.status == 200 && data.data.shopData != null){
                    if(operate != 4){
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
                    }
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
                            "<img class='addimg' src='/static/images/newuploadimg-add1.png' > " +
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
                            "<input class='update_input' id='panorama"+value.id+"' value='' type='hidden' placeholder='请上传屏幕安装图' nullmsg='panorama_image' datatype='*'>"+
                            "<img class='imgspace' src='/static/images/newuploadimgspacing.png'> " +
                            "<img class='addimg' src='/static/images/newuploadimg-add1.png' > " +
                            "<input type='file' id='up"+i+"' class='Upload-imginput' onclick='uploud(this)'> " +
                            "</div> " +
                            "</div> " +
                            "<div class='detail' id='sc"+value.id+"'> " +
                            "<p class='number'>设备编号</p> " +
                            "<div class='sy_gg_scan'> " +
                            "<input type='text' placeholder='请扫描或填写设备编码' value=''  id='scan"+value.id+"' nullmsg='panorama_number' datatype='*'> " +
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
    if(operate == 4){
        $('.xz').html('新增屏幕编号和安装照片');
        $('.add').css('display','block');
        $('.problem').append(' <textarea class="text_area" name="" id="problem" placeholder="请输入问题描述(必填)" nullmsg="panorama_problem" datatype="*"></textarea>');
    }
    $("#idform").Validform({
        postonce:true,
        tiptype:function(msg,o,cssctl){
            if(msg=='panorama_image'){
                conftankuangparam("sy_examin_panel",'安装屏幕图片不能为空');
            };
            if(msg=='panorama_number'){
                conftankuangparam("sy_examin_panel",'设备编码不能为空');
            };
            if(operate == 4){
                if(msg=='panorama_problem'){
                    conftankuangparam("sy_examin_panel",'问题描述不能为空');
                };
            }
        },
        beforeSubmit:function(curform){
            var judedata=$('.sy-newinstalled').attr('data-v');
            if(judedata==0){
                //$('.sy-newinstalled').addClass('set-grey');
                //$('.sy-newinstalled').attr('data-v',1);
                $('.detail .tip').css('visibility','hidden');
                var install_name=$("#install_name").html();
                var install_mobile=$("#install_mobile").html();
                var screen=$.parseJSON($("#screenBB").val());

                var install_images="[";
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
                if(isstop==1){
                    return false;
                }
                install_images = install_images.substring(0,install_images.length-1) + "]";
                var ajaxUrl = '';
                if(operate == 4){//增屏
                    ajaxUrl = '/screeninstall/screen-incr'
                }else{//新店安装
                    ajaxUrl = '/screeninstall/screeninster';
                }
                $.ajax({
                    url:baseApiUrl+ajaxUrl,
                    type:"post",
                    dataType:"json",
                    data:{
                        id:<?=$shopid?>,
                        install_name:install_name,
                        install_mobile:install_mobile,
                        install_images:install_images,
                        token:'<?=$token?>',
                        isupdate:false,
                        replace_id:replace_id,
                        problem_description:$('#problem').val(),
                        operate:operate,
                    },
                    success:function(data) {
                        if(data.status==200){
                            location.href="/screen/screenwait?shopid=<?=$shopid?>&token=<?=$token?>&replace_id=<?=$replace_id?>&operate=<?=$operate?>";
                        }else{
                            if(data.status==484)
                            {
                                $('.detail .tip').css('visibility','hidden');
                                //已安装在其它屏幕中
                                $('#sc'+data.data.id).find(".tip").html("屏幕编号与店铺不符");
                                $('#sc'+data.data.id).find(".tip").css('visibility','visible');
                                $('.sy-newinstalled').removeClass('set-grey');
                                $('.sy-newinstalled').attr('data-v',1);
                            }else if(data.status==635){
                                $('.detail .tip').css('visibility','hidden');
                                //仓库中不存在
                                $('#sc'+data.data.id).find(".tip").html("屏幕编号不存在或未出库");
                                $('#sc'+data.data.id).find(".tip").css('visibility','visible');
                                $('.sy-newinstalled').removeClass('set-grey');
                                $('.sy-newinstalled').attr('data-v',1);
                            }else{
                                conftankuangparam("sy_examin_panel",data.message);
                                $('.sy-newinstalled').removeClass('set-grey');
                                $('.sy-newinstalled').attr('data-v',1);
                            }
                        }
                    }
                });
            }
            return false;
        }

    })

//
    function uploud(q){
          // uploadimg('Upload-imginput'); //开发环境开启 以下关闭
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
//
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
//
//</script>
<script type="text/javascript" src="/static/js/img-upload.js" ></script>
</body>
</html>
