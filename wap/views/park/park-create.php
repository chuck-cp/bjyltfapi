<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>公园资料上传</title>
    <link rel="stylesheet" type="text/css" href="/static/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/static/css/logo_install.css">
    <link rel="stylesheet" type="text/css" href="/static/css/swiper.min.css">
    <link rel="stylesheet" type="text/css" href="/static/css/apply.css">
    <link rel="stylesheet" type="text/css" href="/static/css/tab_change.css" >
</head>
<body>
<form id="idform">
    <!-- 店铺信息填写S -->
    <ul class="shop_info" id="list_box">
        <li><b class="mark_tt">公园信息</b><span></span></li>
        <li class="equipment_type" id="inc_info">
            <b>公司名称</b>
            <span>
				<input class="user_result"  type="text" value="" focus_bool="false" disabled="disabled" onfocus="document.activeElement.blur()" placeholder="选择公司信息"  datatype="*" nullmsg="userResult">
                <input type="hidden" id="company_name" name="company_name" value="">
					</span>
        </li>
        <li>
            <b>公园联系人</b>
            <span>
						<input type="text" name="" id="contact_name" value="" placeholder="填写公园联系人" autocomplete="off" datatype="*" nullmsg="office_building">
					</span>
        </li>
        <li>
            <b>联系人电话</b>
            <span>
						<input type="number" name="" id="contact_mobile" value="" placeholder="填写公园联系人电话" autocomplete="off" datatype="*" nullmsg="telephone_number">
					</span>
        </li>
        <li>
            <b>公园名称</b>
            <span>
						<input type="text" name="" id="shop_name" value="" placeholder="填写公园名称" autocomplete="off" datatype="*" nullmsg="property_company">
					</span>
        </li>
        <li style="margin-bottom: 0;">
            <b>安装地址</b>
            <span id="azdz">
					    <input type="hidden" id="area" placeholder="请选择安装地址" nullmsg="installation_address" datatype="*" value="">
					    <i id="area_name" class="yx_srnr_dqu">请选择安装地址</i>
					</span>
        </li>
        <li>
            <b>&nbsp;</b>
            <span>
					    <input type="text" name="" id="address" value="" placeholder="请填写详细地址" autocomplete="off" datatype="*" nullmsg="detailed_address">
					</span>
        </li>
        <li class="equipment_type" id="floor_lv">
            <b>公园等级</b>
            <span>
						<input class="user_result" type="text" id="shop_level" value="" focus_bool="false" disabled="disabled" onfocus="document.activeElement.blur()" placeholder="选择公园等级" datatype="*" nullmsg="installation_dj">
					</span>
        </li>
        <li>
            <b>公园卫生间数量</b>
            <span>
						<input type="number" name="" id="floor_number" value="" placeholder="填写公园卫生间数量" autocomplete="off" datatype="*" nullmsg="property_wc">
					</span>
        </li>
        <li>
            <b>备注（选填）</b><span><input type="text" name="" id="description" value="" placeholder="请填写备注信息" autocomplete="off"></span>
        </li>
    </ul>
    <ul class="shop_info img_up_ul">
        <li class="screen_info" style="position: relative;"><b class="mark_tt">上传照片</b><span></span></li>
        <li>
            <b>公园入口照</b>
            <span>
				        <label>
							<img class="Upload-imginput" src="/static/image/addphoto.png">
				            <input type="hidden" id="shop_image" datatype="*" nullmsg="shop_image" value="">
				            <input hidden="hidden" type="file" name="" id="" value="">
				        </label>
				    </span>
        </li>
        <li>
            <b>公园平面结构图</b>
            <span>
				        <label>
				            <img class="Upload-imginput" src="/static/image/addphoto.png">
				            <input type="hidden" id="plan_image" datatype="*" nullmsg="plan_image" value="">
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
                        <input class="update_input" id="other_image1" type="hidden">
                        <img class="imgspace" src="/static/image/blank.jpg">
                        <img class="addimg" src="/static/image/addphoto_big.png">
                        <input type="file" class="Upload-imginput" accept="image/*" id="up15">
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
<div class="mask"></div>
<div class="mask_box"></div>
<!--提交失败提示-->
<p class="sy-installed-ts">提交失敗</p>
<!-- 选择公司信息、楼宇类型、楼宇等级弹框s -->
<div class="equipment_box">
    <ul class="equipment_list" id="equipment_list"></ul>
    <div class="btn_box">
        <button class="cancel_btn" type="button">取消</button>
    </div>
