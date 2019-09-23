<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>已安装店铺</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet" href="/static/css/sy_installteam.css" />
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
    <p class='txt'>暂未有已安装店铺信息</p>
</div>
<!--有数据-->
<div class="sy_waitinstall" style="display: none;">
    <p class="tip">总计:<span id="install_shop_number">已安装店铺6家</span><span id="install_screen_number">已安装屏幕50台</span></p>
    <div class="listbox" style="padding-bottom:0;">
        <ul id="listli">
<!--            <li class="listitem clearfix">-->
<!--                <div class="img">-->
<!--                    <img src="images/pic/tempalte.jpg">-->
<!--                </div>-->
<!--                <div class="sy_waitin_txt">-->
<!--                    <p class="name">动感魔发</p>-->
<!--                    <p class="adress">北京市北京市丰台区北京市北京市丰台区</p>-->
<!--                    <p class="street">新村街道</p>-->
<!--                </div>-->
<!--                <div class="sy_waitin_corpra">-->
<!--                    <p class="screen">5台屏幕</p>-->
<!--                    <p class="date">2018-06-10</p>-->
<!--                    <p class="xingming">张伞</p>-->
<!--                </div>-->
<!--            </li>-->
        </ul>
    </div>
</div>


<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script>
    //获取数据
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    //初始化数据
    $(function(){
        showshop();
    });
    function showshop(){
        $.ajax({
            url:baseApiUrl+"/team/<?=$team_id?>/shop?token=<?=$token?>&shop_install_status=3",
            type:"get",
            dataType:"json",
            success:function(data) {
                //初始化待指派的店铺
                if(data.status==200&&data.data!=null){
                    $("#install_shop_number").html("已安装店铺"+data.data.install_shop_number+"家");
                    $("#install_screen_number").html("已安装屏幕"+data.data.install_screen_number+"台");
                    var waithtml="";
                    $.each(data.data.shop_list,function(i,value){
                            waithtml=waithtml+"<li class='listitem clearfix'> " +
                            "<div class='img'> " +
                            "<img src='"+value.shop_image+"?imageView2/0/w/66'> " +
                            "</div> " +
                            "<div class='sy_waitin_txt'> " +
                            "<p class='name'>"+value.name+"</p> " +
                            "<p class='adress'>"+value.area_name+"</p> " +
                            "<p class='street'>"+value.address+"</p> " +
                            "</div> " +
                            "<div class='sy_waitin_corpra'> ";
                            if(value.status==3){
                                waithtml=waithtml+"<p class='screen'>安装待审核</p>";
                            }else if(value.status==4){
                                waithtml=waithtml+"<p class='screen'>安装待审核</p>";
                            }else{
                                waithtml=waithtml+ "<p class='screen'>"+value.screen_number+"台屏幕</p> ";
                            }
                            waithtml=waithtml+ "<p class='xingming'>"+value.install_member_name+"</p> " + "</div> " + "</li>";
                    });
                    if(waithtml==""){//暂无待指派任务
                        $(".sy_loadingpage").hide();
                        $(".sy_waitinstall").hide();
                        $(".sy_nowaitins_shop").show();
                    }else{
                        $("#listli").html(waithtml);
                        $(".sy_nowaitins_shop").hide();
                        $(".sy_loadingpage").hide();
                        $(".sy_waitinstall").show();
                    }
                }else{
                    <!--暂无待指派任务-->
                    $(".sy_waitinstall").hide();
                    $(".sy_loadingpage").hide();
                    $(".sy_nowaitins_shop").show();
                }
            }
        });
    }
</script>
<script>
    //滚动加载
    $(window).scroll(function(){
        var doc_height = $(document).height();
        var scroll_top = $(document).scrollTop();
        var window_height = $(window).height();
        if(scroll_top + window_height >= doc_height){
        }
    });
</script>
</body>
</html>
