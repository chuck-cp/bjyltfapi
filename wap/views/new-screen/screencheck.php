<!DOCTYPE html>
<html><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <?if($operate == 1):?>
        <?if($status == 2):?>
            <title>核对订单信息</title>
        <?else:?>
            <title>新店安装</title>
        <?endif;?>
    <?else:?>
        <?if($status == 1):?>
            <title>核对订单信息</title>
        <?else:?>
            <title>屏幕新增</title>
        <?endif;?>
    <?endif;?>

    <meta name="apple-touch-fullscreen" content="YES">
    <meta name="format-detection" content="telephone=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    <meta http-equiv="Expires" content="-1">
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no">
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/wait_examine.css?v=181221"/>
    <style>
        .yx_body{background: #F0F0F0;}
        #infoshow{background: #fff;}
        .yx_shenghe_green{width:100%;border-radius:0}
        .yx_shenghenr{padding-left: 0;}
        .yx_dphoto{width: 50%;}
        .yx_dphoto_img{width: 70px;height: 70px;margin:0;float: right;border-radius:0}
        .yx_shenghe_cheng{position: fixed;bottom: 0;width: 100%;}
        .yx_shenghenr .yx_sh_lb{padding: 10px;}
    </style>
</head>
<body class="yx_body">
<div class="sy_loadingpage" style="display: none;">
    <img src="/static/images/loading.gif">
    <p>正在加载...</p>
</div>
<div id="infoshow" style="">
    <div class="yx_shenghenr" style="    border-top: 10px solid #f2f2f2;">
        <div class="yx_sh_lb">
            <p class="lf_mc">店铺联系人</p>
            <p class="rl_nr" id="contacts_name">刚刚我说过</p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">手机号码</p>
            <p class="rl_nr" id="contacts_mobile">13796651342</p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">公司名称</p>
            <p class="rl_nr" id="company_name">刚刚发现一条狗</p>
        </div>
        <div class="yx_sh_lb" >
            <p class="lf_mc">店铺名称</p>
            <p class="rl_nr" id="shop_name">刚刚发现的是个</p>
        </div>
        <div class="yx_sh_lb"">
            <p class="lf_mc">店铺地址</p>
            <p class="syadd_rl_nr">
                <span class="txt" id="area">北京市北京市丰台区右安门街道</span>
            </p>
        </div>
        <!--详细地址注意：-->
        <p class="yx_xx_address" id="address">258</p>
        <p class="" style="background-color: #f2f2f2 ;border: none;height:10px; float: right;width: 100%"></p>
        <div class="yx_sh_lb">
            <p class="lf_mc">店铺面积</p>
            <p class="rl_nr"><span id="acreage"></span>平米</p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">安装数量</p>
            <p class="rl_nr"><span id="screen_number"></span>台</p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">镜面数量</p>
            <p class="rl_nr"><span id="mirror_account"></span>面</p>
        </div>
        <div class="yx_sh_lb" >
            <p class="lf_mc">屏幕运行时间</p>
            <p class="rl_nr" id="screen_start_at">9.00-21.00</p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">业务合作人</p>
            <p class="rl_nr" id="member_name">安装人吴</p>
        </div>
        <div class="yx_sh_lb" style="border-bottom-width:10px;">
            <p class="lf_mc">联系电话</p>
            <p class="rl_nr" id="member_mobile">18410060001</p>
        </div>

        <div class="yx_sh_lb">
            <p class="lf_mc">店铺门脸照片</p>
            <p class="rl_nr">
                <img class="yx_dphoto_img" id="shop_image" src="" >
            </p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">室内全景照片</p>
            <p class="rl_nr">
                <img id="panorama_image" class="yx_dphoto_img" src="" >
            </p>
        </div>
    </div>

    <!--待审核-->
    <div class="yx_shehe_jg" id="daishen" style="display: none;">
        <a href="javascript:;" class="yx_shenghe_hui">待审核</a>
    </div>
    <!--订单已审核-->
    <div class="yx_shehe_jg" id="yishengshenhe" style="display: none;">
        <a href="javascript:;" class="yx_shenghe_cheng">订单已审核</a>
    </div>
    <!--订单已核对-->
    <div class="yx_shehe_jg" id="yitongguo">
        <a href="/screen/screenconfirm?shopid=<?=$shopid?>&token=<?=$token?>&dev=<?=$dev?>&change=<?=$change?>&replace_id=<?=$replace_id?>&operate=<?=$operate?>" class="yx_shenghe_green">订单已核对</a>
    </div>
    <!--激活失败更换屏幕-->
    <div class="yx_shehe_jg" id="update" style="display: none;">
        <a href="/screen/screenover?shopid=<?=$shopid?>&operate=<?=$operate?>&replace_id=<?=$replace_id?>&token=<?=$token?>&istrue=0" class="yx_shenghe_cheng">激活失败更换屏幕</a>
    </div>
    <!--修改安装信息-->
    <div class="yx_shehe_jg" id="weitongguo" style="display: none;">
        <p class="yx_yuanyin" id="wtgyuanying"></p>
<!--        <a href="/screen/screen-new-update?shopid=--><?//=$shopid?><!--&token=--><?//=$token?><!--&dev=--><?//=$dev?><!--&replace_id=--><?//=$replace_id?><!--&operate=--><?//=$operate?><!--" class="yx_shenghe_cheng">修改安装信息</a>-->
        <a href="/screen/screen-new-update?shopid=<?=$shopid?>&token=<?=$token?>&dev=<?=$dev?>&replace_id=<?=$replace_id?>&operate=<?=$operate?>" class="yx_shenghe_cheng">修改安装信息</a>
        <br />
    </div>
</div>
<br>
<br>
<br>
<br>
<br>
<br>
<div class="mask"></div>
<!--确定弹框-->
<div class="sy_examin_panel">
    <div class="img">
        <div class="examcon">
            <p class="con">提交成功等待审核</p>
            <p class="btn"><a href="http://wap.bjyl.com/screen/screenshoplist?token=4QBxiNnvXjK3gzgsXBFtn5DDyrSC4Y-9">确定</a></p>
        </div>
    </div>
</div>
<style>
    #overlay {
        background: #000;
        filter: alpha(opacity=50); /* IE的透明度 */
        opacity: 0.5;  /* 透明度 */
        display: none;
        position: absolute;
        top: 0px;
        left: 0px;
        width: 100%;
        height: 180%;
        z-index: 100; /* 此处的图层要大于页面 */
    }
