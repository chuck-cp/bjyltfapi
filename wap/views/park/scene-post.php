<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>公园卫生间</title>
    <link rel="stylesheet" type="text/css" href="/static/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/static/css/logo_install.css">
    <link rel="stylesheet" type="text/css" href="/static/css/swiper.min.css">
    <link rel="stylesheet" type="text/css" href="/static/css/apply.css">
    <link rel="stylesheet" type="text/css" href="/static/css/tab_change.css" />
</head>
<body>
<form id="idform">
    <div class="shop_info">
        <li class="alone_possess_li">
            <b>是否独占</b>
            <span class="alone_possess_bool">
						<div class="radio_box">
							<label><input type="radio" name="alone_possess" id="" value="1" placeholder="请选择是否独占" checked="checked" />是</label>
						</div>
						<div class="radio_box">
							<label><input type="radio" name="alone_possess" id="" value="2" placeholder="请选择是否独占" />否</label>
						</div>
					</span>
        </li>
    </div>
    <div class="shop_info">
        <li class="screen_info" style="position: relative;margin-bottom: 0;"><b class="mark_tt">填写信息</b><span class="add_span"><img
                    class="add_img" src="/static/image/sy_sqfdadd.png"></span></li>
        <li class="cur_box" id="titleTag"><span class="cur_def">当前页</span></li>
    </div>
    <ol id="list_box">
        <li class="item_li cur_show" view_id="0">
            <ul class="shop_info">
                <li class="floor_type">
                    <b>卫生间编号</b>
                    <span>
								<input class="floor_no" type="text" name="" id="" value="" focus_bool="false" onfocus="document.activeElement.blur()"
                                       placeholder="请选择卫生间编号" autocomplete="off">
							</span>
                </li>
                <li class="equipment_type" id="device_types">
                    <b>设备类型</b>
                    <span>
								<input class="user_result" type="text" id="" value="" focus_bool="false" disabled="disabled" onfocus="document.activeElement.blur()"
                                       placeholder="请选择设备类型">
							</span>
                </li>
                <li><b>备注（选填）</b><span><input type="text" class="desc" name="" id="" value="" optional_bool="true" placeholder="请填写备注信息" /></span></li>
            </ul>
        </li>
    </ol>
    <p class="sure_box" style="display: none;"><button class="sure_btn" type="button">保存</button></p>
    <br>
    <br>
    <br>
    <!-- 店铺信息填写E -->
</form>
<!-- 克隆表格范本s -->
<div id="clone">
    <li class="item_li cur_show" view_id="0">
        <ul class="shop_info">
            <li class="floor_type">
                <b>卫生间编号</b>
                <span>
							<input class="floor_no" type="text" name="" id="" value="" focus_bool="false" onfocus="document.activeElement.blur()"
                                   placeholder="请选择卫生间编号" autocomplete="off">
						</span>
            </li>

            <li class="equipment_type" id="device_types">
                <b>设备类型</b>
                <span>
							<input class="user_result" type="text" id="" value="" focus_bool="false" disabled="disabled" onfocus="document.activeElement.blur()"
                                   placeholder="请选择设备类型">
						</span>
            </li>
            <li><b>备注（选填）</b><span><input type="text" class="desc" name="" id="" value="" optional_bool="true" placeholder="请填写备注信息" /></span></li>
        </ul>
    </li>
</div>
<!-- 克隆表格范本e -->
<!-- 背景遮罩 -->
<div class="mask"></div>
<!-- 背景遮罩2 -->
<div class="mask_box"></div>
<!--提交失败提示-->
<p class="sy-installed-ts">提交失败</p>
<!-- 选择设备弹框 -->
<div class="equipment_box">
    <ul class="equipment_list"></ul>
    <div class="btn_box">
        <button class="cancel_btn" type="button">取消</button>
    </div>
</div>
<!-- 选择设备弹框 -->
<!-- 楼层选择弹框s -->
<div class="floor_sel_box">
    <div class="floor_tt_warp">
        <b class="floor_tt">卫生间编号</b><img class="floor_close" src="/static/image/close_floor.png">
    </div>
    <div class="floor_con"></div>
    <div class="floor_btn_warp">
        <button class="floor_btn" type="button">确认</button>
    </div>
</div>
<!-- 楼层选择弹框e -->
<div id="areaMask" class="dzmask"></div>
<input type="hidden" name="yzmtk" id="yzmtk" value="">
<script type="text/javascript">
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    var token = "<?=$token?>";
    var id = "<?=$id?>";
    var screen_types = "<?=$screen_type?>";
</script>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jQuery.validate.min.js"></script>
<script type="text/javascript" src="/static/js/mobile-adapt.js"></script>
<script type="text/javascript" src="/static/js/tip/tip.js"></script>
<script type="text/javascript" src="/static/js/swiper.min.js"></script>
<script type="text/javascript" src="/static/js/tab_select_time.js" ></script>
<script type="text/javascript" src="/static/js/toilet_tab_change.js" ></script>
<script type="text/javascript">

    function init_list() {
        // 表格初始化
        $('.cur_box span').removeClass('cur_def');
        $('.cur_box span:first').addClass('cur_def');
        $('#list_box li').removeClass('cur_show');
        $('#list_box li:first').addClass('cur_show');
    }