</div>
<!-- 选择公司信息、楼宇类型、楼宇等级弹框e -->
<div id="areaMask" class="dzmask"></div>
<section id="areaLayer" class="express-area-box">
    <header>
        <h3>选择地区</h3>
        <a id="backUp" class="back" href="javascript:void(0)" title="返回"></a>
        <a id="closeArea" class="close" href="javascript:void(0)" title="关闭"></a>
    </header>
    <article id="areaBox">
        <ul id="areaList" class="area-list"></ul>
    </article>
</section>
<input type="hidden" name="yzmtk" id="yzmtk" value="">
<script type="text/javascript">
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    var token= "<?=$token?>";
</script>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/tip/tip.js"></script>
<script type="text/javascript" src="/static/js/tip/getpics.js"></script>
<script type="text/javascript" src="/static/js/jQuery.validate.min.js"></script>
<script type="text/javascript" src="/static/js/cos-js-sdk-v4.js"></script>
<script type="text/javascript" src="/static/js/mobile-adapt.js"></script>
<script type="text/javascript" src="/static/js/exif.js"></script>
<script type="text/javascript" src="/static/js/img-upload.js"></script>
<script type="text/javascript" src="/static/js/jquery.area.js"></script>
<script type="text/javascript" src="/static/js/select_time.js"></script>
<script type="text/javascript" src="/static/js/swiper.min.js"></script>
<script src="/static/js/tab_change.js"></script>
<script type="text/javascript">
    //获取公司信息
    $('#inc_info span').on('click', function () {
        $.ajax({
            url: baseApiUrl + "/build-wap/choose-company?token="+token,
            type: "POST",
            async: false,
            success:function (phpdata) {
                list_info(phpdata.data);
            },
            error:function (phpdata) {

            }
        });
    })
    //生成公司信息、公园等级列表
    var sel_def = '';
    //var inc_info = ['北京好物业有限公司', '北京放心住管理有限公司']; //公司信息
    var floor_lv = ['A', 'B', 'C', 'D'] //公园等级
    function list_info(a) {
        //遍历数据
        sel_def = '';
        $.each(a, function(index, value) {
            sel_def += '<li data-id='+index+'>' + value + '</li>';
        });
    }
    function screen_type(e) {
        //筛选数据
        switch (e) {
//            case 'inc_info':
//                list_info(inc_info);
//                break;
            case 'floor_lv':
                list_info(floor_lv);
                break;
        }
    }
    $('#list_box').on('click', '.equipment_type', function() {
        var self_me = $(this).attr('id');
        screen_type(self_me); //筛选数据
        $('.equipment_list').html(sel_def);
    })
</script>
</script>
<script type="text/javascript">
/*选择省市地区 */
$(function() {
    $("#area_name").click(function() {
        zxcs("azdz")
    })
})


