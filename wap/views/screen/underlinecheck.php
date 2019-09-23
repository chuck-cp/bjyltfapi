<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>核对信息</title>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/wait_examine.css"/>
<!--    <link rel="stylesheet" href="/static/css/lsy-install.css?v=1.5" />-->
<!--    <link rel="stylesheet" href="/static/css/swiper.min.css" />-->
    <style>
        .sy_addnewphone{ padding: 10px 0;}
        .sy_addnewphone h5{ font-size: 15px; line-height: 35px; border-bottom: 1px solid #333;}
        .sy_addcon{ padding: 5px 0;}
        .sy_addcon p{width: 50%; float: left;line-height: 30px;}
        .sy_addcon p:first-child{ text-align: left;}
        .sy_addcon p:last-child{ text-align:right;}
        .sy_swzp{ line-height: 30px;}
    </style>
</head>
<body class="yx_body">
<div class="sy_loadingpage" >
    <img src="/static/images/loading.gif">
    <p>正在加载...</p>
</div>
<div id="infoshow" style="display: none;">
    <div class="yx_shenghenr">
        <div class="yx_sh_lb">
            <p class="lf_mc">订单号</p>
            <p class="rl_nr" id="apply_code"></p>
        </div>
        <div class="yx_sh_lb yincang">
            <p class="lf_mc">法人代表</p>
            <p class="rl_nr" id="apply_name"></p>
        </div>
        <div class="yx_sh_lb yincang">
            <p class="lf_mc">身份证号码</p>
            <p class="rl_nr" id="identity_card_num"></p>
        </div>
        <div class="yx_sh_lb yincang">
            <p class="lf_mc">手机号码</p>
            <p class="rl_nr" id="apply_mobile"></p>
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
            <p class="lf_mc">公司名称</p>
            <p class="rl_nr" id="company_name"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">店铺名称</p>
            <p class="rl_nr" id="name"></p>
        </div>
        <div class="yx_sh_lb yincang" >
            <p class="lf_mc">统一社会信用代码</p>
            <p class="rl_nr" id="registration_mark"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">安装地址</p>
            <p class="syadd_rl_nr">
                <span class="txt" id="area_name"></span>
            </p>
        </div>
        <!--详细地址注意：-->
        <p class="yx_xx_address" id="address"></p>
        <div class="yx_sh_lb">
            <p class="lf_mc">店铺面积</p>
            <p class="rl_nr" id="acreage"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">安装数量</p>
            <p class="rl_nr" id="apply_screen_number"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">镜面数量</p>
            <p class="rl_nr" id="mirror_account"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">屏幕运行时间</p>
            <p class="rl_nr" id="screen_start_at"></p>
        </div>
        <div class="yx_sh_lb yincang">
            <p class="yx_dphoto">法人代表</p>
            <div class="yx_dphoto_img">
                <img src="/static/images/azpm_spacing.png" class="imgspace">
                <img class="img" id="identity_card_front" src="">
            </div>
            <div class="yx_dphoto_img">
                <img src="/static/images/azpm_spacing.png" class="imgspace">
                <img class="img" id="identity_card_back" src="">
            </div>
        </div>
        <div class="yx_sh_lb yincang" id="lianxiren" style="display: none">
            <p class="yx_dphoto">店铺联系人(选填)</p>
            <div class="yx_dphoto_img">
                <img src="/static/images/azpm_spacing.png" class="imgspace">
                <img class="img" id="agent_identity_card_front" src="">
            </div>
            <div class="yx_dphoto_img">
                <img src="/static/images/azpm_spacing.png" class="imgspace">
                <img class="img" id="agent_identity_card_back" src="">
            </div>
        </div>
        <div class="yx_sh_lb yincang">
            <p class="yx_dphoto">营业执照</p>
            <div class="yx_dphoto_img">
                <img src="/static/images/azpm_spacing.png" class="imgspace">
                <img class="img" id="business_licence" src="">
            </div>
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
        <div class="yx_sh_lb no_biankuang yincang"  id="shouquan" style="display: none">
            <p class="yx_dphoto">授权证明</p>
        </div>
        <div class="yx_sh_lb no_biankuang yincang" id="qita" style="display: none">
            <p class="yx_dphoto">其它</p>
        </div>
        <!--安装人信息 -->
        <div class="sy_addnewphone" style="display: none;"></div>
    </div>
            <div class="yx_shehe_jg" id="daishen">
                <a href="javascript:;" class="yx_shenghe_hui">待审核</a>
            </div>
    <div class="yx_shehe_jg" id="weitongguo" style="display: none;">
                <p class="yx_yuanyin" id="wtgyuanying"></p>
                <a href="javascript:" class="yx_shenghe_cheng">修改店铺申请</a>
            </div>
    <div class="yx_shehe_jg" id="yishengshenhe" style="display: none;">
                <a href="javascript:;" class="yx_shenghe_hui">待安装</a>
            </div>
</div>
<div class="mask"></div>
<!--确定弹框-->
<div class="sy_examin_panel">
    <div class="img">
        <div class="examcon">
            <p class="con">提交成功等待审核</p>
            <p class="btn"><a href="/inner-install?token=<?=$token?>">确定</a></p>
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
        var uploadimgW=$('.imgtu').find('.imgspace').width();
        $.ajax({
            url:baseApiUrl+"/screeninstall/underlinecheck",
            type:"get",
            dataType:"json",
            data:{id:<?=$shopid?>,token:'<?=$token?>'},
            success:function(data) {
                //console.log(data)
                if(data.status==200 && data.data!=null){

                    $("#apply_code").html(data.data.apply_code);
                    $("#apply_name").html(data.data.apply_name);
                    $("#identity_card_num").html(data.data.identity_card_num);
                    $("#apply_mobile").html(data.data.apply_mobile);
                    $("#contacts_name").html(data.data.contacts_name);
                    $("#contacts_mobile").html(data.data.contacts_mobile);
                    $("#company_name").html(data.data.company_name);
                    $("#name").html(data.data.name);
                    $("#registration_mark").html(data.data.registration_mark);
                    $("#area_name").html(data.data.area_name);
                    $("#address").html(data.data.address);
                    $("#acreage").html(data.data.acreage);
                    $("#apply_screen_number").html(data.data.screen_number);
                    $("#mirror_account").html(data.data.mirror_account);
                    var stattime=data.data.screen_start_at;
                    var endtime=data.data.screen_end_at;
                    $("#screen_start_at").html(stattime+'-'+endtime);
                    $("#identity_card_front").attr('src',data.data.identity_card_front+"?imageView2/0/w/"+uploadimgW);
                    $("#identity_card_back").attr('src',data.data.identity_card_back+"?imageView2/0/w/"+uploadimgW);
                    if(data.data.agent_identity_card_front!=""){ //店铺联系人身份证为空不显示
                        $("#agent_identity_card_front").attr('src',data.data.agent_identity_card_front+"?imageView2/0/w/"+uploadimgW);
                        $("#agent_identity_card_back").attr('src',data.data.agent_identity_card_back+"?imageView2/0/w/"+uploadimgW);
                        $("#lianxiren").show();
                    }
                    $("#business_licence").attr('src',data.data.business_licence+"?imageView2/0/w/"+uploadimgW);
                    $("#shop_image").attr('src',data.data.shop_image+"?imageView2/0/w/"+uploadimgW);
                    $("#panorama_image").attr('src',data.data.panorama_image+"?imageView2/0/w/"+uploadimgW);
                    if(data.data.authorize_image != "" && data.data.authorize_image != null){
                        var authorize_image = data.data.authorize_image.split(",");
                        var image = '';
                        for(image in authorize_image){
                            image = authorize_image[image] + "?imageView2/0/w/"+uploadimgW;
                            $('#shouquan').append('<div class="yx_dphoto_img"><img src="/static/images/azpm_spacing.png" class="imgspace"> <img id="panorama_image" class="img"  src="'+image+'"></div>');
                        }
                        $("#other_image").attr('src',data.data.other_image+"?imageView2/0/w/"+uploadimgW);
                        $("#shouquan").show();
                    }
                    //其它信息图片为空不显示
                    if(data.data.other_image!="" && data.data.other_image!=null){
                        var other_image = data.data.other_image.split(",");
                        var image = '';
                        for(image in other_image){
                            image = other_image[image] + "?imageView2/0/w/"+uploadimgW;
                            $('#qita').append('<div class="yx_dphoto_img"><img src="/static/images/azpm_spacing.png" class="imgspace"> <img id="panorama_image" class="img"  src="'+image+'"></div>');
                        }
                        $("#other_image").attr('src',data.data.other_image+"?imageView2/0/w/"+uploadimgW);
                        $("#qita").show();
                    }
                    //如果订单状态未通过
                    if(data.data.status==1){
                        if(data.data.shop_operate_type == 3){
                            //连锁店信息
                            $('.yx_shenghe_cheng').attr('href','/shop/branch-install-modify?shop_id=<?=$shopid?>&token=<?=$token?>&type=yewu')
                        }else{
                            $('.yx_shenghe_cheng').attr('href','/shop/modify-shop?shop_id=<?=$shopid?>&token=<?=$token?>&type=yewu')
                        }
                        $('#wtgyuanying').html("未通过原因："+data.data.examine_desc);
                        $('#daishen').hide();
                        $('#weitongguo').show();
                    }
                    //如果订单状态未通过
                    if(data.data.status==2){
                        if(data.data.install_member_name==""){
                          var html="  <h5>安装人信息</h5>" +
                              "<p class='sy_swzp'>安装人尚未指派</p>";
                        }else{
                            var html="  <h5>安装人信息</h5>" +
                                "<div class='sy_addcon' >" +
                            "<p>"+data.data.install_member_name+"</p> " +
                            "<p>"+data.data.install_mobile+"</p> " +
                            "</div>";
                        }
                        $('.sy_addnewphone').html(html);
                        $('.sy_addnewphone').show();

                        $('.yincang').hide();
                        $('#daishen').hide();
                        $('#yishengshenhe').show();
                    }
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
