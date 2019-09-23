<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title></title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <style type="text/css">
        /*标签初始化*/
        /*标签初始化*/
        * {margin: 0;padding: 0 }
        table {border-collapse: collapse;border-spacing: 0}
        h1,h2,h3,h4,h5,h6 {font-size: 100%; font-weight: normal;}
        ul,ol,li {list-style: none}
        em,i {font-style: normal}
        img {border: 0;display:inline-block}
        input,img {vertical-align: middle; border:none; }
        a {color: #333;text-decoration: none;-webkit-tap-highlight-color:transparent;}
        input,button,textarea{-webkit-tap-highlight-color:transparent;outline: none;  }
        article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {display: block;}
        body{ background: #f0f0f0; -webkit-overflow-scrolling: touch; color: #333; font-size: 14px;min-width: 300PX;max-width: 640PX; margin: 0 auto; font-family: '微软雅黑';}
        /*头部*/
        .adver_nav{height: 44px; background: #fff; box-sizing: border-box; max-width: 640px; z-index: 9; font-size: 16px;line-height: 44px;text-align: center; padding:0 4%; position:fixed; width: 100%;}
        .adver_nav .goback{ width: 20px; display: block; position: absolute;}
        .adver_nav .goback img{ width: 100%;}
        .reload{ line-height: 44px; position: absolute; right: 10px; top: 0;}
        /*内容*/
        .advertise{ position:relative; padding: 44px 0; overflow: hidden;}
        /*内容--左*/
        .advertise .ad_left{ float: left; width: 40%; border-right: 1px solid #fff;box-sizing: border-box;}
        .advertise .ad_left li{ height: 40px; line-height: 40px; font-size: 14px; color: #fff; background: #ea9061;border-bottom: 1px solid #fff;box-sizing: border-box;}
        .ad_left_blank{ display: none;}
        .ad_left_input{ float: left; margin:11px 5px 0 10px;  width: 18px; height: 18px;background:url(/static/image/adv_nav_unchecked.png);background-size: contain;background-position: 50% 50%;background-repeat: no-repeat;}
        .advertise .ad_left li p{ float: left; width: 60%; line-height: 40px;text-overflow:ellipsis; white-space:nowrap; overflow:hidden;}
        .ad_left_input input{ width: 18px; height: 18px; opacity: 0; float: left;display: none !important;}
        .ad_left_rq{ text-align: center;}
        /*内容--右*/
        .advertise .ad_right{ width: 60%; float: left; overflow-x: auto;box-sizing: border-box;}
        /*.advertise .ad_right_rq{width: 82px; height: 40px; float: left;line-height: 40px;font-size: 14px;color: #fff;background: #fccab0;text-align: center;border-bottom: 1px solid #fff;border-right: 1px solid #fff;box-sizing: border-box;}*/
        .ad_right_rqblank{height: 40px;box-sizing: border-box; display: none;}
        .ad_right_rqblank{ display: none;}
        .advertise .ad_right_list ul{ width:82px; float: left; border-bottom: 1px solid #fff; position: relative;}
        .advertise .ad_right_list ul li{height: 40px;line-height: 40px;font-size: 14px;color: #333;background: #e6e6e6;text-align: center;border-bottom: 1px solid #fff;border-right: 1px solid #fff;box-sizing: border-box;
        }
        .advertise .ad_right_rq{}
        .advertise .ad_right_rq li{height: 40px; float:left;line-height: 40px;font-size: 14px;color: #fff;background: #fccab0;text-align: center;border-bottom: 1px solid #fff;border-right: 1px solid #fff;box-sizing: border-box; width: 82px;}
        .noallowance{ background: #efa5a5 !important; color: #fff !important;}
        .fixed_lf{ position: fixed; width: 40%; border-right: 1px solid #fff;z-index: 10;}
        .fixed_rh{ position:fixed;border-right: 1px solid #fff;height: 40px;width:82px; z-index: 9; top: 44px;}
        /*.fixed_rh{ position:fixed;border-right: 1px solid #fff;height: 40px;width:82px;}*/
        /*底部*/
        .adver_bottom{ position: fixed; left: 0; bottom:0; background: #fff; width: 100%; height: 44px;    z-index: 22;}
        .adver_bottom li{ float: left; text-align: center; line-height: 44px;}
        .adver_bottom .selected{ width: 26.67%; background: #ccc;}
        .adver_bottom .selected_all{ width: 20%; background: #fff; position: relative;}
        .adver_select_all{ width: 100%; height: 44px; position: absolute; left: 0; top: 0; opacity: 0;}
        .adver_bottom .confirm_area{ background: #ea9061; color: #fff; width: 53.33%;}
        .adver_bottom .confirm_area button{background: #ea9061; color: #fff; width: 100%;height: 44px; border: none; font-size: 15px;}
        .selected_area_panel{ position: fixed; bottom:44px; background: #fff;width: 100%; display: none; z-index: 22}
        .selected_area_panel .clears{ height: 35px; line-height: 35px; background: #e6e6e6;text-align: right;padding: 0 10px; }
        .selected_area_panel .clears img{ width: 15px; height: 17px; padding-right: 5px;}
        .selected_area_panel .list{ padding: 0 10px; max-height: 216px; overflow-y: scroll;}
        .selected_area_panel .list li{ line-height: 35px; border-bottom: 1px solid #e6e6e6; height:35px;position: relative;}
        .selected_area_panel .list li .lf{ float: left; width: 50%;text-overflow:ellipsis; white-space:nowrap; overflow:hidden;}
        .selected_area_panel .list li .rh{ float:right;width: 30%; margin-right: 30px; text-align:right;text-overflow:ellipsis; white-space:nowrap; overflow:hidden;}
        .adv_list_delete{ position: absolute; right: 0px; top: 7px;width: 20px; height: 20px; display: inline-block;}
        .adv_list_delete img{width: 20px; height: 20px; float: left;}
        .selected_area_panel .total_box{ line-height: 40px; overflow: hidden; padding: 0 10px; font-weight: bold;}
        .selected_area_panel .total_box .total{ float: left;}
        .selected_area_panel .total_box .lent_jd{ float: right;}
        .mask{ position: fixed; width: 100%; left: 0; top: 0; z-index: 20;}
        .sy-installed-ts{ width: 100%; height: 30px; line-height: 30px; text-align: center; color: #fff; position: fixed;
            margin-top: -15px; border-radius:5px; font-size: 14px; display: none;}
        .sy-installed-ts span{ background: rgba(0,0,0,0.3); color: #fff; font-size: 14px;  padding: 5px; border-radius: 5px; background: rgba(0,0,0,0.3);}
    </style>
</head>
<body>
<div class="adver_nav">
	 <span class="goback">
	 	<a id="goback" href="javascript:void(0);"></a><img src="/static/image/adv_nav_back.png">
	 </span>
    <h3>余量日历</h3>
    <a href="javascript:;" class="reload" onclick="javascript:location.reload();">刷新</a>
</div>
<div class="advertise">
    <div class="ad_left">
        <ul id="area">
            <li class="ad_left_rq">日期/地区</li>
            <li class="ad_left_blank"></li>

        </ul>
    </div>
    <div class="ad_right">
        <div class="ad_right_rqblank"></div>
        <div class="ad_right_rq" >
            <ul id="date">

            </ul>
        </div>
        <div class="ad_right_list" id="space">

        </div>
    </div>
</div>
<style>
    .width100 .confirm_area{ width: 100%}
</style>
<?if(json_decode($params)->request_types == 'modify'):?>
    <div class="adver_bottom width100">
        <ul>
            <li class="confirm_area" id="modify_order"><button type="button">确认修改</button></li>
        </ul>
    </div>
<?else:?>
    <div class="adver_bottom" style="display: none;">
        <ul>
            <li class="selected adver_selected" style="position: relative">已选择 <i style="width: 20%; height: 20px; line-height: 20px; position: absolute; right: 0px; background: #ea9061; display: inline-block; font-size: 12px; text-align: center; color: #fff; border-radius: 20px;display: none;" id="sel_num">8</i></li>
            <li class="selected_all"><span class="selected_all_span">取消全选</span><input checked="checked" type="checkbox" class="adver_select_all"></li>
            <li class="confirm_area" id="confirm_area"><button type="button">确认区域</button></li>
        </ul>
    </div>
<?endif;?>

<!--已选择区域弹框-->
<div class="mask"></div>
<div class="selected_area_panel">
    <div class="clears"><img src="/static/image/adv_clear.png">清空</div>
    <div class="list">
        <ul class="selected_area_ul">

        </ul>
    </div>
    <div class="total_box">
        <p class="total">总计：<em class="selected_area_all"></em></p>
        <p class="lent_jd"><span class="selected_area_legth">0</span>区域</p>
    </div>

</div>
<div class="sy-installed-ts"><span>请选择要投放的地区！</span></div>
<script src="/static/js/jquery-1.7.2.min.js"></script>
<script>
    $(function(){
        //初始界面开始
        var objs = '<?echo $params;?>';
        var member_id = '<?echo $member_id;?>';
        var arrobj = eval("("+objs+")");
        var url_route = "<?=Yii::$app->params['baseApiUrl']?>"+"/member/"+member_id+"/confirm-area-view";
        var order_id = arrobj.order_id ? arrobj.order_id : false;
        $.ajax({
            url: url_route,
            type: 'GET',
            async:false,
            dataType: 'json',
            data:{'token':arrobj.token, 'area_id':arrobj.area_id,'advert_id':arrobj.advert_id,'advert_time':arrobj.advert_time, 'rate':arrobj.rate, 'start_at':arrobj.start_at, 'end_at':arrobj.end_at, 'page':arrobj.page, 'request_types':arrobj.request_types, 'order_id':order_id},
            success:function (phpdata) {
                var aobj = phpdata.data;
                var shtml = '';
                var sphtml = '';
                if(arrobj.request_types == 'modify'){
                    for(var i=0; i<aobj.area.length; i++){
                        shtml += '<li><span class="ad_left_input" style="visibility: hidden;"><input checked="checked" disabled="disabled" value="'+aobj.area[i].area_id+'"  type="checkbox"  data-pre="'+aobj.area[i].prev_name+'"></span><p>'+aobj.area[i].area_name+'</p></li>';
                    }
                }else{
                    for(var i=0; i<aobj.area.length; i++){
                        shtml += '<li><span class="ad_left_input" style="visibility: hidden;"><input checked="checked" value="'+aobj.area[i].area_id+'"  type="checkbox"  data-pre="'+aobj.area[i].prev_name+'"></span><p>'+aobj.area[i].area_name+'</p></li>';
                    }
                }
                var dhtml = '';
                for (var j=0; j<aobj.item.length; j++){
                    dhtml += '<li>'+aobj.item[j].date+'</li>';
                    sphtml += '<ul>';
                    for (var k=0; k<aobj.item[j].space_time_list.length; k++){
                        if(aobj.item[j].space_time_list[k] == '无余量'){
                            sphtml += '<li class="noallowance">'+aobj.item[j].space_time_list[k]+'</li>';
                        }else{
                            sphtml += '<li>'+aobj.item[j].space_time_list[k]+'</li>';
                        }

                    }
                    sphtml += '</ul>';
                }
                $('#sel_num').html(aobj.item.length);
                $("#area").append(shtml);
                $("#date").append(dhtml);
                $("#space").append(sphtml);
                var  cs_ul_leg=$('.ad_right_rq li').length;
                $('.ad_right_list').css('width',cs_ul_leg*82);
                $('.ad_right_rqblank').css('width',cs_ul_leg*82);
                $('.ad_right_rq').css('width',cs_ul_leg*82);
                $('.ad_left_input').css({"background":"url(/static/image/adv_nav_checked.png)","background-size":"contain","background-position":"50% 50%","background-repeat":"no-repeat"});
                //把所有的地区加入已选列表
                $('.ad_left_input').each(function () {
                    var this_area = $(this).next('p').html();
                    var prev_area = $(this).find('input').attr('data-pre');
                    var index_id = $(this).parents('li').index();
                    $('.selected_area_ul').append('<li id="'+index_id+'"><p class="lf">'+this_area+'</p><p class="rh">'+prev_area+'</p><span class="adv_list_delete"><img src="/static/image/adv_list_clear.png"></span></li>');
                })
                var counts = $('.selected_area_ul li').length;
                $('.selected_area_legth').html(counts);
            },error:function (phpdata) {

            }
        })

        //初始界面结束



    //$(document).ready(function(){

        var lef_wd=$('.ad_left').width();
        //上下滚动
        $(window).scroll(function(){
            var scroll_top = $(document).scrollTop();
            if(scroll_top>1){
                $('.ad_left_rq').addClass('fixed_lf');
                $('.ad_right_rq').addClass('fixed_rh');
                $('.ad_left_blank').show();
                $('.ad_right_rqblank').show();
            }
            else{
                $('.ad_left_rq').removeClass('fixed_lf');
                $('.ad_right_rq').removeClass('fixed_rh');
                $('.ad_left_blank').hide();
                $('.ad_right_rqblank').hide();
            }
        });

        //左右滚动
        //console.log(arrobj);
        $('.ad_right').scroll(function(){
            //获取屏幕的宽度
            var scroll_w=$(window).width();
            var rgh_box=$('.ad_right_list').width();
            var offset_x=rgh_box-scroll_w;

            //总天数
            var total_days = '<?echo $total_days?>';
            //真实的偏移坐标
            var relpffset_x=Math.abs($('.ad_right_list').offset().left);
            $('.ad_right_rq').css('left',$('.ad_right_list').offset().left);
            var doc_w = $('.ad_right_list').width();
            var scroll_left = $(".ad_right").scrollLeft();
            var window_wh = $('.ad_right').width();
            console.log(scroll_left+window_wh+'----'+doc_w)
            console.log(scroll_left+window_wh>=doc_w)

            if(scroll_left+window_wh>=doc_w-20){
                /**************************************/
                arrobj.page++;
//                if(arrobj.page >= Math.ceil(parseInt(total_days)/3)){
//                    return false;
//                }
                $.ajax({
                    url: url_route,
                    type: 'GET',
                    async:false,
                    dataType: 'json',
                    data:{'token':arrobj.token, 'area_id':arrobj.area_id,'advert_id':arrobj.advert_id,'advert_time':arrobj.advert_time, 'rate':arrobj.rate, 'start_at':arrobj.start_at, 'end_at':arrobj.end_at, 'page':arrobj.page},
                    success:function (phpdata) {
                        var aobj = phpdata.data;
                        var sphtml = '';
                        var dhtml = '';
                        for (var j=0; j<aobj.item.length; j++){
                            dhtml += '<li>'+aobj.item[j].date+'</li>';
                            sphtml += '<ul>';
                            for (var k=0; k<aobj.item[j].space_time_list.length; k++){
                                if(aobj.item[j].space_time_list[k] == '无余量'){
                                    sphtml += '<li class="noallowance">'+aobj.item[j].space_time_list[k]+'</li>';
                                }else{
                                    sphtml += '<li>'+aobj.item[j].space_time_list[k]+'</li>';
                                }
                            }
                            sphtml += '</ul>';
                        }
                        $("#date").append(dhtml);
                        $("#space").append(sphtml);
                        var  ul_leg=$('.ad_right_rq li').length;
                        $('.ad_right_list').css('width',ul_leg*82);
                        $('.ad_right_rq').css('width',ul_leg*82);
                    },error:function (phpdata) {

                    }
                })
                /**************************************/

                //获取个数

                var scroll_top = $(document).scrollTop();
                if(scroll_top>1){
                    $('.ad_right_rq').addClass('fixed_rh');
                    $('.ad_right_rqblank').show();
                }
                else{
                    $('.ad_right_rq').removeClass('fixed_rh');
                    $('.ad_right_rqblank').hide();
                }

            }
        });

        //选择区域
        $('.ad_left_input input').live('click',function(){
            var this_area = $(this).parent('span').next('p').html();
            var index_id = $(this).parents('li').index();
            if($(this).attr('checked')=="checked"){
                $(this).parent('.ad_left_input').css({"background":"url(/static/image/adv_nav_checked.png)","background-size":"contain","background-position":"50% 50%","background-repeat":"no-repeat"});
                var prev_area = $(this).attr('data-pre');
                var already_area = '<li id="'+index_id+'"><p class="lf">'+this_area+'</p><p class="rh">'+prev_area+'</p><span class="adv_list_delete"><img src="/static/image/adv_list_clear.png"></span></li>';
                $('.selected_area_ul').append(already_area);
                //取消全选
                var tag = true;
                $('.ad_left_input input').each(function () {
                    if($(this).prop('checked') !== true){
                       tag = false;
                       return;
                    }
                })
                if(tag){
                    $('.adver_bottom .selected_all_span').text('取消全选');
                    $('adver_select_all').attr('checked',true);
                }
            }else{
                $('.selected_all').find('span').html('全选').next().attr('checked', false);
                $(this).parent('.ad_left_input').css({"background":"url(/static/image/adv_nav_unchecked.png)","background-size":"contain","background-position":"50% 50%","background-repeat":"no-repeat"});
                $('.selected_area_ul #'+index_id+'').remove();
            }
            var counts = $('.selected_area_ul li').length;
            $('.selected_area_legth').html(counts);
        })
//	//全选
        $('.adver_select_all').click(function(){
            if($(this).attr("checked") == "checked"){
                $('.adver_bottom .selected_all_span').text('取消全选');
                $('.ad_left_input').find('input').attr("checked",true);
                $('.ad_left_input').css({"background":"url(/static/image/adv_nav_checked.png)","background-size":"contain","background-position":"50% 50%","background-repeat":"no-repeat"});
                //把所有的地区加入已选列表
                $('.selected_area_ul li').remove();
                $('.ad_left_input').each(function () {
                    var this_area = $(this).next('p').html();
                    var prev_area = $(this).find('input').attr('data-pre');
                    var index_id = $(this).parents('li').index();
                    $('.selected_area_ul').append('<li id="'+index_id+'"><p class="lf">'+this_area+'</p><p class="rh">'+prev_area+'</p><span class="adv_list_delete"><img src="/static/image/adv_list_clear.png"></span></li>');
                })
            }else{
                $('.adver_bottom .selected_all_span').text('全选');
                $('.ad_left_input').find('input').attr("checked",false);
                $('.ad_left_input').css({"background":"url(/static/image/adv_nav_unchecked.png)","background-size":"contain","background-position":"50% 50%","background-repeat":"no-repeat"});
                $('.selected_area_ul li').remove();
            }
            var counts = $('.selected_area_ul>li').length;
            $('.selected_area_legth').html(counts);
        })
        //已选择区域弹框
        //已选择弹框出现
        $('.adver_selected').click(function(){
            if($(this).hasClass('xuanle')){
                $(this).removeClass('xuanle');
                $('.selected_area_panel').hide();
                $(".mask").hide();
                $(".mask").css("height",0);
            }else{
                $(this).addClass('xuanle');
                $('.selected_area_panel').show();
                $(".mask").css('opacity','0.3').show();
                var ymheight=$(document).height()+ "px";
                $(".mask").css("height",ymheight);
            }

        })
        $(".mask").click(function(){
            $('.adver_selected').removeClass('xuanle');
            $('.selected_area_panel').hide();
            $(".mask").hide();
            $(".mask").css("height",0);
        })
        //清空
        $('.selected_area_panel .clears').click(function(){
            $('.selected_area_ul').find('li').remove();
            $('.selected_area_panel').hide();
            $('.adver_selected').removeClass('xuanle');
            $('.selected_area_panel').hide();
            $(".mask").hide();
            $(".mask").css("height",0);
            $('.selected_area_legth').html(0);
            $('.adver_select_all').attr('checked', false);
            $('.selected_all_span').html('全选');
            $('.ad_left_input').find('input').attr("checked",false);
            $('.ad_left_input').css({"background":"url(/static/image/adv_nav_unchecked.png)","background-size":"contain","background-position":"50% 50%","background-repeat":"no-repeat"});
        })
        //删除
        $('.adv_list_delete').live('click',function(){
            $(this).parent('li').remove();
            //上面相应的的地区取消选中
            var top_id = $(this).parent('li').attr('id');
            $('#area li:eq('+top_id+')').find('.ad_left_input').css({"background":"url(/static/image/adv_nav_unchecked.png)","background-size":"contain","background-position":"50% 50%","background-repeat":"no-repeat"}).find('input').attr('checked', false);
            //改变数量

            $('.selected_all').find('span').html('全选').next().attr('checked', false);

            $('.selected_area_legth').html(counts);
        })
        //修改页面的确认
        $('#modify_order').click(function () {
            var result = {"action":"clickconfirmmodifyorder"};
            var ua = navigator.userAgent.toLowerCase();
            if (/iphone|ipad|ipod/.test(ua)) {
                webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
            }else if(/android/.test(ua)) {
                window.jsObj.HtmlcallJava(JSON.stringify(result));
            }
        })
        //确认地区
        $('#confirm_area').click(function () {
            var arr = [];
            $('.ad_left_input input').each(function () {
                if($(this).prop('checked') == true){
                    arr.push($(this).val());
                }
            })
            var url_route = "<?=Yii::$app->params['baseApiUrl']?>"+"/member/"+member_id+"/order/area";

            if(!arr.length){
                $('.sy-installed-ts').show();
                setTimeout(function(){$('.sy-installed-ts').hide()},2000);
                return false;
            }
            var alen = arr[0].length;
            var area_type = 0;
            if(alen == 5){
                area_type = 1;
            }else if(alen == 7){
                area_type = 2;
            }else if(alen == 9){
                area_type = 3;
            }else if(alen == 12){
                area_type = 4;
            }
            if(arr.length > 1){
                area_ids = arr.join();
            }else{
                area_ids = arr[0];
            }

            $.ajax({
                url: url_route,
                type: 'POST',
                async: false,
                dataType: 'json',
                data:{'token':arrobj.token, 'area_id':area_ids, 'area_type':area_type,'type':1, 'advert_id':arrobj.advert_id, 'advert_time':arrobj.advert_time, 'rate':arrobj.rate, 'start_at':arrobj.start_at, 'end_at':arrobj.end_at},
                success:function (phpdata) {
                    if(phpdata.status == 200){
                        var result = {"action":"selectareawebview","status":"200"};
                        var ua = navigator.userAgent.toLowerCase();
                        if (/iphone|ipad|ipod/.test(ua)) {
                            webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
                        }else if(/android/.test(ua)) {
                            window.jsObj.HtmlcallJava(JSON.stringify(result));
                        }
                    }else {
                        $('.sy-installed-ts span').html('地区确认失败！');
                        $('.sy-installed-ts').show();
                        setTimeout(function(){$('.sy-installed-ts').hide()},2000);
                    }
                },error:function (phpdata) {
                    $('.sy-installed-ts span').html('地区确认失败！');
                    $('.sy-installed-ts').show();
                    setTimeout(function(){$('.sy-installed-ts').hide()},2000);
                }
            })
        })
        //点击返回交互
        $('#goback').next().click(function () {
            var result = {"action":"selectareawebviewgoback"};
            var ua = navigator.userAgent.toLowerCase();
            if (/iphone|ipad|ipod/.test(ua)) {
                webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
            }else if(/android/.test(ua)) {
                window.jsObj.HtmlcallJava(JSON.stringify(result));
            }
        })
    })

</script>
</body>
</html>
