<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>屏幕更换</title>
    <link rel="stylesheet" type="text/css" href="/static/css/mreset.css"/>
    <link rel="stylesheet" type="text/css" href="/static/css/reset.css?v=20180804">
    <style type="text/css">
        body{background: #f0f0f0;}
        /*选择地区弹层*/
        .express-area-box { position: absolute; bottom: -100%; left: 50%; z-index: 15; width: 320px; margin-left: -160px; background-color: #fff; color: #4d525d; display:none }
        .express-area-box header { position: relative; border-top: 1px solid #e2e2e2; border-bottom: 1px solid #e2e2e2; }
        .express-area-box header h3 { margin: 0 1.675rem; text-align: center; font-size: .8rem; line-height: 2.25rem; }
        .express-area-box header .back,
        .express-area-box header .close { position: absolute; top: 0; width: 1.675rem; height: 2.25rem; }
        .express-area-box header .back { display: none; left: 0; background: url(/static/image/back.png) no-repeat center;   background-size: .45rem .75rem; }
        .express-area-box header .close { right: 0; background: url(/static/image/close-area.png) no-repeat center; background-size: .675rem .675rem; }
        .express-area-box article { height: 210px; overflow-y: scroll; }
        .area-list li { padding: .5rem; border-bottom: 1px solid #e2e2e2; text-align: justify; font-size: .7rem; line-height: 1.25rem; }
        @media (min-width: 721px) and (max-width: 1300px) {
            .express-area-box { position: fixed; left: 0; width: 100%; margin-left: 0; }
        }
        @media (max-width: 720px) {
            .express-area-box { position: fixed; left: 0; width: 100%; margin-left: 0; }
        }
        .yx_srnr_dqu{font-style: normal; font-size: 13px;color: #b2b2b2;width: 100%; display: inline-block;}
        .upload_img_box{width: 19%;margin-top: 14px;margin-bottom: 14px;}
        .sy_bgzsytp{ position: absolute; left: 0; top: 0; width: 100%; height: 100%;}
        /* 上传 */
        .upload{ width: 19%;  box-sizing: border-box; position: relative; }
        .upload .imgspace{ width: 100%; height:auto; overflow: hidden;}
        .upload .addimg{ width: 100%; height: 100%; position: absolute; left: 0; top: 0;}
        .upload .Upload-imginput{width: 100%;height: 100%;position: absolute;left: 0;top: 0;opacity: 0; cursor: pointer;padding: 0;}
        /* 提示弹框 */
        .sy-installed-ts{top: 14%;}
        /* 苹果输入框 */
        .shop_info input{line-height: 1.4;border-radius:0 ;}
        /* 遮挡了上传功能 */
        .upload{width: 24%;float: left;}
        .sc_fail_ts{width: 126%; font-size: 12px;    top: -5%;}
        /* 内容 */
        .submission { position: fixed; background: #f86908; font-size: 16px; bottom: 0; color: #fff; width: 100%; text-align: center; padding-top: 16px; padding-bottom: 16px; border: 0; }
        .old_equipment{margin-top: 14px;}
        .old_equipment_num{border-left: 4px solid #ff7d09;}
        .old_equipment p{padding: 16px 12px;background: #fff;margin-bottom: 1px;overflow: hidden;}
        .sao_ma{float: right; color: #ff7d09; border: 1px #ff7d09 solid; padding: 2px 10px; border-radius: 10px;visibility: hidden;}
        .remarks_col{color: #999999;}
        .old_equipment_num{padding-left: 10px;}
        .wenti{ height: 100px; border: 0;}
        .upload_box{background: #fff; overflow: hidden; padding-top: 12px; padding-bottom: 12px; padding-left: 12px;margin-bottom: 1px;}
        .sao_ma_box{float: right;width: 66%;position: relative;}
        .old_equipment .sao_ma_num{padding: 0 12px;color: #999999;    margin-top: 14px;}
        .tip{color: red}
        .upload_box{padding-right: 12px}
        .old_equipment div{padding: 16px 12px; background: #fff; margin-bottom: 1px; overflow: hidden;}
        .red{color: red; visibility: hidden;    padding-bottom: 0 !important;    clear: both;}
        .progress-bar{opacity: 0}
        .sao_ma2{position: absolute; right: 0; z-index: 999; top: 34%; visibility: hidden;}
        #old0,#newDv0{background: none}
        #newpic{position: relative}
    </style>
</head>
<body >
<form id="idform" >
    <div class="old_equipment" id="oldDv">
        <p class="old_equipment_tt">
            <span class="old_equipment_num">更换旧设备编号</span><span class="remarks_col"></span>
        </p>
    </div>
    <div class="old_equipment" id="newpic">
        <p class="old_equipment_tt" style="display: block;">
            <span class="old_equipment_num">更换新设备编号</span><span class="remarks_col">（选填）</span>
        </p>
        <div class="upload_box">
            <div class="upload" id="upload1">
                <input id="shop_image" class="update_input" type="hidden" nullmsg="shopimage" datatype="*">
                <img class="imgspace" src="/static/images/up_img.png">
                <img class="addimg" src="/static/images/up_img.png">
                <!--?if ($from == 'app'):?-->
                <p class="Upload-imginput" id="up2"></p>
                <!--?else:?-->
                <input type="file" class="Upload-imginput" accept="image/*" id="up1">
                <!--?endif;?-->
                <div class="progress-bar" step="0"></div>
            </div>
            <div class="sao_ma_box">
                <p class="sao_ma_num fsm"><input type="text" name="" id="" value="" placeholder="请扫描或填写设备软件编号" /></p>
                <p><span onclick='scan(this)' class="sao_ma">扫码</span></p>
            </div>
        </div>
    </div>
    <div class="old_equipment">
        <p class="old_equipment_tt"><span class="old_equipment_num">问题描述</span><span class="remarks_col">（必填）</span></p>
        <p class="wenti" rows="" id="descript" cols="" placeholder="请填写问题描述" nullmsg='problem_description' datatype='*' value=""></p>
    </div>
    <br /><br /><br /><br /><br />
    <button class="submission sy-newinstalled" type="submit" data-v="0">提交安装信息</button>
</form>
<!--提交失败提示-->
<p class="sy-installed-ts">提交失败</p>
<!--确定弹框-->
<div class="sy_examin_panel">
    <div class="img">
        <div class="examcon">
            <p class="con">提交成功等待审核</p>
            <p class="btn"><a href="javascript:;" onclick="closetankuan('sy_examin_panel')">确定</a></p>
        </div>
    </div>
</div>
<input type="hidden" name="current" id="current" value="">
<script src="/static/js/jquery-1.7.2.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="/static/js/jQuery.validate.min.js" ></script>
<script type="text/javascript" src="/static/js/cos-js-sdk-v4.js" ></script>
<script type="text/javascript" src="/static/js/mobile-adapt.js"></script>
<script type="text/javascript" src="/static/js/exif.js?v=1.2"></script>
<script type="text/javascript" src="/static/js/tankuangpub.js" ></script>
<script src="/static/js/img-upload.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    //一：加载数据
    var myFolder = '/member/<?=$member_id?>/';
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    var dev = "<?=$dev?>";
    $(function(){
        $.ajax({
            url:baseApiUrl+"/screen-change/change-screen-update/?replace_id=<?=$replace_id?>&token=<?=$token?>",
            type:"get",
            dataType:"json",
            success:function(data) {
                if(data.status==200){
                    if (data.data.install_software_number == "") {
                        window.location.href = "/screen/change-view?shopid=<?=$shopid?>&token=<?=$token?>&dev=<?=$dev?>&replace_id=<?=$replace_id?>";
                        return;
                    }
                    var html="";
                    var oldDv = "";
                    var eachNumber = data.data.remove_device_number.split(',');

                    for (var i=0; i<eachNumber.length; i++){
                        //1.old device number
                        oldDv=oldDv+"<div>" +
                            "<span class='fsm'><input type='text' name='old' id='old"+i+"' value='"+eachNumber[i]+"' disabled='disabled' /></span>"+
                            "<span><span onclick='scan(this)' class='sao_ma'>扫码</span></span>"+"<p class='red'>设备未在此店铺中找到</p>"+
                            "</div>";

                    }
                    var newSolt = data.data.install_software_number.split(',');
                    for (var i= 0; i < newSolt.length; i++){
                        //2. new device number
                        var ua = navigator.userAgent.toLowerCase();
                        if (/iphone|ipad|ipod/.test(ua)) {
                            html = html+'<div class="upload_box">' +
                                '<div class="upload" id="upload'+(i+1)+'">' +
                                '<input id="panorama'+i+'" name="newpic" class="update_input" type="hidden" nullmsg="shopimage" value="'+data.data.images[i]+'" datatype="*">' +
                                '<img class="imgspace" src="/static/images/up_img.png">' +
                                '<img class="addimg" src="'+data.data.images[i]+'">' +
                                '<p class="Upload-imginput" id="upp'+i+'"></p>' +
                                '<input type="hidden" name="updateSid" value="'+data.data.screen_id[i]+'">' +
                                "<p type='file' id='up"+i+"' class='Upload-imginput' onclick='uploud(this)'></p> " +
                                '<div class="progress-bar" step="0"></div>' +
                                '</div>' +
                                '<div class="sao_ma_box">' +
                                '<p class="sao_ma_num fsm"><input type="text" name="new" id="newDv'+i+'" value="'+newSolt[i]+'" disabled="disabled" placeholder="请扫描或填写设备软件编号" /></p>' +
                                '<span class="tip" style="visibility: hidden">设备未在系统中找到或未出库</span><span onclick="scan(this)" class="sao_ma sao_ma2">扫码</span>' +
                                '</div>' +
                                '</div>';

                        }else if(/android/.test(ua)) {
                            html = html+'<div class="upload_box">' +
                                '<div class="upload" id="screen'+i+'">' +
                                '<input id="panorama'+i+'" name="newpic" class="update_input" type="hidden" nullmsg="shopimage" datatype="*" value="'+data.data.images[i]+'">' +
                                '<img class="imgspace" src="/static/images/up_img.png">' +
                                '<input type="hidden" name="updateSid" value="'+data.data.screen_id[i]+'">' +
                                '<img class="addimg" src="'+data.data.images[i]+'">' +
                                '<p class="Upload-imginput" id="upp'+i+'"></p>' +
                                "<input type='file' id='up"+i+"' class='Upload-imginput' onclick='uploud(this)'> " +
                                '<div class="progress-bar" step="0"></div>' +
                                '</div>' +
                                '<div class="sao_ma_box">' +
                                '<p class="sao_ma_num fsm"><input type="text" name="new" id="newDv'+i+'" value="'+newSolt[i]+'" disabled="disabled" placeholder="请扫描或填写设备软件编号" /></p>' +
                                '<span class="tip" style="visibility: hidden">设备未在系统中找到或未出库</span><span onclick="scan(this)" class="sao_ma sao_ma2">扫码</span>' +
                                '</div>' +
                                '</div>';
                        }
                    }
                    $("#oldDv").append(oldDv);
                    $("#newpic").html(html);
                    $("#descript").html(data.data.problem_description);
                    $("#descript").attr('value',data.data.problem_description);
                    $('.sy_loadingpage').hide();
                    $('.sy-newunchecked').show();
                }else{
                    conftankuangparam("sy_examin_panel","数据有误,请重试！");
                    setTimeout(function(){location.href="/screen/screenshoplist?token=<?=$token?>"},2000);

                }
            }
        });
    });

    //二：异步提交数据
    //验证信息
    function tippanel(){
        $('.sy-installed-ts').show();
        setTimeout(function(){$('.sy-installed-ts').hide()},2000);
    }
    /*表单验证 shopimage */
    $("#idform").Validform({
        postonce:true,
        tiptype:function(msg,o,cssctl){
            if(msg=='problem_description'){
                conftankuangparam("sy_examin_panel",'问题描述不能为空');
            };
            if(msg=='shopimage'){
                conftankuangparam("sy_examin_panel",'屏幕图片不能为空');
            };
        },
        beforeSubmit:function(curform){
            var judedata = $('.sy-newinstalled').attr('data-v');
            if(judedata==0){
                //更换屏幕图片
                var new_images = [];
                $('input[name="newpic"]').each(function () {
                    pushArr(new_images,$(this).val());
                })
//                if(install_software_number.length != new_images.length){
//                    conftankuangparam("sy_examin_panel",'新增屏幕必须和图片数量一致');
//                    return false;
//                }
                //要更改的screenid
                var screenIds = [];
                $('input[name="updateSid"]').each(function () {
                    pushArr(screenIds,$(this).val());
                })
                //if(screenIds.)
                //1首先验证屏幕合法性
                $.ajax({
                    url:baseApiUrl+"/screen-change/change-new-update-post",
                    type:"post",
                    dataType:"json",
                    //async: false,//使用同步的方式,true为异步方式
                    data:{
                        id:'<?=$shopid?>',
                        token:'<?=$token?>',
                        replace_id:'<?=$replace_id?>',
                        new_images:new_images,
                        screenIds:screenIds,
                        //remove_device_number:remove_device_number,
                        //install_software_number:install_software_number,
                        //描述
                        //problem_description:$('#descript').val(),
                    },
                    success:function (phpdata) {
                        if(phpdata.status == 200){
                            location.href="/screen/screenshoplist?shopid=<?=$shopid?>&token=<?=$token?>&change=change&replace_id=<?=$replace_id?>&operate=2";
                        }else if(phpdata.status == 752){
                            if(phpdata.data.del.length > 0){
                                $.each(phpdata.data.del,function (i) {
                                    $('#old'+i).parent().parent().find('.red').css('visibility','visible');
                                })
                            }
                            if(phpdata.data.add.length > 0){
                                $.each(phpdata.data.add,function (j) {
                                    $('#newDv'+j).parents('.sao_ma_box').find('.tip').css('visibility','visible');
                                })
                            }
                            if(phpdata.data.alredy.length > 0){
                                $.each(phpdata.data.alredy,function (k) {
                                    $('#newDv'+k).parents('.sao_ma_box').find('.tip').html('该屏幕已经在店铺中存在').css('visibility','visible');
                                })
                            }
                            conftankuangparam("sy_examin_panel",phpdata.message);
                            return false;
                        }else{
                            conftankuangparam("sy_examin_panel",phpdata.message);
                        }
                    },error:function (phpdata) {

                    }
                });

            }
            return false;
        }

    })

    function tjData() {
        //2再提交
        $.ajax({
            url:baseApiUrl+"/screen-change/change-post-new",
            type:"post",
            dataType:"json",
            data:{
                id:'<?=$shopid?>',
                token:'<?=$token?>',
                replace_id:'<?=$replace_id?>',
                new_images:new_images,
                remove_device_number:remove_device_number,
                install_software_number:install_software_number,
                //现有屏幕数量
                now_screen_number:$('#current').val(),


            },
            success:function(data) {
                alert('成功')
                if(data.status==200){
                    location.href="/screen/screenwait?shopid=<?=$shopid?>&token=<?=$token?>&change=change&replace_id=<?=$replace_id?>";
                }else{
                    conftankuangparam("sy_examin_panel",data.message);
                }
            }
        });
        return false;
    }

    //数组压入
    function pushArr(arr,val) {
        if(!Array.isArray(arr) || !val){
            return false;
        }
        return arr.push(val);
    }

    function isInArray(arr,value){
        for(var i = 0; i < arr.length; i++){
            if(value === arr[i]){
                return true;
            }
        }
        return false;
    }


    //三：扫码
    function scan(q){
        var scanid=$(q).parent('.fsm').find('input').attr('id');
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
</script>
</body>
</html>