</script>
<script type="text/javascript">
    //根据position_config的id 获取应该填写的表单内容
    $.ajax({
        url:baseApiUrl + '/build-wap/get-detail-scene-by-config-id',
        type:"GET",
        data:{
            'id':id,
            'token':token,
            'shop_id':'<?=$shop_id?>',
            'shop_type':'<?=$shop_type?>',
            'screen_type':'<?=$screen_type?>',
            'position_id':'<?=$id?>'
        },
        success:function (phpdata) {
            if(phpdata.data){
                //console.log(phpdata.data.newData);
                var obj = phpdata.data.newData;
                var shtml = '';
                for (var i=0; i<obj.length; i++){
                    shtml += '<li>';
                    shtml += '<b>'+obj[i].value+'</b>';
                    shtml += '<span>';
                    shtml += '<input type="number" mark-data="'+obj[i]["mark"]+'" config-data="'+obj[i]["id"]+'" name="'+obj[i]["key"]+'" id="'+obj[i]["key"]+'" value="" placeholder="请填写'+obj[i].placeholder+'数量" autocomplete="off">';
                    shtml += '</span>';
                    shtml += '</li>';
                }
                $('#list_box .shop_info .floor_type, #clone .shop_info .floor_type').after(shtml);
            }
            if(phpdata.data.oldData){
                console.log(phpdata.data.oldData);
                var oldObj = phpdata.data.oldData;
                var titleTag = '';
                var mainBody = '';
                var domObj = $('.item_li').eq(0);
                for(var j=0; j<oldObj.positionView.length; j++){
                    titleTag += '<span class="cur_def" view_id="'+oldObj.positionView[oldObj.positionView.length-j-1].id+'">安装信息'+(oldObj.positionView.length-j)+'</span>';
                    //给被克隆页面赋值
                    domObj.find('.floor_no').val(oldObj.positionView[j].floor_number);
                    domObj.find('.desc').val(oldObj.positionView[j].description);
                    var this_positon_num = oldObj.positionView[j].position_number.split(',');
                    domObj.find('input[name="position_number"]').each(function (i) {
                        $(this).val(this_positon_num[i]);
                    });
                    var this_position_config_number = oldObj.positionView[j].position_config_number.split(',');
                    domObj.find('input[name="position_name"]').each(function (i) {
                        $(this).val(this_position_config_number[i]);
                    });
                    domObj.find('.user_result').val(oldObj.positionView[j].screen_spec);
                    domObj.find('input[name="reference_number"]').val(oldObj.positionView[j].reference_number);
                    if(oldObj.positionView[j].screen_start_at){
                        domObj.find('.business-open').html(oldObj.positionView[j].screen_start_at);
                    }
                    if(oldObj.positionView[j].screen_end_at){
                        domObj.find('.business-close').html(oldObj.positionView[j].screen_end_at);
                    }

                    //设置标记物
                    domObj.attr('view_id',oldObj.positionView[j].id);
                    //插入旧数据
                    domObj.after(domObj.clone());
                }
                //删除当前空页面
                domObj.eq(0).remove();
                //tab切换标签
                $('#titleTag').html(titleTag);
                //是否独占
                $('input:radio[value="'+phpdata.data.oldData.positon.monopoly+'"]').attr('checked',true);
                //设置隐藏域存储position_id
                $('#postion_id').val(phpdata.data.oldData.positon.id);
                init_list();
            }else{
                $('.sure_box').css('display','block');
            }
        },error:function (phpdata) {

        }
    });
</script>


<script type="text/javascript">
    function getValsArr(dom, nextDom, val, dataType, is_null){
        var re = [];
        $(dom).each(function (i,item) {
            re.push(getAttr($(this).find(nextDom), val, dataType, is_null));
        })
        return re;
    }
    $('.sure_btn').live('click', function() {
        // 保存按钮
        each_ver(); //验证表单
        if (true == verify_bool) {
            //alert('已通过验证,所有表单提交成功');
            var postion_config_id = [];
            var postData = {
                //position table
                'shop_type': <?=$shop_type?>,
                'screen_type': <?=$screen_type?>,
                'shop_id': <?=$shop_id?>,
                'position_id': <?=$id?>,
                'position_config_id': getAttr($('#idform .cur_show input[name="position_name"]'), 'config-data'),
                'monopoly': $('input[name="alone_possess"]').val(),
                //view table
                'position_number': getValsArr('#list_box .item_li','input[name="position_number"]', 'val', []),
                'position_config_number': getValsArr('#list_box .item_li','input[name="position_name"]', 'val', []),
                'reference_number': getValsArr('#list_box .item_li','input[name="reference_number"]', 'val', []),
                'mark_data': getValsArr('#list_box .item_li','input[name="position_name"]', 'mark-data', []),
                'screen_spec': getAttr($('#idform .user_result'), 'val', []),
                'floor_number': getAttr($('#idform .floor_no'), 'val', []),
                'description': getAttr($('#idform .desc'), 'val', [], true),
                'token': token,
                'screen_start_at': getAttr($('#idform .business-open'), 'val', [], false, 'html'),
                'screen_end_at': getAttr($('#idform .business-close'), 'val', [], false, 'html'),
                //用来判断是插入还是修改
                'shop_position_id':$('#postion_id').val(),
                'view_id':getAttr($('#idform .item_li'), 'view_id', [], true),
            };
            //console.log(postData);return;
            $.ajax({
                url:baseApiUrl + '/build-wap/build-scene',
                type:"POST",
                data:postData,
                success:function (phpdata) {
                    if(phpdata.status == 200){
                        $('.sy-installed-ts').text('信息保存成功');
                        tippanel('/park/?token='+token);
                    }else{
                        $('.sy-installed-ts').text(phpdata.message);
                        tippanel();
                    }
                },error:function () {
                    $('.sy-installed-ts').text('提交时发生错误，请重试');
                    tippanel();
                }
            });
        }
    });
