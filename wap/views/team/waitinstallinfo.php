<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>待安装店铺</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet" href="/static/css/sy_installteam.css" />
</head>
<body class="sy-body">
<div class="sy_loadingpage">
    <img src="/static/images/loading.gif">
    <p>正在加载...</p>
</div>
<div class="sy_waitins_xq" style="display: none;">
    <!--店铺照片-->
    <div class="sy_waitins_xqdianpu">
        <!--spacing为空间占位符-->
        <img class="spacing" src="/static/images/sy_waitins_dianpu.jpg">
        <!--店铺真实照片-->
        <img class="reaimg" src="">
        <!--审核状态-->
        <div class="status"><p>已审核</p></div>
        <p class="name"><span id="shop_nameB">东时尚丰台店</span></p>
    </div>
    <!--店铺信息-->
    <div class="sy_waitins_xqdpinfor clearfix">
        <p class="tit">店铺信息</p>
        <div class="sy_waitins_xqdpcon">
            <p class="list"><span class="lf">店铺名称：</span><span class="rh" id="shop_name"></span></p>
            <p class="list">
                <span class="lf">店铺位置：</span>
                <span class="rh" id="area_name"></span>
            </p>
            <p class="list">
                <span class="lf">安装屏幕数量：</span>
                <span class="rh" id="screen_number"></span>
            </p>
            <p class="list">
                <span class="lf">店铺联系人：</span>
                <span class="rh" id="member_name"></span>
            </p>
            <p class="list">
                <span class="lf">联系方式：   </span>
                <span class="rh" id="mobile"></span>
            </p>
        </div>
    </div>
    <!--联系人-->
    <div class="sy_waitins_xqdpinfor clearfix">
        <p class="tit">联系人/业务合作人信息</p>
        <div class="sy_waitins_xqdpcon">
            <p class="list"><span class="lf">姓名：</span><span class="rh" id="ywlxr"></span></p>
            <p class="list">
                <span class="lf">联系方式：</span>
                <span class="rh" id="ywlxfs"></span>
            </p>
        </div>
    </div>
    <!--店铺照片-->
    <div class="sy_waitins_xqphoto">
        <p class="tit">店铺照片</p>
        <div class="img">
            <ul id="imglist">
                <li>
                    <!--spacing为图品占位符-->
                    <img class="spacing" src="/static/images/sy_waitins_dianpupic.jpg">
                    <!--realimg为真实数据-->
                    <img class="realimg" src="">
                </li>
            </ul>
        </div>
    </div>

</div>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script>
    //获取数据
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    //初始化数据
    $(function(){
        $.ajax({
            url:baseApiUrl+"/shop/<?=$shopid?>?token=<?=$token?>&shop_type=2",
            type:"get",
            dataType:"json",
            success:function(data) {
              //  console.log(data);
                if(data.status==200&&data.data!=null){
                    var zhuW=$('.sy_waitins_xqdianpu').find('.spacing').width();
                    var dibuW=$('.imgtu').find('.imgspace').width();
                   $('#shop_name').html(data.data.shop.name);
                   $('#shop_nameB').html(data.data.shop.name);
                   $('#area_name').html(data.data.shop.area_name+"<br>"+data.data.shop.address);
                   $('#screen_number').html(data.data.shop.screen_number+"台");
                   $('#member_name').html(data.data.shop.apply_name);
                   $('#mobile').html(data.data.shop.apply_mobile);
                   //业务合作人
                    $('#ywlxr').html(data.data.shop.member_name);
                    $('#ywlxfs').html(data.data.shop.mobile);
                    var status="";
                    switch(data.data.shop.status)
                    {
                        case "2":
                            status="待安装";
                            break;
                        case "4":
                            status="未通过";
                            break;
                    }
                    $('.status').html("<p>"+status+"</p>");
                    var html="";
                   $.each(data.data.shop_images,function(i,value){
                       html=html+" <li> " +
                       "<img class='spacing' src='/static/images/sy_waitins_dianpupic.jpg'> " +
                       "<img class='realimg' src='"+value.image_url+"?imageView2/0/w/"+dibuW+"'> " +
                       "</li>";
                       if(i==0){
                           $('.reaimg').attr("src", value.image_url+"?imageView2/0/w/"+zhuW);
                       }

                    });
                   $('#imglist').html(html);
                   $(".sy_loadingpage").hide();
                   $(".sy_waitins_xq").show();
                }
            }
        });
    });
</script>
</body>
</html>
