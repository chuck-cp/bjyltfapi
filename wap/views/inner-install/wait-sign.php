<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>待签约店铺</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" type="text/css" href="/static/css/reset.css"/>
    <link rel="stylesheet"  href="/static/css/sy_xgnewfeedback.css" />
    <style>
        .sy_screamins .cooperate{display: block}
        .sqdp{padding: 2% 6%;background: #ff8302;color: #fff;display: inline-block;text-align: center;margin-bottom: 8%}
    </style>
</head>
<body class="sy-body">
<div class="sy_loadingpage">
    <img src="/static/images/loading.gif">
    <p>正在加载...</p>
</div>
<!--暂无待安装业务-->
<div class='sy_nowaitins_shop' style="display: none;">
    <p class='img'>
        <img src='/static/images/waitinstalled_shop.png'>
    </p>
    <p class='txt'>暂无待签约店铺</p>
</div>
<div class="sy_screamins" style="display: none;">
    <ul id="listli">
        <!--样式 已安装 yeted 屏幕待激活/待安装/安装待审核nowaited nopass未通过-->
    </ul>
</div>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script>
    var token = "<?=$token?>";
    //获取数据
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    var first = true;
    //初始化数据
    function showshop(page,first){
        $.ajax({
            url:baseApiUrl+"/shop/contract/list?page="+page+"&token=<?=$token?>",
            type:"get",
            dataType:"json",
            success:function (phpdata) {
                console.log(phpdata);
                if(phpdata.status == 200){
                        var shtml = '';
                        var status = '';
                        var source = '';
                        //0、未签约 1、已签约 2、签约失败 订单类型(1、推荐订单 2、指派订单)
                        if(phpdata.data !== null){
                            $.each(phpdata.data,function (i,value) {
                                switch (value.order_source){
                                    case '1':
                                        source="<p>推荐订单</p> ";
                                        break;
                                    case '2':
                                        source="<p>指派订单</p>";
                                        break;
                                }
                                switch (value.status){
                                    case '0':
                                        status="<div class='cooperate nowaited'><p><a class='sqdp' href='/shop/choose-shop-type?token="+token+"&active_id="+value.id+"'>店铺申请</a></p>"+source+"</div> ";
                                        break;
                                    case '1':
                                        status="<div class='cooperate nowaited'><p><a class='sqdp' href='javascript:void(0);'>已签约</a></p>"+source+"</div> ";
                                        break;
                                    case '2':
                                        status="<div class='cooperate nowaited'><p><a class='sqdp' href='javascript:void(0);'>签约失败</a></p>"+source+"</div> ";
                                        break;
                                }

                                shtml = shtml+"<li class='listitem clearfix'>" +
                                    "<a href='/inner-install/active-shop-info?active_id="+value.id+"&token=<?=$token?>'> " +
                                    "<div class='img'>" +
                                    "<img src='"+value.shop_image+"?imageView2/0/w/66'> " +
                                    "</div> " +
                                    "<div class='sy_scins_txt'> " +
                                    "<p class='name'>"+value.shop_name+"</p> " +
                                    "<p class='adress'>"+value.area_name+"</p> " +
                                    "<p class='street'>"+value.address+"</p> " +
                                    "</div> " + status +
                                    "</a> " +
                                    "</li>";
                            })
                            $("#listli").append(shtml);
                            $(".sy_loadingpage").hide();
                            $(".sy_screamins").show();
                        }else {
                            if(first){
                                $(".sy_loadingpage").hide();
                                $('.sy_nowaitins_shop').show();
                            }else {
                                $(".sy_loadingpage").hide();
                                if($('#no-more').length < 1){
                                    $("#listli").append('<p id="no-more" style="color: #afafaf; text-align: center;margin-top: 6%; display: block;">没有更多店铺啦</p>');
                                }
                            }
                        }
                }else {
                    $(".sy_loadingpage").hide();
                    //$(".sy_nowaitins_shop").show();
                    if($('.last').length < 1){
                        $("#listli").append('<p id="no-more" style="color: #afafaf; text-align: center;margin-top: 6%; display: block;">没有更多店铺啦</p>');
                    }
                }

            }
        });
    }
    showshop(page=1,true);
    //滚动加载
    $(window).scroll(function(){
        var doc_height = $(document).height();
        var scroll_top = $(document).scrollTop();
        var window_height = $(window).height();
        if(scroll_top + window_height >= doc_height){
            page ++;
            if($('#no-more').length < 1){
                showshop(page,false);
            }
        }
    });
</script>


</body>
</html>