</script>
<script type="text/javascript">
    //生成设备类型
    var sel_def = '';
    //var device_types = ['海报1', '海报2']; //设备类型
    function list_info(a) {
        //遍历数据
        sel_def = '';
        $.each(a, function(index, value) {
            sel_def += '<li>' + value + '</li>';
        });
    }
    function screen_type(e) {
        //筛选数据
        switch (e) {
//            case 'device_types':
//                list_info(device_types);
//                break;
            case 'floor_type':
                list_info(floor_type);
                break;
            case 'floor_lv':
                list_info(floor_lv);
                break;
        }
    }
    $('#list_box').on('click', '.equipment_type', function() {
        var self_me = $(this).attr('id');
        var stype = screen_types == 1 ? 'led' : 'poster' ;
        $.ajax({
            url:baseApiUrl + '/system/get-led-or-poster-spec',
            type:"GET",
            data:{'id':id, 'token':token, 'equiment_type':stype},
            async:false,
            success:function (phpdata) {
                if(phpdata.data){
                    var device_types = phpdata.data;
                    //console.log(device_types)
                    list_info(device_types);
                }
            },error:function () {

            }
        });
        screen_type(self_me); //筛选数据
        $('.equipment_list').html(sel_def);
    })
</script>
<script type="text/javascript">
    /*选择省市地区 */
    $(function() {
        $("#area_name").click(function() {
            zxcs("azdz")
        })
    })
    //选择时间错误弹框
    function err_time() {
        $('.error_time').show();
        setTimeout(function() {
            $('.error_time').hide()
        }, 2000);
    }
    //区号选择
    var area_val_cur;
    $('.area_selection').click(function() {
        $('.area_sel').show();
    })
    $('.cancel_btn').click(function() {
        $('.area_sel').hide();
    })
    $('.area_label').click(function() {
        area_val_cur = $(this).find('.area_val').text();
    })
    $('.confirm_btn').click(function() {
        $('.area_selection').text(area_val_cur);
        $('.area_sel').hide();
    })
    $("#idform").Validform({
        tipSweep: true,
        tiptype: function(msg, o, cssctl) {
            if (msg == 'office_building') {
                $('.sy-installed-ts').text('大堂电梯等候区数量不能为空');
                tippanel();
            };
            if (msg == 'userResult') {
                $('.sy-installed-ts').text('设备类型不能为空');
                tippanel();
            };
            if (msg == 'installation_quantity') {
                $('.sy-installed-ts').text('填写侧面安装设备数量不能为空');
                tippanel();
            };
        },
        beforeSubmit: function(curform) {
            var name = $('#zhuzl').val();
            var area = $('#area').val();
            var address = $('#address').val();
            var company_name = $('#company').val();
            var contacts_name = $('#applier').val();
            var contacts_mobile = $('.per_info_mobi').val();
            var apply_screen_number = $('#anz_num').val();
            var screen_start_at = $('#screen_start_at').html();
            var screen_end_at = $('#screen_end_at').html();
            var shop_place_type = '2';
            var postData = {
                'name': name,
                'area': area,
                'address': address,
                'company_name': company_name,
                'contacts_name': contacts_name,
                'contacts_mobile': contacts_mobile,
                'apply_screen_number': apply_screen_number,
                'screen_start_at': screen_start_at,
                'screen_end_at': screen_end_at,
                'shop_place_type': shop_place_type,
            };
            //console.log(postData);return;
            $.ajax({
                url: baseApiUrl + "/shop/hk-create?token=f62QxaXfFGWK7F7x1iMVqCGFqfq0-MVI",
                type: "POST",
                data: postData,
                async: false,
                success: function(phpdata) {
                    if (phpdata.status == 200) {
                        $('.sy-installed-ts').text('申请成功');
                        tippanel("/hk-shop/hk-choose?token=f62QxaXfFGWK7F7x1iMVqCGFqfq0-MVI");
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
