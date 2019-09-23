<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>公司信息填写</title>
    <link rel="stylesheet" type="text/css" href="/static/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/static/css/logo_install.css">
    <link rel="stylesheet" type="text/css" href="/static/css/swiper.min.css">
    <link rel="stylesheet" type="text/css" href="/static/css/apply.css">
    <link rel="stylesheet" type="text/css" href="/static/css/tab_change.css"/>
</head>
<body>
<form id="idform">
    <!-- 店铺信息填写S -->
    <ul class="shop_info">
        <li><b class="mark_tt">公司名称信息</b><span></span></li>
        <li>
            <b>公司名称</b>
            <span>
                        <input type="text" name="" id="zhuzl" value="" placeholder="请填写公司名称" autocomplete="off" datatype="*" nullmsg="office_building">
                    </span>
        </li>
        <li style="margin-bottom: 0;">
            <b>公司地址</b>
            <span id="azdz">
                    <input type="hidden" id="area" placeholder="请选择公司地区" nullmsg="installation_address" datatype="*" value="">
                    <i id="area_name" class="yx_srnr_dqu">请选择公司地区</i>
                </span>
        </li>
        <li>
            <b>&nbsp;</b>
            <span>
                    <input type="text" name="" id="address" value="" placeholder="请填写详细地址" autocomplete="off" datatype="*" nullmsg="detailed_address">
                </span>
        </li>
        <li><b>统一社会信用代码</b><span><input type="text" name="" id="company" value="" placeholder="请填写社会信用代码" autocomplete="off" datatype="*" nullmsg="property_company"></span></li>
        <li><b>申请人</b><span><input type="text" name="" id="applier" value="" placeholder="请填写联系人名称" autocomplete="off" datatype="*" nullmsg="contact_person"></span></li>
        <li style="position: relative;">
            <b>申请人电话</b>
            <span>
                    <input class="per_info_mobi" style="margin-top: -1%;" type="number" name="" id="apply_mobile" value="" placeholder="请填写联系人电话" autocomplete="off" maxlength="11" datatype="*" nullmsg="telephone_number">
					<input type="button" value="发送验证码" onclick="settime(this)" class="yanzhenma">
				</span>
        </li>
        <li><b>验证码</b><span><input type="text" name="" id="verify" value="" placeholder="请输入验证码" autocomplete="off" datatype="*" nullmsg="contact_person_1"></span></li>
        <li><b>备注（选填）</b><span><input type="text" name="" id="description" value="" placeholder="请填写备注信息" autocomplete="off"></span></li>
    </ul>
    <ul class="shop_info img_up_ul">
        <li class="screen_info" style="position: relative;"><b class="mark_tt">上传照片</b><span></span></li>
        <li>
            <b>申请人（选填）<br/><i style="color: #999999;font-size: 12px;">请按照示例上传清晰的身份证照片</i></b>

            <span>
                    <label>
                        <img class="Upload-imginput" src="/static/image/addphoto.png">
                        <input type="hidden" id="indent_front" value="">
						<img class="Upload-imginput" src="/static/image/addphoto.png">
                        <input type="hidden" id="indent_black" value="">
                        <input hidden="hidden" type="file" name="" id="" value="">
                    </label>
                </span>
        </li>
        <li>
            <b>请上传清晰的营业执照</b>

            <span>
                    <label>
                        <img class="Upload-imginput" src="/static/image/addphoto.png">
                        <input type="hidden" id="biz_img" datatype="*" value="" nullmsg="biz_img">
                        <input hidden="hidden" type="file" name="" id="" value="">
                    </label>
                </span>
        </li>
        <li>
            <p>其他（选填，最多上传5张）</p>
            <br>
            <div class="yx_sc_img">
                <div id="upload_other_image" class="sy_authorcertif">
                    <!-- 重要 不加这段代码，增加按钮失效 -->
                    <div class="upload" id="screen15" style="display: none;">
                        <img class="imgspace" src="/static/image/blank.jpg">
                        <img class="addimg" src="/static/image/addphoto_big.png">
                        <input type="file" class="Upload-imginput" accept="image/*" id="up15">
                        <input class="update_input" id="screen100" type="hidden">
                        <div class="progress-bar" step="0"></div>
                        <p class="syred_upload_pic"><img src="/static/image/sy_sqfdreducs.png"></p>
                    </div>
                    <!-- 重要 不加这段代码，增加按钮失效  -->
                    <div class="upload" id="screen16">
                        <img class="imgspace" src="/static/image/blank.jpg">
                        <img class="addimg" src="/static/image/addphoto_big.png">
                        <input type="file" class="Upload-imginput" accept="image/*" id="up16">
                        <input class="update_input" id="screen101" type="hidden">
                        <div class="progress-bar" step="0"></div>
                        <p class="syred_upload_pic"><img src="/static/image/sy_sqfdreducs.png"></p>
                    </div>
                    <p class="syadd_upload_pic" name="other_upload_image">
                        <img class="imgspace" src="/static/image/blank.jpg">
                        <img class="img" src="/static/image/sy_sqfdadd.png">
                    </p>
                </div>
            </div>
        </li>
    </ul>


    <p class="sure_box"><button class="sure_btn" type="submit">确认提交</button></p>
    <br>
    <br>
    <br>
    <!-- 店铺信息填写E -->
