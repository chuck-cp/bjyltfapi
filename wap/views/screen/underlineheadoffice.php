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
    <style>
         .yx_dphoto_img{ width: 53.4%;position:relative; margin:10px 23.3% 0; overflow: hidden; border-radius: 5px;}
         .yx_dphoto_img .imgspace{ width: 100%; height: auto;}
         .yx_dphoto_img .img{ position: absolute; left: 0; top: 0; width: 100%; height: 100%;}
         #lsit{overflow: hidden}
    </style>
</head>
<body class="yx_body">
<div class="sy_loadingpage" >
    <img src="/static/images/loading.gif">
    <p>正在加载...</p>
</div>
<!--暂无待安装业务-->
<div id="sy-wrapper" style="display: none;">
    <div class="yx_shenghenr">
        <div class="yx_sh_lb">
            <p class="lf_mc">个人信息</p>
            <p class="rl_nr"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">法人代表</p>
            <p class="rl_nr" id="name"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">身份证号码</p>
            <p class="rl_nr" id="identity_card_num"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">手机号码</p>
            <p class="rl_nr" id="mobile"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">公司信息</p>
            <p class="rl_nr"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">公司名称</p>
            <p class="rl_nr" id="company_name"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">统一社会信用代码</p>
            <p class="rl_nr" id="registration_mark"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">公司地址</p>
            <p class="rl_nr">
                <span class="txt" id="company_area_name"></span>
            </p>
        </div>
        <!--详细地址注意：-->
        <p class="yx_xx_address" id="company_address"></p>

        <div class="yx_sh_lb">
            <p class="yx_dphoto">法人身份证照片</p>
            <p class="yx_dphoto_img">
                <img src="/static/images/azpm_spacing.png" class="imgspace">
                <img id="identity_card_front" src="" class="img">
            </p>
            <p class="yx_dphoto_img">
                <img src="/static/images/azpm_spacing.png" class="imgspace">
                <img id="identity_card_back" src="" class="img">
            </p>
        </div>
        <div class="yx_sh_lb no_biankuang">
            <p class="yx_dphoto">营业执照照片</p>
            <p class="yx_dphoto_img">
                <img src="/static/images/azpm_spacing.png" class="imgspace">
                <img id="business_licence" src="" class="img">
            </p>
        </div>
        <div class="yx_sh_lb no_biankuang yincang" id="qita" style="display: none">
            <p class="yx_dphoto">其它</p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">分店信息</p>
            <p class="rl_nr"></p>
        </div>
        <div id="lsit">
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc fengdian" id="fendiannumber" style="width: 100%; text-align: center">共10家分店</p>
        </div>
    </div>

    <!--待审核-->
    <div class="yx_shehe_jg" id="daishen">
        <a href="javascript:;" class="yx_shenghe_hui">待审核</a>
    </div>
    <!--修改安装信息-->
    <div class="yx_shehe_jg" id="weitongguo" style="display: none">
        <p class="yx_yuanyin">未通过原因：店铺图片不清晰</p>
        <a href="/shop/head-office-modify?token=<?=$token?>&headquarters_id=<?=$headquarters_id?>&dev=<?=$dev?>" class="yx_shenghe_cheng">修改总部申请</a>
    </div>
</div>
<div class="mask"></div>
<!--确定弹框-->
<div class="sy_examin_panel">
    <div class="img">
        <div class="examcon">
            <p class="con">提交成功等待审核</p>
            <p class="btn"><a href="/shop/head-office-modify?token=<?=$token?>&headquarters_id=<?=$headquarters_id?>&dev=<?=$dev?>">确定</a></p>
        </div>
    </div>
</div>
<p class="sy-installed-ts">提交成功</p>
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
        var uploadimgW=$('.imgspace').width();
        $.ajax({
            url:baseApiUrl+"/screeninstall/headoffice",
            type:"get",
            dataType:"json",
            data:{id:<?=$headquarters_id?>,token:'<?=$token?>'},
            success:function(data) {
                console.log(data);
                if(data.status==200&&data.data!=null){
                      $("#name").html(data.data.name);
                      $("#identity_card_num").html(data.data.identity_card_num);
                      $("#mobile").html(data.data.mobile);
                      $("#company_name").html(data.data.company_name);
                      $("#registration_mark").html(data.data.registration_mark);
                      $("#company_area_name").html(data.data.company_area_name);
                      $("#company_address").html(data.data.company_address);
                      $("#identity_card_front").attr('src',data.data.identity_card_front+"?imageView2/0/w/"+uploadimgW);
                      $("#identity_card_back").attr('src',data.data.identity_card_back+"?imageView2/0/w/"+uploadimgW);
                      $("#business_licence").attr('src',data.data.business_licence+"?imageView2/0/w/"+uploadimgW);
                      var listhtml="";
                    $.each(data.data.list,function(i,value){
                        listhtml=listhtml+"<div class='yx_sh_lb'> " +
                        "<p class='lf_mc'>分店名称</p> " +
                        "<p class='rl_nr'>"+value.branch_shop_name+"</p> " +
                        "</div> " +
                        "<div class='yx_sh_lb'> " +
                        "<p class='lf_mc'>分店地址</p> " +
                        "<p class='rl_nr'> " +
                        "<span class='txt'>"+value.branch_shop_area_name+"</span> " +
                        "</p> " +
                        "</div> " +
                        "<div class='yx_xx_address'>"+value.branch_shop_address+"</div>";
                    });
                    $("#lsit").html(listhtml);
                    $("#fendiannumber").html("共"+data.data.list.length+"家分店");
//                    //按订单状态显示按钮
                    if(data.data.examine_status==2){
                        $(".yx_yuanyin").html("未通过原因:"+data.data.examine_desc);
                        $('#daishen').hide();
                        $('#weitongguo').show();
                    }
                    $(".sy_loadingpage").hide();
                    $("#sy-wrapper").show();
                    //其他
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
                }else{
                    conftankuangparam("sy_examin_panel","数据有误,请重试！");
//                    $('.sy-installed-ts').text('数据不存在');
//                    tippanel();
//                    setTimeout(function() {  location.href="" },2000);
                }
            }
        });
    });
</script>
</body>
</html>
