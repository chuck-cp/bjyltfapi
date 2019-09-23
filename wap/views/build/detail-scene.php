<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>大堂以上等候区画框</title>
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
        <li class="screen_info" style="position: relative;margin-bottom: 0;">
            <b class="mark_tt">填写信息</b>
            <span class="add_span">
                <img class="add_img" src="/static/image/sy_sqfdadd.png">
            </span>
        </li>
        <li class="cur_box" id="titleTag"><span class="cur_def">当前页</span></li>
    </div>
    <ol id="list_box" class="ol_list">
        <li class="item_li cur_show" view_id="0">
            <ul class="shop_info">
                <li class="floor_type">
                    <b>安装层数</b>
                    <span>
                        <input class="floor_no" type="text" name="" id="" value="" focus_bool="false" onfocus="document.activeElement.blur()" placeholder="请选择安装层数" autocomplete="off">
                    </span>
                </li>
                <li class="equipment_type" id="device_types">
                    <b>设备类型</b>
                    <span>
                        <input class="user_result" type="text" id="" value="" focus_bool="false" disabled="disabled" onfocus="document.activeElement.blur()" placeholder="请选择设备类型">
                    </span>
                </li>
                <?php if($screen_type==1):?>
                    <li style="position: relative;" class="sy_selecttime"><b>开机时间</b><span class="business-open" id="screen_start_at">09:00</span><input
                                type="button" value="自定义" class="yanzhenma"></li>
                    <li style="position: relative;" class="zhj_selecttime"><b>关机时间</b><span class="business-close test" id="screen_end_at">21:00</span><input
                                type="button" value="自定义" class="yanzhenma"></li>
                <?endif;?>
                <li><b>备注（选填）</b><span><input type="text" class="desc" name="" id="" value="" optional_bool="true" placeholder="请填写备注信息" /></span></li>
            </ul>
        </li>
    </ol>
    <p class="sure_box" style="display: none;"><button class="sure_btn" type="button">保存</button></p>
    <br>
    <br>
    <br>
    <!-- 店铺信息填写E -->
    <input type="hidden" name="position_id" id="postion_id" value="0">
</form>
<!-- 克隆表格范本s -->
<div id="clone">
    <li class="item_li cur_show" view_id="0">
        <ul class="shop_info">
<!--            <li>-->
<!--                <b>客梯等候区</b>-->
<!--                <span>-->
<!--                    <input type="number" name="" id="" value="" placeholder="请填写每层电梯等候区数量" autocomplete="off">-->
<!--                </span>-->
<!--            </li>-->
<!--            <li><b>客梯数量</b><span><input type="number" name="" id="" value="" placeholder="请填写每层客梯数量" autocomplete="off"></span></li>-->
<!--            <li>-->
<!--                <b>安装数量</b><span><input type="number" name="" id="" value="" placeholder="填写每个电梯等候区安装数量" autocomplete="off"></span>-->
<!--            </li>-->
            <li class="floor_type">
                <b>安装层数</b>
                <span>
                    <input class="floor_no" type="text" name="" id="" value="" focus_bool="false" onfocus="document.activeElement.blur()" placeholder="请选择安装层数" autocomplete="off">
                </span>
            </li>
            <li class="equipment_type" id="device_types">
                <b>设备类型</b>
                <span>
                    <input class="user_result" type="text" id="" value="" focus_bool="false" disabled="disabled" onfocus="document.activeElement.blur()" placeholder="请选择设备类型">
                </span>
            </li>
            <?php if($screen_type==1):?>
                <li style="position: relative;" class="sy_selecttime">
                    <b>开机时间</b><span class="business-open" id="screen_start_at">09:00</span><input type="button" value="自定义" class="yanzhenma">
                </li>
                <li style="position: relative;" class="zhj_selecttime">
                    <b>关机时间</b><span class="business-close test" id="screen_end_at">21:00</span><input type="button" value="自定义" class="yanzhenma">
                </li>
            <?endif;?>
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
        <b class="floor_tt">层数选择</b><img class="floor_close" src="/static/image/close_floor.png">
    </div>
    <div class="floor_con"></div>
    <div class="floor_btn_warp">
        <button class="floor_btn" type="button">确认</button>
    </div>
</div>
<!-- 楼层选择弹框e -->
<p class="error_time">屏幕开机时间不得小于10小时</p>
<!--开关机选择弹框-->
<div class="riqi-select">
    <div class="select-select">
        <p class="select-cancel">取消</p>
        <p class="select-confirm">确定</p>
        <span class="line"></span>
    </div>
    <div class="riqi-default">:</div>
    <!--分-->
    <div class="year-date">
        <div class="swiper-container">
            <div class="swiper-wrapper">
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
    <!--秒-->
    <div class="mouth-date">
        <div class="swiper-container">
            <div class="swiper-wrapper">
            </div>
            <!-- Add Pagination -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</div>
<!--开关机结束-->
<div id="areaMask" class="dzmask"></div>
<script type="text/javascript">
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    var token = "<?=$token?>";
    var id = "<?=$id?>";
    var screen_types = "<?=$screen_type?>";
    var shop_id = "<?=$shop_id?>";
</script>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/jQuery.validate.min.js"></script>
<script type="text/javascript" src="/static/js/mobile-adapt.js"></script>
<script type="text/javascript" src="/static/js/swiper.min.js"></script>
<script type="text/javascript" src="/static/js/tab_select_time.js" ></script>
<script type="text/javascript" src="/static/js/tip/tip.js" ></script>
<script type="text/javascript" src="/static/js/tab_change.js" ></script>
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
                if(phpdata.data.floor_status == -1){
                    $('.sure_box').css('display','block');
                }
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
            }
        },error:function (phpdata) {
            $('.sy-installed-ts').text('请联系检查服务器配置');
            tippanel();
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
            alert('已通过验证,所有表单提交成功');
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
            var toUrl = screen_types == 1 ? 'scene-led' : 'scene-post';
            //console.log(postData);return;
            $.ajax({
                url:baseApiUrl + '/build-wap/build-scene',
                type:"POST",
                data:postData,
                success:function (phpdata) {
                    if(phpdata.status == 200){
                        $('.sy-installed-ts').text('信息保存成功');
                        tippanel('/build/'+toUrl+'?token='+token+'&build_id=<?=$shop_id?>&screen_type=<?=$screen_type?>&shop_type=<?=$shop_type?>');
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
        },
        callback: function(form) {
            return false;
        },
    })
</script>


</body>
</html>