</form>
<!-- 背景遮罩 -->
<div class="mask" style="opacity: 0.3; display: none; height: 1310px;"></div>
<!--提交失败提示-->
<p class="sy-installed-ts">提交失敗</p>
<p class="error_time">屏幕開機時間不得小於8小時</p>
<!--开关机选择弹框-->
<div class="riqi-select" id="start" style="bottom: -999px;">
    <div class="select-select">
        <p class="select-cancel">取消</p>
        <button class="select-confirm">確定</button>
        <span class="line"></span>
    </div>
    <div class="riqi-default">:</div>
    <!--分-->
    <div class="year-date">
        <div class="swiper-container swiper-container-vertical swiper-container-free-mode">
            <div class="swiper-wrapper" style="transform: translate3d(0px, 0px, 0px);"><div class="swiper-slide swiper-slide-prev" data-year="8" style="height: 40px;">08</div><div class="swiper-slide swiper-slide-active" data-year="9" style="height: 40px;">09</div><div class="swiper-slide swiper-slide-next" data-year="10" style="height: 40px;">10</div><div class="swiper-slide" data-year="11" style="height: 40px;">11</div><div class="swiper-slide" data-year="12" style="height: 40px;">12</div><div class="swiper-slide" data-year="13" style="height: 40px;">13</div></div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
    <!--秒-->
    <div class="mouth-date">
        <div class="swiper-container swiper-container-vertical swiper-container-free-mode">
            <div class="swiper-wrapper" style="transform: translate3d(0px, 0px, 0px);"><div class="swiper-slide swiper-slide-prev" data-month="0" style="height: 40px;">00</div><div class="swiper-slide swiper-slide-active" data-month="10" style="height: 40px;">10</div><div class="swiper-slide swiper-slide-next" data-month="20" style="height: 40px;">20</div><div class="swiper-slide" data-month="30" style="height: 40px;">30</div><div class="swiper-slide" data-month="40" style="height: 40px;">40</div><div class="swiper-slide" data-month="50" style="height: 40px;">50</div></div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>
<!--开关机结束-->
<!-- 国家区号选择 S-->
<div class="area_sel">
    <div class="area_sel_box">
        <ul class="area_sel_ul">
            <li><label class="area_label"><span>中国大陆</span><i class="area_val">+86</i><input class="area_sel_input" type="radio" name="area_sel_cur" id="" value=""></label></li>
            <li><label class="area_label"><span>中国香港</span><i class="area_val">+852</i><label><input class="area_sel_input" checked="checked" type="radio" name="area_sel_cur" id="" value=""></label></label></li>
        </ul>
        <p class="btns_p"><button class="cancel_btn" type="button">取消</button><button class="confirm_btn" type="button">確定</button></p>
    </div>
</div>
<!-- 国家区号选择 E-->
<div id="areaMask" class="dzmask"></div>
<section id="areaLayer" class="express-area-box">
    <header>
        <h3>选择地区</h3>
        <a id="backUp" class="back" href="javascript:void(0)" title="返回"></a>
        <a id="closeArea" class="close" href="javascript:void(0)" title="关闭"></a>
    </header>
    <article id="areaBox">
        <ul id="areaList" class="area-list">
        </ul>
    </article>
</section>