$("#idform").Validform({
    tipSweep: true,
    tiptype: function(msg, o, cssctl) {
        if(msg=='userResult'){
            $('.sy-installed-ts').text('公司名称不能为空');
            tippanel();
        };
        if(msg=='office_building'){
            $('.sy-installed-ts').text('公园联系人不能为空');
            tippanel();
        };
        if(msg=='installation_address'){
            $('.sy-installed-ts').text('安裝地址不能为空');
            tippanel();
        };
        if(msg=='detailed_address'){
            $('.sy-installed-ts').text('详细地址不能为空');
            tippanel();
        };
        if(msg=='property_company'){
            $('.sy-installed-ts').text('公园名称不能为空');
            tippanel();
        };
        if(msg=='telephone_number'){
            $('.sy-installed-ts').text('联系人电话不能为空');
            tippanel();
        };
        if(msg=='property_wc'){
            $('.sy-installed-ts').text('公园卫生间数量不能为空');
            tippanel();
        };
        if(msg=='installation_dj'){
            $('.sy-installed-ts').text('公园等级不能为空');
            tippanel();
        };
        if (msg == 'shop_image') {
            $('.sy-installed-ts').text('公园入口照不能为空');
            tippanel();
        };
        if (msg == 'plan_image') {
            $('.sy-installed-ts').text('公园平面结构图不能为空');
            tippanel();
        };
    },
    beforeSubmit: function(curform) {
        var postData = {
            'company_id': $('#company_name').val(),
            'contact_name': $('#contact_name').val(),
            'contact_mobile': $('#contact_mobile').val(),
            'shop_name': $('#shop_name').val(),
            'area_id': $('#area').val(),
            'address': $('#address').val(),
            'shop_level': $('#shop_level').val(),
            'floor_number': $('#floor_number').val(),
            'description': $('#description').val(),
            'shop_image':getpics('#shop_image',1) ,
            'plan_image':getpics('#plan_image',1) ,
            'other_image':getpics('.update_input',1),

        };
        //console.log(postData);return;
        $.ajax({
            url: baseApiUrl + "/park-wap/create-park?token="+token,
            type: "POST",
            data: postData,
            async: false,
            success: function(phpdata) {
                if (phpdata.status == 200) {
                    $('.sy-installed-ts').text('提交成功，等待审核中');
                    tippanel("/inner-install/choose?token="+token);
                } else {
                    $('.sy-installed-ts').text(phpdata.message);
                    tippanel();
                }
            },
            error: function(phpdata) {
                $('.sy-installed-ts').text('申请失败');
                tippanel();
            }

        });
    },
    callback: function(form) {
        return false;
    },
    datatype: {
        "IDcard": /^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[0-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/i,
        "phone": /^1[34578]\d{9}$/,
        'j_cont': /^[1-9]$|^[1-9]\d$|^[1-9]\d{2}$|^[1-9]\d{3}$/
    }
})
</script>
<script>
    // 添加店鋪
    $('.sy_addspan').live('click', function() {
        $('.sy_fendianlist').append(
            '<div class="each"><dl><dt>分店名稱</dt><dd><input id="" autocomplete="off" class="sy_yx_srnr" type="text" placeholder="填寫店鋪名稱" nullmsg="companyname" datatype="*"><span class="sy_addspan"><img src="/static/image/sy_sqfdadd.png"></span></dd></dl><dl class="yx_anzdz"><dt>分店地址</dt><dd><p class="yx_dzxq azdz"><input type="hidden" class="br_area_id" id="area" placeholder="請選擇安裝地址" nullmsg="installarea" datatype="*" value=""><cite class="yx_srnr_dqu">請選擇安裝地址</cite></p><p class="yx_dzxq" id="xxdz"><input autocomplete="off" id="" class="yx_srnr" type="text" placeholder="填寫詳細地址" nullmsg="installaddress" datatype="*"></p><span class="sy_addspan_red"><img src="/static/image/sy_sqfdreduce.png"></span></dd></dl></div>'
        )
        $(this).remove();
        //店铺数加1
        var numobj = $('#fdnum span').html();
        $('#fdnum span').html(parseInt(numobj) + 1);
    })
    //刪除店鋪
    $('.sy_addspan_red').live('click', function() {
        var nu = $(this).parents('.each').next('.each').index();
        if (nu == -1) {
            $(this).parents('.each').prev('.each').find('dl:first dd').append(
                '<span class="sy_addspan"><img src="/static/image/sy_sqfdadd.png"></span>');
        }
        $(this).parents('.each').remove();
        //店铺数减1
        var numobj = $('#fdnum span').html();
        $('#fdnum span').html(parseInt(numobj) - 1);
    })
</script>
<script type="text/javascript">
    /*请上传授权证明  点击加号显示授权证明上传框*/
    $('.syadd_upload_pic').click(function() {
        //默认图片个数
        var inputleg = $(this).parents('.sy_authorcertif').find('.upload').length;
        //获取上传证明 upload的id 及Upload-imginput的id并赋值
        //当前点击时的id值
        var uploadid = $(this).parents('.sy_authorcertif').find('.upload').eq(inputleg - 1).attr('id');
        //当前点击时的id值【除去数字】
        var uploadpre = uploadid.substr(0, uploadid.length - 1);
        //当前点击的id值的最后一个数字+1
        var uploadidlast = parseInt(uploadid.substr(uploadid.length - 1, 1)) + 1;
        //重新生成一个id值  当前点击的值的id数+1
        var newuploadid = uploadpre + uploadidlast;
        //当前点击的 Upload-imginput【添加图片层】的id
        var upid = $(this).parents('.sy_authorcertif').find('.upload').eq(inputleg - 1).find('.Upload-imginput').attr(
            'id')
        //当前点击的 Upload-imginput【添加图片层】的id除去最后一个数字 id="up1*"
        var upidpre = upid.substr(0, upid.length - 1);
        //当前点击的 Upload-imginput【添加图片层】当前点击的id值的最后一个数字+1
        var upidlast = parseInt(upid.substr(upid.length - 1, 1)) + 1;
        //当前点击的 Upload-imginput【添加图片层】重新生成一个id值  当前点击的值的id数+1
        var newupid = upidpre + upidlast;
        //authoriz_image1
        // <input class="update_input" id="other_image1" type="hidden">  这个的id号+1
        var update_inputid = $(this).parents('.sy_authorcertif').find('.upload').eq(inputleg - 1).find('.update_input').attr(
            'id')
        var update_inputidpre = update_inputid.substr(0, update_inputid.length - 1);
        var update_inputidlast = parseInt(update_inputid.substr(update_inputid.length - 1, 1)) + 1;
        var newupdate_inputid = update_inputidpre + update_inputidlast;

        if (inputleg < 6) {
            if ($(this).attr('name') == 'other_upload_image') {
                var inputHtml = '<input class="update_input" id="' + newupdate_inputid + '" type="hidden">';
            } else {
                var inputHtml = '<input class="update_input" id="' + newupdate_inputid +
                    '" type="hidden" placeholder="請上傳圖片" nullmsg="authoriz_image" datatype="*">';
            }
            $(this).parents('.sy_authorcertif').find('.syadd_upload_pic').before('<div class="upload" id="' + newuploadid +
                '">' +
                inputHtml +
                '<img class="imgspace" src="/static/image/blank.jpg"><img class="addimg" src="/static/image/addphoto_big.png">' +
                '<input type="file" class="Upload-imginput" accept="image/*" id="' + newupid + '">' +
                '<div class="progress-bar" step="0"></div><p class="syred_upload_pic"><img src="/static/image/sy_sqfdreducs.png"></p></div>'
            )
            var lolg = $(this).parents('.sy_authorcertif').find('.upload').length;
            if (lolg == 6) {
                $(this).parents('.sy_authorcertif').find('.syadd_upload_pic').hide();
            }
        }
    })
    //减号 删除添加的授权证明图片
    $('.syred_upload_pic').live('click', function() {
        var lolg = $(this).parents('.sy_authorcertif').find('.upload').length;
        if (lolg < 7) {
            $(this).parents('.sy_authorcertif').find('.syadd_upload_pic').show();
        }
        $(this).parent('.upload').remove();
    })
</script>
</body>
</html>
