<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>核对订单信息</title>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/wait_examine.css?v=181221"/>
</head>
<body class="yx_body">
<div class="sy_loadingpage">
    <img src="/static/images/loading.gif">
    <p>正在加载...</p>
</div>
<div id="infoshow" style="display: none;">
    <div class="yx_shenghenr">
        <div class="yx_sh_lb">
            <p class="lf_mc">订单号</p>
            <p class="rl_nr" id="apply_code"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">业务员姓名</p>
            <p class="rl_nr" id="member_name"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">业务员手机号</p>
            <p class="rl_nr" id="member_mobile"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">店铺联系人</p>
            <p class="rl_nr" id="contacts_name"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">手机号码</p>
            <p class="rl_nr" id="contacts_mobile"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">店铺地址</p>
            <p class="syadd_rl_nr">
                <span class="txt" id="area"></span>
            </p>
        </div>
        <!--详细地址注意：-->
        <p class="yx_xx_address" id="address"></p>

        <div class="yx_sh_lb">
            <p class="lf_mc">公司名称</p>
            <p class="rl_nr" id="company_name"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">店铺名称</p>
            <p class="rl_nr" id="shop_name"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">店铺面积</p>
            <p class="rl_nr" id="acreage"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">安装数量</p>
            <p class="rl_nr" id="screen_number"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">镜面数量</p>
            <p class="rl_nr" id="mirror_account"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">屏幕运行时间</p>
            <p class="rl_nr" id="screen_start_at"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="yx_dphoto">店铺门脸照片</p>
            <div class="yx_dphoto_img">
                <img src="/static/images/azpm_spacing.png" class="imgspace">
                <img class="img" id="shop_image" src="">
            </div>
        </div>
        <div class="yx_sh_lb no_biankuang">
            <p class="yx_dphoto">室内全景照片</p>
            <div class="yx_dphoto_img">
                <img src="/static/images/azpm_spacing.png" class="imgspace">
                <img id="panorama_image" class="img"  src="">
            </div>
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
        <a href="/screen/screenconfirm?shopid=<?=$shopid?>&token=<?=$token?>&dev=<?=$dev?>&change=<?=$change?>" class="yx_shenghe_green">订单已核对</a>
    </div>
    <!--激活失败更换屏幕-->
    <div class="yx_shehe_jg" id="update" style="display: none;">
        <a href="/screen/screenover?shopid=<?=$shopid?>&token=<?=$token?>&istrue=0" class="yx_shenghe_cheng">激活失败更换屏幕</a>
    </div>
    <!--修改安装信息-->
    <div class="yx_shehe_jg" id="weitongguo" style="display: none;">
        <p class="yx_yuanyin" id="wtgyuanying"></p>
        <a href="/screen/screenupdateimg?shopid=<?=$shopid?>&token=<?=$token?>&dev=<?=$dev?>" class="yx_shenghe_cheng">修改安装信息</a>
    </div>
</div>
<div class="mask"></div>
<!--确定弹框-->
<div class="sy_examin_panel">
    <div class="img">
        <div class="examcon">
            <p class="con">提交成功等待审核</p>
            <p class="btn"><a href="/screen/screenshoplist?token=<?=$token?>">确定</a></p>
        </div>
    </div>
</div>
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
        var uploadimgW=$('.yx_dphoto_img').find('.imgspace').width();
        $.ajax({
            url:baseApiUrl+"/screeninstall/screencheck",
            type:"get",
            dataType:"json",
            data:{id:<?=$shopid?>,token:'<?=$token?>'},
            success:function(data) {
                console.log(data);
                if(data.status==200 && data.data!=null){
                    //如果订单安装反馈待审核
                    if(data.data.status==3){
                        if(data.data.screen_status==1){
                            $('#yitongguo').hide();
                            $('#daishen').show();
                        }else{
                            $('#yitongguo').hide();
                            $('#update').show();
                        }
                    }
                    //如果订单安装反馈审核失败
                    if(data.data.status==4 && data.data.install_status==2){
                        $('#wtgyuanying').html("未通过原因："+data.data.examine_desc);
                        $('#yitongguo').hide();
                        $('#weitongguo').show();
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
</script>
</body>
</html>