<input type="hidden" name="yzmtk" id="yzmtk" value="">
<script type="text/javascript">
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    var token= "<?=$token?>";
</script>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jQuery.validate.min.js"></script>
<script type="text/javascript" src="/static/js/tip/tip.js"></script>
<script type="text/javascript" src="/static/js/tip/getpics.js"></script>
<script type="text/javascript" src="/static/js/cos-js-sdk-v4.js"></script>
<script type="text/javascript" src="/static/js/mobile-adapt.js"></script>
<script type="text/javascript" src="/static/js/exif.js"></script>
<script type="text/javascript" src="/static/js/img-upload.js"></script>
<script type="text/javascript" src="/static/js/jquery.area.js"></script>
<script type="text/javascript" src="/static/js/select_time.js"></script>
<script type="text/javascript" src="/static/js/swiper.min.js"></script>
<script type="text/javascript" src="/static/js/tab_change.js"></script>
<script type="text/javascript" src="/static/js/verify/verify.js"></script>
<script type="text/javascript" src="/static/js/verify/check.js"></script>



<script type="text/javascript">
/*选择省市地区 */
$(function (){
    $("#area_name").click(function (){
        zxcs("azdz")
    })
})
//选择时间错误弹框
function err_time(){
    $('.error_time').show();
    setTimeout(function(){$('.error_time').hide()},2000);
}

