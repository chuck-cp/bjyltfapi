<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <?if($status == 1):?>
        <title>核对订单信息</title>
    <?else:?>
        <title>屏幕更换</title>
    <?endif;?>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/wait_examine.css"/>
	<style>
		.yx_shenghenr{padding-left: 0;}
		.yx_dphoto{width: 50%;}
		.yx_dphoto_img{width: 70px;height: 70px;margin:0;float: right;border-radius:0}
	</style>
</head>
<body class="yx_body">
<div class="sy_loadingpage">
    <img src="/static/images/loading.gif">
    <p>正在加载...</p>
</div>
<div id="infoshow" style="display: none;">
    <div class="yx_shenghenr">
	  <div style="padding-left: 10px;">
          <div class="yx_sh_lb">
              <p class="lf_mc">店铺名称</p>
              <p class="rl_nr" id="shop_name"></p>
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
              <p class="lf_mc">店铺联系人</p>
              <p class="rl_nr" id="contacts_name"></p>
          </div>
          <div class="yx_sh_lb">
              <p class="lf_mc">手机号码</p>
              <p class="rl_nr" id="contacts_mobile"></p>
          </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">业务合作人</p>
            <p class="rl_nr" id="member_name"></p>
        </div>
        <div class="yx_sh_lb">
            <p class="lf_mc">联系电话</p>
            <p class="rl_nr" id="member_mobile"></p>
        </div>

	  </div>
	  <div style="padding-left: 10px;" id="dv">
          <div class="yx_sh_lb" style="border-bottom:0">
              <p class="lf_mc">问题描述</p>
              <p class="rl_nr" id="problem" style="clear: both;text-align: left;width: 100%;  margin-top: 10px;    line-height: normal;  white-space: normal;">

              </p>
          </div>


	  </div>
    </div>

    <!--待审核-->
    <div class="yx_shehe_jg" id="daishen" style="display: none;">
        <a href="javascript:;" class="yx_shenghe_hui">待审核</a>
    </div>
<!--    换屏完成-->
    <div class="yx_shehe_jg" id="yiwancheng" style="display: none;">
        <a href="javascript:;" class="yx_shenghe_hui">更换完成</a>
    </div>
    <!--订单已审核-->
    <div class="yx_shehe_jg" id="yishengshenhe" style="display: none;">
        <a href="javascript:;" class="yx_shenghe_cheng">订单已审核</a>
    </div>
    <!--订单已核对-->
    <div class="yx_shehe_jg" id="yitongguo">
        <a href="/screen/change-view?shopid=<?=$shopid?>&token=<?=$token?>&dev=<?=$dev?>&change=<?=$change?>&replace_id=<?=$replace_id?>" class="yx_shenghe_green">订单已核对</a>
    </div>
    <div class="yx_shehe_jg" id="yiwanc" style="display: none;">
        <a href="javascript:void(0);" class="yx_shenghe_hui">订单已核对</a>
    </div>
    <!--激活失败更换屏幕-->
    <div class="yx_shehe_jg" id="update" style="display: none;">
        <a href="/screen/screenover?shopid=<?=$shopid?>&token=<?=$token?>&istrue=0&change=<?=$change?>&replace_id=<?=$replace_id?>" class="yx_shenghe_cheng">激活失败更换屏幕</a>
    </div>
    <!--修改安装信息-->
    <div class="yx_shehe_jg" id="weitongguo" style="display: none;">
        <p class="yx_yuanyin" id="wtgyuanying"></p>
<!--        <a href="/screen/screenupdateimg?shopid=--><?//=$shopid?><!--&token=--><?//=$token?><!--&dev=--><?//=$dev?><!--" class="yx_shenghe_cheng">修改安装信息</a>-->
        <?if($operate == 2):?>
            <a href="/screen/change-view-new-update?shopid=<?=$shopid?>&token=<?=$token?>&dev=<?=$dev?>&replace_id=<?=$replace_id?>" class="yx_shenghe_cheng">修改安装信息</a>
        <?else:?>
        <a href="/screen/change-update?shopid=<?=$shopid?>&token=<?=$token?>&dev=<?=$dev?>&replace_id=<?=$replace_id?>" class="yx_shenghe_cheng">修改安装信息</a>
        <?endif;?>
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
            url:baseApiUrl+"/screeninstall/change",
            type:"get",
            dataType:"json",
            data:{id:<?=$shopid?>,token:'<?=$token?>','replace_id':<?=$replace_id?>},
            success:function(data) {
                if(data.status==200 && data.data!=null){
                    //待更换
                    if(data.data.replace.status == 1){
                        $('#yitongguo').show();
                        $('#daishen').hide();
                    }
                    //更换未通过
                    if(data.data.replace.status == 3){
                        $('#wtgyuanying').html("未通过原因："+data.data.replace.examine_desc);
                        $('#yitongguo').hide();
                        $('#weitongguo').show();
                    }
                    //待审核
                    if(data.data.replace.status == 2){
                        if(data.data.replace.screen_status == 1){
                            $('#yiwancheng').hide();
                            $('#yitongguo').hide();
                            $('#daishen').show();
                        }else{
                            $('#update').show();
                            $('#yitongguo').hide();
                            $('#daishen').hide();
                        }
                    }
                    //换屏完成
                    if(data.data.replace.status == 4){
                        $('#yiwanc').show();
                        $('#yitongguo').hide();
                        $('#daishen').hide();
                    }
                    $("#apply_code").html(data.data.apply_code);
                    $("#apply_name").html(data.data.apply_name);
                    $("#member_name").html(data.data.member_name);
                    $("#member_mobile").html(data.data.member_mobile);
                    $("#yx_yhphone").html(data.data.apply_mobile);
                    $("#area").html(data.data.area_name);
                    $("#address").html(data.data.address);
                    $("#shop_name").html(data.data.name);
                    $("#contacts_name").html(data.data.contacts_name);
                    $("#contacts_mobile").html(data.data.contacts_mobile);
                    $("#member_name").html(data.data.member_name);
                    $("#member_mobile").html(data.data.member_mobile);
                    $(".sy_loadingpage").hide();
                    $("#infoshow").show();
                    //新旧设备编号
                    var old = data.data.replace.remove_device_number.split(',');
                    var oldHtml = '';
                    for(var i=0; i<old.length; i++){
                        if(i == 0){
                            oldHtml += '<div class="yx_sh_lb"><p class="lf_mc">更换旧设备编号</p><p class="rl_nr old_dv">'+old[i]+'</p></div>';
                        }else{
                            oldHtml += '<div class="yx_sh_lb"><p class="lf_mc">&nbsp;</p><p class="rl_nr old_dv">'+old[i]+'</p></div>';
                        }
                    }
                    var newdv = data.data.replace.install_software_number.split(',');
                    var newHtml = '';
                    for(var i=0; i<newdv.length; i++){
                        if(i == 0){
                            newHtml += '<div class="yx_sh_lb"><p class="lf_mc">更换新设备编号</p><p class="rl_nr new_dv">'+newdv[i]+'</p></div>';
                        }else{
                            newHtml += '<div class="yx_sh_lb"><p class="lf_mc">&nbsp;</p><p class="rl_nr new_dv">'+newdv[i]+'</p></div>';
                        }
                    }
                    $('#dv').prepend(oldHtml+newHtml);
                    $('#problem').html(data.data.replace.problem_description);
                }else{
                    conftankuangparam("sy_examin_panel","数据有误,请重试！");
                }
            }
        });
    });
</script>
</body>
</html>
