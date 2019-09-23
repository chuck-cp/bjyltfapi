<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>屏幕拆除</title>
    <link rel="stylesheet" type="text/css" href="/static/css/reset.css?v=181221"/>
	<link rel="stylesheet" type="text/css" href="/static/css/wait_examine.css?v=181221">
    <style type="text/css">
        body{background: #f0f0f0;}
        .shop_info{margin-top: 4%;margin-bottom: 80px;}
        .shop_info li{padding-top: 14px;padding-bottom: 14px;    margin-bottom: 1px;background: #fff;padding-left: 2%; padding-right: 2%;overflow: hidden;font-size: 14px;}
        .shop_info b{float: left;font-weight: normal;}
        .shop_info span{float: right;width: 70%;text-align: right;color: #999999;}
        .submission{position: fixed; background: #f86908; font-size: 16px; bottom: 0; color: #fff; width: 100%; text-align: center; padding-top: 16px; padding-bottom: 16px;}
        .question_answer{padding-top: 12px;padding-bottom: 44px; }
        .text_area{margin-top: 14px; border: 0; min-height: 80px; width: 100%;}
        .shop_info .sweep_code{width: 15%; color: #ff7d09; border: 1px #ff7d09 solid; border-radius: 35px; text-align: center;}
        /*.sy_loadingpage{display: none;}*/
        /* 提示弹框 */
        .sy-installed-ts{top: 14%;}
		#remove0{font-size: 12px;width: 160%;}
    </style>
</head>
<body >
<form action="" id="remove_form">
    <ul class="shop_info">
        <li><b>店铺名称</b><span id="sname">木北造型长阳店</span></li>
        <li style="margin-bottom:0"><b>店铺地址</b><span id="area">北京市-丰台区-航丰路</span></li>
        <li><b></b><span id="address">航丰路1号时代财富天地B座2015室</span></li>
        <li class="dplx"><b>店铺联系人</b><span id="contacts_name">吴彦祖</span></li>
        <li class="dplx"><b>手机号码</b><span id="contacts_mobile">18888888888</span></li>
        <li><b>业务合作人</b><span id="member">吴彦祖</span></li>
        <li><b>联系电话</b><span id="member_mobile">18888888888</span></li>
        <!-- 拆除设备编号 S  -->
<!--        <li style="margin-bottom:0"><b>拆除设备编号</b><span></span></li>-->
        <!-- 拆除设备编号 E -->
        <li>
            <p>问题描述</p>
            <textarea id="problem" class="text_area" rows="" cols="" placeholder="请填写问题描述 (选填)"></textarea>
        </li>

    </ul>
    <button class="submission" type="submit">确认拆除</button> <!-- 这里闪现红色 确认拆除 按钮 -->
</form>
 <div class="yx_shehe_jg" id="daishenhe">
        <a href="javascript:void(0);" class="yx_shenghe_hui">待审核</a>
</div> 
<p class="sy-installed-ts">提交失败</p>
<div class="sy_examin_panel">
    <div class="img">
        <div class="examcon">
            <p class="con">提交成功等待审核</p>
            <p class="btn"><a href="javascript:void(0);" onclick="closetankuan('sy_examin_panel')">确定</a></p>
        </div>
    </div>
</div>
<script src="/static/js/jquery-1.7.2.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript" src="/static/js/jQuery.validate.min.js" ></script>
<script type="text/javascript" src="/static/js/tankuangpub.js" ></script>
<script>
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    function tippanel(){
        $('.sy-installed-ts').show();
        setTimeout(function(){$('.sy-installed-ts').hide()},2000);
    }
    $(function(){
        //1加载信息

        $.ajax({
            url:baseApiUrl+"/screeninstall/remove-screen-info",
            type:"get",
            dataType:"json",
            data:{
                shop_id:<?=$shopid?>,
                token:'<?=$token?>',
                id:'<?=$replace_id?>'
            },
            success:function (phpdata) {
                if(phpdata.status == 200){
                    $('.sy_loadingpage').css({'visibility':'hidden'});
                    $('#sname').html(phpdata.data.shop_name);
                    $('#area').html(phpdata.data.shop_area_name);
                    $('#address').html(phpdata.data.shop_address);
                    $('#member').html(phpdata.data.member_name);
                    $('#member_mobile').html(phpdata.data.member_mobile);
                    $('#contacts_mobile').html(phpdata.data.contacts_mobile);
                    $('#contacts_name').html(phpdata.data.contacts_name);
                    //待拆除
                    if(phpdata.data.status == 1){
                        $('#daishenhe').hide();
                        var sli = '<li style="margin-bottom:0"><b>拆除设备编号</b><span></span></li>';
                        for (var i=0; i<phpdata.data.replace_screen_number; i++){
                            sli += '<li><b><input type="text" name="delscreen" id="remove'+i+'" value="" placeholder="请扫码或填写设备硬件编号"  class="screen_text_list"/></b><span class="sweep_code" onclick="scan(this)">扫码</span><p style="clear: both;"><span class="tip" style="visibility: hidden;float: left; text-align: left;color: red;">设备未在系统中找到或未出库</span></p></li>';
                        }
                        $('.shop_info li:last').before(sli);
                    }
                    //待审核或者审核失败
                    if(phpdata.data.status == 2 || phpdata.data.status == 3){
                        $('.submission').remove();
                        $('.dplx').remove();
                        var contactHtml = '<li><b>店铺联系人</b><span>'+phpdata.data.contacts_name+'</span></li><li><b>手机号码</b><span>'+phpdata.data.contacts_mobile+'</span></li>';
                        //alert(contactHtml)
                        $('.shop_info li:eq(2)').after(contactHtml);
                        var deviceArr = phpdata.data.remove_device_number.split(',');
                        var sli = '';
                        for (var i=0; i<deviceArr.length; i++){
                            if(i == 0){
                                sli += '<li><b>拆除设备编号</b><span class="">'+deviceArr[i]+'</span></li>';
                            }else {
                                sli += '<li><b></b><span>'+deviceArr[i]+'</span></li>';
                            }

                        }
                        $('.shop_info li:last').before(sli);
                        $('#problem').replaceWith('<p class="question_answer">'+phpdata.data.problem_description+'</p>');
                    }

                }else{
                    conftankuangparam("sy_examin_panel","数据有误,请重试！");
                }
            }

        });


        //2提交拆屏
        $('#remove_form').Validform({
            postonce:true,
            tiptype:function(msg,o,cssctl){
                if(msg == 'problem_description'){
                    conftankuangparam("sy_examin_panel",'问题描述不能为空');
                };
            },
            beforeSubmit:function(curform){
                var delArr = [];
                //设备码不能重复
                $('input[name="delscreen"]').each(function (i) {
                    if(isInArray(delArr,$(this).val())){
                        //$(this).css('border','2px red solid');
                        $(this).parents('li').find('.tip').html('屏幕编号不能重复').css('visibility','visible');
                        return false;
                    }
                    if($(this).val()){
                        delArr.push($(this).val());
                    }
                })
                if(delArr.length < 1){
                    conftankuangparam("sy_examin_panel",'拆除屏幕编号不能为空');
                    return false;
                }
                //先检验要拆除的设备编码是否在店铺中存在
                $.ajax({
                    url:baseApiUrl+'/screeninstall/remove-screen-check',
                    type:"post",
                    dataType:"json",
                    data:{
                        id:'<?=$replace_id?>',
                        shop_id:'<?=$shopid?>',
                        token:'<?=$token?>',
                        remove_device_number:delArr,
                        problem_description:$('#problem').val(),
                    },
                    success:function (phpdata) {
                        console.log(phpdata);
                        if(phpdata.status == 200){
                            conftankuangparam("sy_examin_panel",'提交成功，等待审核');
                            setTimeout("tz()",2000);
                        }else if(phpdata.status == 752){
                            if(phpdata.data.length > 0){
                                $.each(phpdata.data,function (i,value) {
                                    $("#remove"+i+"").parent().next().next().find('.tip').css('visibility','visible');
                                })
                            }
                        }else{
                            conftankuangparam("sy_examin_panel",phpdata.message);
                            setTimeout("tz()",2000);
                        }
                    }
                })
                return false;
            }
        });



    });
    function tz() {
        window.location.href = '/screen/screenshoplist?token=<?=$token?>';
    }
    function isInArray(arr,value){
        for(var i = 0; i < arr.length; i++){
            if(value === arr[i]){
                return true;
            }
        }
        return false;
    }
    function scan(q){
        var scanid=$(q).prev().find('input').attr('id');
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
</body>
<div class="sy_loadingpage">
    <img src="/static/images/loading.gif">
    <p>正在加载...</p>
</div>
</html>