$("#idform").Validform({
    tipSweep:true,
    tiptype:function(msg,o,cssctl){
        if(msg=='office_building'){
            $('.sy-installed-ts').text('公司名称不能为空');
            tippanel();
        };
        if(msg=='installation_address'){
            $('.sy-installed-ts').text('公司地址不能为空');
            tippanel();
        };
        if(msg=='detailed_address'){
            $('.sy-installed-ts').text('详细地址不能为空');
            tippanel();
        };
        if(msg=='property_company'){
            $('.sy-installed-ts').text('社会信用代码不能为空');
            tippanel();
        };
        if(msg=='contact_person'){
            $('.sy-installed-ts').text('申请人名称不能为空');
            tippanel();
        };

        if(msg=='telephone_number'){
            $('.sy-installed-ts').text('申请人电话不能为空');
            tippanel();
        };
        if(msg=='contact_person_1'){
            $('.sy-installed-ts').text('验证码不能为空');
            tippanel();
        };
        if(msg=='biz_img'){
            $('.sy-installed-ts').text('营业执照不能为空');
            tippanel();
        };


    },
    beforeSubmit:function(curform){
        var flag = false;
        var verifyCode = $("#verify").val();
        var mobile = $("#apply_mobile").val();
        var verifyUrl = baseApiUrl+"/verifyCode/"+verifyCode+"/mobile/"+mobile;
        $.ajax({
            url: verifyUrl,
            type: "GET",
            async: false,
            success:function (phpdata) {
                if(phpdata){
                    //$("#idform").append('<input type="hidden" id="code" name="verifyCode" value="'+phpdata+'" >');
                }
                if(!phpdata){
                    $('.sy-installed-ts').text('手机验证码不正确！');
                    flag = true;
                    tippanel();
                    return false;
                }
            },error:function (phpdata) {
                $('.sy-installed-ts').text('手机验证码验证失败0！');
                flag = true;
                tippanel();
                return false;
            }

        });
        if(flag){
            $('.sy-installed-ts').text('手机验证码验证失败1！');
            tippanel();
        }
        var name = $('#zhuzl').val();
        var area = $('#area').val();
        var address = $('#address').val();
        var registration_mark = $('#company').val();
        var apply_name = $('#applier').val();
        var apply_mobile = $('#apply_mobile').val();
        var description = $('#description').val();
        var identity_card_front = getpics('#indent_front',1);
        var identity_card_back = getpics('#indent_black',1);
        var postData = {
            'company_name':name,
            'area_id':area,
            'address':address,
            'registration_mark':registration_mark,
            'apply_name':apply_name,
            'apply_mobile':apply_mobile,
            'description':description,
            'identity_card_front':identity_card_front,
            'identity_card_back':identity_card_back,
            'business_licence':getpics('#biz_img', 1),
            'other_image':getpics('.update_input', 1),
        };
        //console.log(postData);return;
        $.ajax({
            url: baseApiUrl+"/build-wap/create-company?token="+token,
            type: "POST",
            data:postData,
            async: false,
            success:function (phpdata) {
                if(phpdata.status == 200){
                    $('.sy-installed-ts').text('提交成功，等待审核中');
                    tippanel("/inner-install/choose?token="+token);
                }else{
                    $('.sy-installed-ts').text(phpdata.message);
                    tippanel();
                }
            },error:function (phpdata) {
                $('.sy-installed-ts').text('提交失敗');
                tippanel();
            }

        });
    },
    callback:function(form){
        return false;
    },
    datatype:{
        "IDcard":/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[0-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/i,
        "phone":/^1[34578]\d{9}$/,
        'j_cont':/^[1-9]$|^[1-9]\d$|^[1-9]\d{2}$|^[1-9]\d{3}$/
    }
})


</script>
<script type="text/javascript">
    /*请上传授权证明  点击加号显示授权证明上传框*/
    $('.syadd_upload_pic').click(function(){
        //默认图片个数
        var inputleg=$(this).parents('.sy_authorcertif').find('.upload').length;
        //获取上传证明 upload的id 及Upload-imginput的id并赋值
        //当前点击时的id值
        var uploadid=$(this).parents('.sy_authorcertif').find('.upload').eq(inputleg-1).attr('id');
        //当前点击时的id值【除去数字】
        var uploadpre=uploadid.substr(0,uploadid.length-1);
        //当前点击的id值的最后一个数字+1
        var uploadidlast=parseInt(uploadid.substr(uploadid.length-1,1))+1;
        //重新生成一个id值  当前点击的值的id数+1
        var newuploadid=uploadpre+uploadidlast;
        //当前点击的 Upload-imginput【添加图片层】的id
        var upid=$(this).parents('.sy_authorcertif').find('.upload').eq(inputleg-1).find('.Upload-imginput').attr('id')
        //当前点击的 Upload-imginput【添加图片层】的id除去最后一个数字 id="up1*"
        var upidpre=upid.substr(0,upid.length-1);
        //当前点击的 Upload-imginput【添加图片层】当前点击的id值的最后一个数字+1
        var upidlast=parseInt(upid.substr(upid.length-1,1))+1;
        //当前点击的 Upload-imginput【添加图片层】重新生成一个id值  当前点击的值的id数+1
        var newupid=upidpre+upidlast;
        //authoriz_image1
        // <input class="update_input" id="other_image1" type="hidden">  这个的id号+1
        var update_inputid=$(this).parents('.sy_authorcertif').find('.upload').eq(inputleg-1).find('.update_input').attr('id')
        var update_inputidpre=update_inputid.substr(0,update_inputid.length-1);
        var update_inputidlast=parseInt(update_inputid.substr(update_inputid.length-1,1))+1;
        var newupdate_inputid=update_inputidpre+update_inputidlast;

        if(inputleg<6){
            if($(this).attr('name') == 'other_upload_image'){
                var inputHtml = '<input class="update_input" id="'+newupdate_inputid+'" type="hidden">';
            }else{
                var inputHtml = '<input class="update_input" id="'+newupdate_inputid+'" type="hidden" placeholder="請上傳圖片" nullmsg="authoriz_image" datatype="*">';
            }
            $(this).parents('.sy_authorcertif').find('.syadd_upload_pic').before('<div class="upload" id="'+newuploadid+'">'
                +'<img class="imgspace" src="/static/image/blank.jpg"><img class="addimg" src="/static/image/addphoto_big.png">'
                +'<input type="file" class="Upload-imginput" accept="image/*" id="'+newupid+'">'
                + inputHtml
                +'<div class="progress-bar" step="0"></div><p class="syred_upload_pic"><img src="/static/image/sy_sqfdreducs.png"></p></div>')
            var lolg=$(this).parents('.sy_authorcertif').find('.upload').length;
            if(lolg==6){
                $(this).parents('.sy_authorcertif').find('.syadd_upload_pic').hide();
            }
        }
    })
    //减号 删除添加的授权证明图片
    $('.syred_upload_pic').live('click',function(){
        var lolg=$(this).parents('.sy_authorcertif').find('.upload').length;
        if(lolg<7){
            $(this).parents('.sy_authorcertif').find('.syadd_upload_pic').show();
        }
        $(this).parent('.upload').remove();
    })


    //图片上传Upload-imginput
    $('.Upload-imginput').live('click',function(){
        //uploadimg('Upload-imginput');
        var ua = navigator.userAgent.toLowerCase();
        if(/android/.test(ua)) {
            //var input_id = $(this).next().next().attr('id');
            var input_id = $(this).next().attr('id');
            var result = {"action":input_id}
            window.jsObj.HtmlcallJava(JSON.stringify(result));
        }else{
            //var input_id = $(this).next().next().attr('id');
            var input_id = $(this).next().attr('id');
            var result = {"action":input_id};
            webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
        }
    })
</script>




</body></html>