</style>
<!--<图片遮罩>-->
<div id="overlay"></div>
<!--<点击后的图片显示在遮罩中间>-->
<div id="bigpic" style="visibility:hidden;z-index: -100;position: absolute;left: 0;top: 0;">
    <img src="" alt="">
</div>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script type="text/javascript" src="/static/js/swiper.min.js" ></script>
<script type="text/javascript" src="/static/js/tankuangpub.js" ></script>
<script type="text/javascript" src="/static/js/picHideShow.js?v=201905160164"></script>
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
        var uploadimgW=$('.yx_dphoto_img').find('.imgspace').width();
        $.ajax({
            url:baseApiUrl+"/screeninstall/screencheck",
            type:"get",
            dataType:"json",
            data:{id:<?=$shopid?>,token:'<?=$token?>',replace_id:'<?=$replace_id?>'},
            success:function(data) {
                if(data.status==200 && data.data!=null){
                    //如果新店入驻 1、申请未通过 2、待安装 3、安装待审核 4、安装未通过
                    //如果replace订单安装反馈待审核  1.待安装(指派)，2.待审核，3.审核未通过，
                    if(data.data.maintain_type == 1){
                        if(data.data.status == 3){
                            if(data.data.screen_status==1){
                                $('#yitongguo').hide();
                                $('#daishen').show();
                            }else{
                                $('#yitongguo').hide();
                                $('#update').show();
                            }
                        }
                        //如果订单安装反馈审核失败
                        if(data.data.status == 4){
                            $('#wtgyuanying').html("未通过原因："+data.data.examine_desc);
                            $('#yitongguo').hide();
                            $('#weitongguo').show();
                        }
                    }else{
                        if(data.data.status == 2){
                            if(data.data.screen_status==1){
                                $('#yitongguo').hide();
                                $('#daishen').show();
                            }else{
                                $('#yitongguo').hide();
                                $('#update').show();
                            }
                        }
                        //如果订单安装反馈审核失败
                        if(data.data.status == 3){
                            $('#wtgyuanying').html("未通过原因："+data.data.examine_desc);
                            $('#yitongguo').hide();
                            $('#weitongguo').show();
                        }
                    }

                    $("#apply_code").html(data.data.apply_code);
                    $("#apply_name").html(data.data.apply_name);
                    $("#member_name").html(data.data.member_name);
                    $("#member_mobile").html(data.data.member_mobile);
                    $("#yx_yhphone").html(data.data.apply_mobile);
                    $("#area").html(data.data.area_name);
                    $("#address").html(data.data.address);
                    $("#shop_name").html(data.data.name);
                    $("#company_name").html(data.data.company_name);
                    $("#acreage").html(data.data.acreage);
                    $("#screen_number").html(data.data.screen_number);
                    $("#mirror_account").html(data.data.mirror_account);
                    $("#shop_image").attr('src',data.data.shop_image+"?imageView2/0/w/"+uploadimgW);
                    $("#panorama_image").attr('src',data.data.panorama_image+"?imageView2/0/w/"+uploadimgW);
                    $("#bigpic img").css('width',parseInt(pageWidth()));
                    $("#install_team_id").html(data.data.install_team_id);
                    $("#install_member_id").html(data.data.install_member_id);
                    var stattime=data.data.screen_start_at;
                    var endtime=data.data.screen_end_at;
                    $("#screen_start_at").html(stattime+"-"+endtime);
                    $("#contacts_name").html(data.data.contacts_name);
                    $("#contacts_mobile").html(data.data.contacts_mobile);
                    $("#member_name").html(data.data.member_name);
                    $("#member_mobile").html(data.data.member_mobile);
                    $(".sy_loadingpage").hide();
                    $("#infoshow").show();
                }else{
                    conftankuangparam("sy_examin_panel","数据有误,请重试！");
                }
            }
        });

    });
    window.onload = function () {
            HitPic();
    }
</script>


</body></html>