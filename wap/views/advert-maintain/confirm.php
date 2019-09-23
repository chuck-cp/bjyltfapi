<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title>广告维护</title>
    <meta name="apple-touch-fullscreen" content="YES">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="Expires" content="-1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/wait_examine.css?v=181221"/>
</head>
<body class="yx_body">
<div class="sy_loadingpage" style="display: none;">
    <img src="/static/images/loading.gif">
    <p>正在加载...</p>
</div>
<div id="infoshow" style="">
    <div class="yx_shenghenr" style="border-top: 10px solid #f2f2f2;">
        <div class="yx_sh_lb">
            <p class="lf_mc">店铺名称</p>
            <p class="rl_nr" id="shop_name"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">地址</p>
            <p class="syadd_rl_nr">
                <span class="txt" id="area"></span>
            </p>
        </div>
        <p class="yx_xx_address" id="address" ></p>

        <div class="yx_sh_lb">
            <p class="lf_mc">店铺联系人</p>
            <p class="rl_nr" id="contact_name"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">手机号码</p>
            <p class="rl_nr" id="contact_mobile"></p>
        </div>
        <p class="" style="background-color: #f2f2f2 ;border: none;height:10px; float: right;width: 100%"></p>
        <div class="yx_sh_lb">
            <p class="lf_mc">业务对接人</p>
            <p class="rl_nr"><span id="djr"></span></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">联系电话</p>
            <p class="rl_nr"><span id="djr_mobile"></span></p>
        </div>
    </div>
    <!--订单已审核-->
    <div class="yx_shehe_jg" id="yishengshenhe" style="">
        <a href="javascript:;" class="yx_shenghe_cheng">确认订单</a>
    </div>
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<div class="mask"></div>

<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script type="text/javascript" src="/static/js/swiper.min.js" ></script>
<script type="text/javascript" src="/static/js/tankuangpub.js" ></script>
<script>
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    var swiper = new Swiper('.sy-shoppoto .swiper-container', {
        pagination: '.sy-shoppoto .swiper-pagination',
        slidesPerView: 'auto',
        paginationClickable: true,
        spaceBetween: 0,
        resistanceRatio : 0,
        longSwipesRatio : 0
    });
    function tippanel(){
        $('.sy-installed-ts').show();
        setTimeout(function(){$('.sy-installed-ts').hide()},2000);
    }
    $(function(){
        $.ajax({
            url:baseApiUrl+"/screen-advert-maintain/get-maintain-info",
            type:"get",
            dataType:"json",
            data:{id:<?=$id?>,shop_id:<?=$shop_id?>,token:'<?=$token?>'},
            success:function(data) {
                if(data.status==200 && data.data!=null){
                    $("#shop_name").html(data.data.shop_name);
                    $("#area").html(data.data.shop_area_name);
                    $("#address").html(data.data.shop_address);
                    $("#contact_name").html(data.data.apply_name);
                    $("#contact_mobile").html(data.data.apply_mobile);
                    $("#djr").html(data.data.member_name);
                    $("#djr_mobile").html(data.data.member_mobile);
                }else{
                    conftankuangparam("sy_examin_panel","数据有误,请重试！");
                }
            }
        });
    });
    //点击确认订单跳转
    $('#yishengshenhe').bind('click',function () {
        window.location.href = '/screen/main-detail?token=<?=$token?>&id=<?=$id?>&mongo_id=<?=$mongo_id?>';
    })
</script>


</body></html>