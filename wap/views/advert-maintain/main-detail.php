<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>维护信息</title>
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
        .old_equipment{margin-top: 14px;margin-bottom: 4rem}
        .old_equipment_num{border-left: 4px solid #ff7d09;}
        .old_equipment p{padding: 16px 12px;background: #fff;margin-bottom: 1px;overflow: hidden;}
        .sao_ma{position: absolute; right: 0;margin-right: 12px; color: #ff7d09; border: 1px #ff7d09 solid; padding: 2px 10px; border-radius: 10px;}
        .remarks_col{color: #999999;}
        .old_equipment_num{padding-left: 10px;}
        .wenti{ height: 100px; border: 0;}
        .upload_box{background: #fff; overflow: hidden; padding-top: 12px; padding-bottom: 12px; padding-left: 12px;margin-bottom: 1px;}
        .sao_ma_box{float: right;width: 66%;position: relative;padding-top: 0 !important;font-size: 12px}
        .old_equipment .sao_ma_num{padding: 0 12px;color: #999999;    margin-top: 14px;padding-left: 0}
        .tip{color: red}
        .upload_box{padding-right: 12px}
        .old_equipment div{padding: 16px 12px; background: #fff; margin-bottom: 1px; overflow: hidden;}
        .red{color: red; visibility: hidden;    padding-bottom: 0 !important;    clear: both;}
        .progress-bar{opacity: 0}
        .saomabox{position: relative}
        .sao_ma2{margin-right: 0; margin-top: -9%; z-index: 9;}
        #old0,#newDv0{font-size: 12px;width: 86%;}
        .go_stats{color: #f86908;font-weight: bold;padding:0 !important;}
        .go_wei{color: #666;font-weight: bold;padding:0 !important;}
        .nub_tt{display: block}
        .old_equipment .sao_ma_num{margin-top: 0}
    </style>
</head>
<body >
<form method="post" name="form" id="idform" action="<?=Yii::$app->params['baseApiUrl'].'/screen-advert-maintain/save-info'?>">
    <input type="hidden" name="id" value="<?=$id?>">
    <input type="hidden" name="shop_id" value="<?=$shop_id?>">
    <input type="hidden" name="mongo_id" value="<?=$mongo_id?>">
    <input type="hidden" name="token" value="<?=$token?>">

    <div class="old_equipment" id="newpic" style="margin-bottom: .5rem">
        <p class="old_equipment_tt">
            <span class="old_equipment_num">维护设备编号</span><span class="remarks_col"></span>
        </p>
    </div>
    <div class="old_equipment">
        <p class="old_equipment_tt"><span class="old_equipment_num">问题描述</span><span class="remarks_col">（选填）</span></p>
        <p><textarea class="wenti" name="problem_description" rows="" id="descript" cols="" placeholder="请填写问题描述" ></textarea></p>
    </div>
    <br /><br /><br /><br /><br />
    <button id="look" class="submission" style="bottom: 20%" type="button">查看广告订单进度</button>
    <button id="save" class="submission" style="bottom: 10%" type="button">保存维护进度</button>
    <button class="submission sy-newinstalled" type="submit">确认提交</button>
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

<script src="/static/js/jquery-1.7.2.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="/static/js/jQuery.validate.min.js" ></script>
<script type="text/javascript" src="/static/js/cos-js-sdk-v4.js" ></script>
<script type="text/javascript" src="/static/js/mobile-adapt.js"></script>
<script type="text/javascript" src="/static/js/exif.js?v=1.2"></script>
<script type="text/javascript" src="/static/js/tankuangpub.js" ></script>
<script src="/static/js/img-upload.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    function ajaxFun() {
        $.ajax({
            url:baseApiUrl+"/screen-advert-maintain/get-mongo-info",
            type:"get",
            dataType:"json",
            //async:false,
            data:{id:<?=$id?>,shop_id:<?=$shop_id?>,mongo_id:'<?=$mongo_id?>',token:'<?=$token?>'},
            success:function(data) {
                if(data.status==200 && data.data.now_number > 0){
                    var html='<p class="old_equipment_tt"><span class="old_equipment_num">维护设备编号</span><span class="remarks_col"></span></p>';
                    var eachNumber = data.data.software_number;
                    $.each(eachNumber,function (i,v) {
                        //状态
                        var go_status= '';
                        if(v.date){
                            go_status = '<p class="go_stats" snum="'+v.number+'">已到达</p>';
                        }else {
                            go_status = '<p class="go_wei" snum="'+v.number+'">未到达</p>';
                        }
                        //图片
                        var imgspacescr = '/static/images/up_img.png';
                        var addimgsr = '/static/images/up_img.png';
                        var hidden_pic = '';
                        if(v.pic){
                            imgspacescr = v.pic;
                            addimgsr = v.pic;
                            hidden_pic = v.pic;
                        }
                        //2. new device number
                        var ua = navigator.userAgent.toLowerCase();
                        if (/iphone|ipad|ipod/.test(ua)) {
                            html = html+'<div class="upload_box">' +
                                '<div class="upload" id="upload'+i+'">' +
                                '<input id="panorama'+i+'" name="newpic[]" class="update_input" type="hidden"  value="'+hidden_pic+'">' +
                                '<img class="imgspace" src="'+imgspacescr+'">' +
                                '<img class="addimg" src="'+addimgsr+'">' +
                                '<p class="Upload-imginput" id="upp'+i+'"></p>' +
                                "<p type='file' id='up"+i+"' class='Upload-imginput' onclick='uploud(this)'></p> " +
                                '<div class="progress-bar" step="0"></div>' +
                                '</div>' +
                                '<div class="sao_ma_box">' +go_status+
                                '<p class="sao_ma_num fsm"><span class="nub_tt">硬件编号:</span><span>'+v.device_number+'</span></p>' +
                                '<p class="sao_ma_num fsm"><span class="nub_tt">软件编号:</span><span>'+v.number+'</span></p>' +
                                '</div>' +
                                '</div>';

                        }else if(/android/.test(ua)) {
                            html = html+'<div class="upload_box">' +
                                '<div class="upload" id="screen'+i+'">' +
                                '<input id="panorama'+i+'" name="newpic[]" class="update_input" type="hidden" value="'+hidden_pic+'" nullmsg="shopimage" datatype="*">' +
                                '<img class="imgspace" src="'+imgspacescr+'">' +
                                '<img class="addimg" src="'+addimgsr+'">' +
                                '<p class="Upload-imginput" id="upp'+i+'"></p>' +
                                "<input type='file' id='up"+i+"' class='Upload-imginput' onclick='uploud(this)'> " +
                                '<div class="progress-bar" step="0"></div>' +
                                '</div>' +
                                '<div class="sao_ma_box">' +go_status+
                                '<p class="sao_ma_num fsm"><span class="nub_tt">硬件编号:</span><span>'+v.device_number+'</span></p>' +
                                '<p class="sao_ma_num fsm"><span class="nub_tt">软件编号:</span><span>'+v.number+'</span></p>' +
                                '</div>' +
                                '</div>';
                        }

                    })
                    $('#descript').val(data.data.problem_description);
                    $("#newpic").html(html);
                    $('.sy_loadingpage').hide();
                    $('.sy-newunchecked').show();
                }else{
                    //conftankuangparam("sy_examin_panel","数据有误,请重试！");
                    //setTimeout(function(){location.href="/screen/screenshoplist?token=<?=$token?>"},2000);

                }
            }
        });
    }
    function getSaveInfo() {
        var saveInfo = [];
        $(".upload_box").each(function (k) {
            soft = $(this).find('.nub_tt:last').next().html();
            pic = $(this).find('[name="newpic[]"]').val();
            if(soft && pic){
                var current = {
                    'number':soft,
                    'pic':pic
                }
                saveInfo[k] = current
            }
        })
        return saveInfo;
    }
    function tippanel(){
        $('.sy-installed-ts').show();
        setTimeout(function(){$('.sy-installed-ts').hide()},2000);
    }
    //一：加载数据
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    $(function(){
        //初始化
        ajaxFun();
        //first: 获取最新到达情况
        $("#look").bind('click', function () {
            ajaxFun();

        })
        //second: 保存维护进度
        $("#save").bind('click', function () {
            //获取图片、软件编号
            var saveInfo = getSaveInfo();
            //获取问题描述
            var pro = $("#descript").val();
            if(!pro){pro = ' ';}
            var problem_description =  pro;
            $.ajax({
                url:baseApiUrl+"/screen-advert-maintain/save-temp-info",
                type:"get",
                dataType:"json",
                data:{id:<?=$id?>,images:saveInfo,problem_description:problem_description,token:'<?=$token?>'},
                success:function (phpdata) {
                    if(phpdata.status == 200){
                        conftankuangparam("sy_examin_panel","保存成功！");
                        setTimeout(function(){location.href="/screen/screenshoplist?token=<?=$token?>"},2000);
                    }
                },error:function () {
                    conftankuangparam("sy_examin_panel","保存失败！");
                }
            })
        })
    });
    //二：异步提交数据
    /*表单验证*/
    $("#idform").Validform({
        postonce:true,
        tipSweep:true,
        tiptype:function(msg,o,cssctl){
            if(msg=='shopimage'){
                conftankuangparam("sy_examin_panel",'屏幕图片不能为空');
            };
        },
        beforeSubmit:function(form){
            var img = JSON.stringify(getSaveInfo());
            $('#idform').append("<input type='hidden' name='images'  value='"+img+"'>");
        },
        ajaxPost:true,
        callback:function (data) {
                if(data.status == 200){
                    if(data.data){
                        var eachNumber = data.data.software_number;
                        if(eachNumber){
                            var falg = 0;
                            if(eachNumber){
                                $.each(eachNumber,function (k,v) {
                                    if(!v.date){
                                        falg = 1;
                                        $('[snum="'+v.number+'"]').css('color','red');
                                    }
                                })
                            }
                            if(falg == 1){
                                conftankuangparam("sy_examin_panel",'请下载广告节目单之后提交');
                                return false;
                            }
                        }
                    }else{
                        $('.sy-installed-ts').text('提交维护信息成功');
                        setTimeout(function(){location.href="/screen/screenshoplist?token=<?=$token?>"},2000);
                        tippanel();
                    }


                }else{
                    $('.sy-installed-ts').text(data.message);
                    setTimeout(function(){location.href="/screen/screenshoplist?token=<?=$token?>"},2000);
                    tippanel();
                    return false;
                }
                return false;
        }
    })

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
        if($(q).attr('data-type') == 'old'){
            var scanid=$(q).parent().prev().find('input').attr('id');
        }else{
            var scanid=$(q).prev().prev().find('input').attr('id');
        }
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