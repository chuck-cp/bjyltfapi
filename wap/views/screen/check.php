<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>设备安装反馈</title>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet" href="/static/css/lsy-install.css?v=1.4" />
    <link rel="stylesheet" href="/static/css/swiper.min.css" />
</head>
<body class="sy-body">
<div class="sy-wrapper">
    <div class="sy-feedback">
        <p class="title">请核对订单信息</p>
        <div class="content">
            <!--详细信息-->
            <div class="sy-part-one">
                <table>
                    <tr>
                        <td><p>订单信息</p></td>
                        <td><p id="apply_code"></p></td>
                    </tr>
                    <tr>
                        <td><p>申请人</p></td>
                        <td><p id="apply_name"></p></td>
                    </tr>
                    <tr>
                        <td><p>手机号码</p></td>
                        <td><p id="apply_mobile"></p></td>
                    </tr>
                    <tr>
                        <td><p>安装地址</p></td>
                        <td><p id="area"></p></td>
                    </tr>
                    <tr>
                        <td><p>店铺名称</p></td>
                        <td><p id="shop_name"></p></td>
                    </tr>
                    <tr>
                        <td><p>公司名称</p></td>
                        <td><p id="company_name"></p></td>
                    </tr>
                    <tr>
                        <td><p>店铺面积</p></td>
                        <td><p id="acreage"></p></td>
                    </tr>
                    <tr>
                        <td><p>安装数量</p></td>
                        <td><p id="screen_number"></p></td>
                    </tr>
                    <tr>
                        <td><p>镜面数量</p></td>
                        <td><p id="mirror_account"></p></td>
                    </tr>
                </table>
            </div>
            <!--门脸照片-->
            <div class="sy-part-two">
                <p class="pt-title">店铺门脸照片</p>
                <div class="content">
                    <div class="imgtu">
                        <img class="imgspace"  src="/static/image/blank.jpg">
                        <img id="shop_image" src="">
                    </div>
                </div>
            </div>
            <!--安装位置照片-->
            <div class="sy-part-three sy-shoppoto">
                <p class="pt-title">室内全景照片</p>
                <div class="content">
                    <div class="swiper-container">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <img id="panorama_image" src="">
                            </div>
                        </div>
                    </div><!--swiper-container-->
                    <!--遮罩层-->
                    <!--<div class="mask"></div>-->
                </div>
            </div>
            <p class="sy_wtgyy" id="wtgyuanying">
                <cite>未通过原因:</cite>
                <span></span>
            </p>
        </div>

        <span class="sy-feedback-btn" id="insert" >订单已核对</span>
        <span class="sy-feedback-btn_hui" id="daishen" style="display: none;">待审核</span>
        <span class="sy-feedback-btn" id="gengping" style="display: none;">激活失败更换屏幕</span>
        <span class="sy-feedback-btn" id="update" style="display: none;" >修改安装信息</span>
    </div>
</div>
<p class="sy-installed-ts">提交成功</p>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script type="text/javascript" src="/static/js/swiper.min.js" ></script>
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
        var dev = "<?=$dev?>";
        var token = "<?=$token?>";
        $.ajax({
            url:baseApiUrl+"/screeninstall",
            type:"get",
            dataType:"json",
            data:{id:<?=$id?>,apply_code:'<?=$apply_code?>',dynamic_code:'<?=$dynamic_code?>',token:token},
            success:function(data) {
                console.log(data);
                if(data.status==200&&data.data!=null){
                    //如果订单安装反馈待审核
                    if(data.data.status==3){
                        $.ajax({
                            url:baseApiUrl+"/screeninstall/activation",
                            type:"get",
                            dataType:"json",
                            data:{id:<?=$id?>,apply_code:'<?=$apply_code?>',dynamic_code:'<?=$dynamic_code?>'},
                            success:function(data) {
                                if(data.status==200){
                                    $('#insert').hide();
                                    $('#daishen').show();
                                }else{
                                    $('#insert').hide();
                                    $('#gengping').show();
                                }
                            }
                        });
                    }
                    //如果订单安装反馈审核失败
                    if(data.data.status==4){
                        $('#wtgyuanying span').html(data.data.examine_desc);
                        $('#wtgyuanying').show();
                        $('#insert').hide();
                        $('#update').show();
                    }
                    $("#apply_code").html(data.data.apply_code);
                    $("#apply_name").html(data.data.apply_name);
                    $("#apply_mobile").html(data.data.apply_mobile);
                    $("#area").html(data.data.area_name+data.data.address);
                    $("#shop_name").html(data.data.name);
                    $("#company_name").html(data.data.company_name);
                    $("#acreage").html(data.data.acreage);
                    $("#screen_number").html(data.data.screen_number);
                    $("#mirror_account").html(data.data.mirror_account);
                    $("#shop_image").attr('src',data.data.shop_image+"?imageView2/0/w/"+uploadimgW);
                    $("#panorama_image").attr('src',data.data.panorama_image+"?imageView2/0/w/"+uploadimgW);
                }else{
                    $('.sy-installed-ts').text('数据不存在');
                    tippanel();
                    setTimeout(function() {  location.href="/screen/login" },1000);
                }
            }
        });
        $("#insert").click(function (){
            location.href="/screen/confirm?number=<?=$id?>&apply_code=<?=$apply_code?>&dynamic_code=<?=$dynamic_code?>&dev=<?=$dev?>&token=<?=$token?>";
        })
        $("#update").click(function (){
            location.href="/screen/updateimg?number=<?=$id?>&token=<?=$token?>&dev=<?=$dev?>&apply_code=<?=$apply_code?>&dynamic_code=<?=$dynamic_code?>";
        })

        $("#gengping").click(function (){
            location.href="/screen/over?number=<?=$id?>&apply_code=<?=$apply_code?>&dynamic_code=<?=$dynamic_code?>&istrue=0&token=<?=$token?>";
        })


    });
</script>
</body>
</html>
