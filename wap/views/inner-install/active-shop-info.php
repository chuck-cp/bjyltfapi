<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>待签约店铺详情</title>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/wait_examine.css"/>
</head>
<body class="yx_body">
<div class="sy_loadingpage">
    <img src="/static/images/loading.gif">
    <p>正在加载...</p>
</div>
<div id="infoshow" style="display: none;">
    <div class="yx_shenghenr">
        <div class="yx_sh_lb">
            <p class="lf_mc">店铺名称</p>
            <p class="rl_nr" id="member_name"></p>
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
            <p class="lf_mc">镜面数量</p>
            <p class="rl_nr" id="screen_number"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">安装地址</p>
            <p class="syadd_rl_nr">
                <span class="txt" id="area"></span>
            </p>
        </div>
        <!--详细地址注意：-->
        <p class="yx_xx_address" id="address"></p>


        <div class="yx_sh_lb">
            <p class="lf_mc">推荐人</p>
            <p class="rl_nr" id="shop_name"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">手机号码</p>
            <p class="rl_nr" id="acreage"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="yx_dphoto">店铺门脸照片</p>
            <div class="yx_dphoto_img">
                <img src="/static/images/azpm_spacing.png" class="imgspace">
                <img class="img" id="shop_image" src="">
            </div>
        </div>
    </div>
    <!--待审核-->
<!--    <div class="yx_shehe_jg" id="daishen" style="display: none;">-->
<!--        <a href="javascript:;" class="yx_shenghe_hui">待审核</a>-->
<!--    </div>-->
    <!--订单已审核-->
<!--    <div class="yx_shehe_jg" id="yishengshenhe" style="display: none;">-->
<!--        <a href="javascript:;" class="yx_shenghe_cheng">订单已审核</a>-->
<!--    </div>-->
    <!--订单已核对-->
    <div class="yx_shehe_jg" id="yitongguo">
        <a style="background: #fa6a06;bottom: 8%;" href="/shop/choose-shop-type?active_id=<?=$id?>&token=<?=$token?>&dev=<?=$dev?>" class="yx_shenghe_green" >店铺申请</a>
    </div>
    <div class="yx_shehe_jg" id="yitongguo">
        <a style="background: #b5b5b5" href="javascript:void(0);" class="yx_shenghe_green failure_sing">签约失败</a>
    </div>
    <!--激活失败更换屏幕-->
    <div class="yx_shehe_jg" id="update" style="display: none;">
        <a href="/screen/screenover?shopid=<?=$id?>&token=<?=$token?>&istrue=0" class="yx_shenghe_cheng">激活失败更换屏幕</a>
    </div>
    <!--修改安装信息-->
    <div class="yx_shehe_jg" id="weitongguo" style="display: none;">
        <p class="yx_yuanyin" id="wtgyuanying"></p>
        <a href="/screen/screenupdateimg?shopid=<?=$id?>&token=<?=$token?>&dev=<?=$dev?>" class="yx_shenghe_cheng">修改安装信息</a>
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
<!-- 签约失败弹框 -->
<style type="text/css">
    .contract_failure{position: fixed;background: rgba(0,0,0,.6);top: 0; width: 100%; height: 100%; left: 0; right: 0;}
    .contract_con{width: 80%;background: #fff;margin: 0 auto; margin-top: 45%;    border-radius: 10px;padding-top: 14px;    overflow: hidden;}
    .failure_tt{border-left: 4px solid #ff7d09;color: #333;margin-left: 20px;padding-left: 10px;    margin-bottom: 16px;font-size: 16px;}
    .info_sel{padding-left: 20px;margin-top: 20px;margin-bottom: 26px;}
    .sel_yy{margin-bottom: 12px;    width: 88%;    height: 30px;    border: 1px solid #f0f0f0;}
    .srk{width: 93%; height: 68px;border: 1px #f0f0f0 solid;padding-left: 1rem; padding-top: 6px;}
    .confirm_tt{background: #f86808;    border-radius: 0 0 10px 10px;}
    .confirm_btn{display: inline-block;color: #fff;padding-top: 12px; padding-bottom: 12px; width: 49%;text-align: center;   font-size: 16px;}
    .cancel_btn{display: inline-block;color: #ffc9b8;padding-top: 12px; padding-bottom: 12px; width: 49%;text-align: center;   border-radius: 0 0 10px 10px;    font-size: 16px;}
</style>
<div class="contract_failure" style="display: none;">
    <div class="contract_con" >
        <p class="failure_tt" >签约失败</p>
        <p style="height: 1px;background: #f0f0f0;"></p>
        <div class="info_sel" >
            <select class="sel_yy" >
                <option value ="店铺信息有误">店铺信息有误</option>
                <option value ="店铺拒绝安装">店铺拒绝安装</option>
                <option value ="店铺不符合要求">店铺不符合要求</option>
                <option value ="其他">其他</option>
            </select>
            <p style="width: 88%;">
                <textarea class="srk"  placeholder="填写签约失败原因（选填）"></textarea>

            </p>
        </div>

        <p class="confirm_tt" ><span class="confirm_btn" >确定</span><span style="color: #fff;    font-size: 16px;">|</span><span class="cancel_btn" style="">取消</span></p>
    </div>
</div>
<p class="sy-installed-ts">提交失败</p>

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
            url:baseApiUrl+"/shop/active-detail/<?=$id?>",
            type:"get",
            dataType:"json",
            data:{token:'<?=$token?>'},
            success:function(data) {
                //console.log(data);
                if(data.status==200 && data.data!=null){

                    $("#member_name").html(data.data.shop_name);
                    $("#member_mobile").html(data.data.member_mobile);
                    $("#yx_yhphone").html(data.data.apply_mobile);
                    $("#area").html(data.data.area_name);
                    $("#address").html(data.data.address);

                    $("#screen_number").html(data.data.mirror_account);
                    $("#shop_image").attr('src',data.data.shop_image+"?imageView2/0/w/"+uploadimgW);

                    $("#contacts_name").html(data.data.apply_name);
                    $("#contacts_mobile").html(data.data.apply_mobile);
                    $("#shop_name").html(data.data.member_name);
                    $("#acreage").html(data.data.member_mobile);
                    if (data.data.status == 1) {
                        $('.yx_shenghe_green').hide();
                    }
                    $(".sy_loadingpage").hide();
                    $("#infoshow").show();
                }else{
                    conftankuangparam("sy_examin_panel","数据有误,请重试！");
                }
            }
        });
        
        $('.failure_sing').click(function () {
            $('.contract_failure').css('display','block');
        })
        $('.cancel_btn').click(function () {
            $('.contract_failure').css('display','none');
        })
        //签约失败点击确定
        $('.confirm_btn').click(function () {
            var sel = $('.sel_yy').val();
            var qt = $('.srk').val();
            if(sel == '其他' && !qt){
                $('.sy-installed-ts').text('请填写原因');
                tippanel();
                return false;
            }
            $.ajax({
                url:baseApiUrl+"/shop/contract/<?=$id?>",
                type:"POST",
                dataType:"json",
                data:{token:'<?=$token?>',reason:sel+' '+qt},
                success:function (phpdata) {
                    if(phpdata.status == 200){
                        $('.sy-installed-ts').text('提交成功');
                        tippanel();
                        $('.contract_failure').css('display','none');
                    }else{
                        $('.sy-installed-ts').text('提交失败');
                        tippanel();
                    }

                },
                error:function () {
                    $('.sy-installed-ts').text('提交失败');
                    tippanel();
                }
            })
        })
    });
</script>
</body>
</html>
