<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>已指派</title>
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
        <img src='/static/images/noalreadyassign.png'>
    </p>
    <p class='txt'>暂无已指派安装任务</p>
</div>
<div class="sy_waitinstall" style="display: none;">
    <p class="tip">总计:<span id="wait_num"></span></p><!--待指派3家-->
    <!--    <span id="already_num">已指派3家</span>-->
    <div class="listbox">
        <ul id="listli">
            <!--            <li class="listitem clearfix">-->
            <!--                <div class="img">-->
            <!--                    <img src="/static/images/pic/tempalte.jpg">-->
            <!--                </div>-->
            <!--                <div class="sy_waitin_txt">-->
            <!--                    <p class="name">动感魔发</p>-->
            <!--                    <p class="adress">北京市北京市丰台区北京市北京市丰台区</p>-->
            <!--                    <p class="street">新村街道</p>-->
            <!--                </div>-->
            <!--                <div class="sy_waitin_corpra">-->
            <!--                    <!--待指派选用tba样式名    已指派选用assigned-->
            <!--                    <p class="assigned">已指派</p>-->
            <!--                    <p class="number">张三</p>-->
            <!--                    <p class="identifited">-->
            <!-- 	  	 	 	  	   	   <span class="unselected">-->
            <!-- 	  	 	 	  	   	       <input type="checkbox" name="yizhipai" value="a">-->
            <!-- 	  	 	 	  	   	   </span>-->
            <!--                    </p>-->
            <!--                </div>-->
            <!--            </li>-->

        </ul>
    </div>
    <button class="sy_waitin_btn" type="button">取消指派</button>
</div>
<!--mask遮罩层-->
<div class="mask"></div>
<!--指派成员弹框-->
<!--请确认安装成员是否处于安装过程中是否取消指派-->
<div class="sy_waitins_doing">
    <div class="img">
        <img src="/static/images/sy_waitinstall_panelbg.png">
        <p>请确认安装成员是否处于安装过程中,是否取消指派?</p>
    </div>
    <div class="btn clearfix">
        <a href="javascript:;" class="qr">确认</a>
        <a href="javascript:;" class="qx">取消</a>
    </div>
</div>
<!--不可同时选择两种状态弹框-->
<div class="sy_waitins_choicetwo">
    <div class="img">
        <p>不可同时选择两种状态</p>
        <img class="bgimg" src="/static/images/sy_waitinstall_panelbgtwo.png">
        <span class="sy_waitins_close"><img src="/static/images/sy_waitinstall_close.png"></span>
    </div>
</div>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script type="text/javascript" src="/static/js/sy_installteampub.js" ></script>
<script>
    //获取数据
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    //初始化数据
    $(function(){
        showshop();
    });
    function showshop(){
        $.ajax({
            url:baseApiUrl+"/team/<?=$team_id?>/shop?token=<?=$token?>&shop_install_status=2",
            type:"get",
            dataType:"json",
            success:function(data) {
                //初始化待指派的店铺
                if(data.status==200&&data.data!=null){
                    $("#wait_num").html("已指派"+data.data.not_install_shop_number+"家");
                    var waithtml="";
                    $.each(data.data.shop_list,function(i,value){
                        console.log(value);
                        if(value.install_member_id != '0'){
                            waithtml=waithtml+"<li class='listitem clearfix'><a href='/team/waitinstallinfo?token=<?=$token?>&shopid="+value.id+"'><div class='img'><img src='"+value.shop_image+"?imageView2/0/w/66'></div><div class='sy_waitin_txt'><p class='name'>"+value.name+"</p><p class='adress'>"+value.area_name+"</p><p class='street'>"+value.address+"</p></div></a><div class='sy_waitin_corpra'><p class='tba'>已指派</p><p class='number'>"+value.install_member_name+"</p><p class='identifited'><span class='unselected'><input type='checkbox' value='"+value.id+"'></span></p></div></li>";
                        }
                    });
                    if(waithtml==""){
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
                    $(".sy_waitinstall").hide();
                    $(".sy_loadingpage").hide();
                    $(".sy_nowaitins_shop").show();
                }
            }
        });
    }
    $(".sy_waitin_btn").click(function (){
        var shop_id="";
        $('#listli li').find('input').each(function(){
            if($(this).prop('checked')){
                shop_id=shop_id+$(this).val()+",";
            }
        });
        shop_id=shop_id.substr(0,shop_id.length-1);
        if(shop_id==""){
            closetankuan('sy_waitins_tabpanel');
            tankuangparam('sy_waitins_choicetwo','请选择取消指派的店铺');
            return false;
        }
        tankuang('sy_waitins_doing');
    });
    $(".qr").click(function (){
        var shop_id="";
        $('#listli li').find('input').each(function(){
            if($(this).prop('checked')){
                shop_id=shop_id+$(this).val()+",";
            }
        });
        shop_id=shop_id.substr(0,shop_id.length-1);
        if(shop_id==""){
            closetankuan('sy_waitins_tabpanel');
            tankuangparam('sy_waitins_choicetwo','请选择取消指派的店铺');
            return false;
        }
        $.ajax({
            url:baseApiUrl+"/team/<?=$team_id?>/shop/"+shop_id,
            type:"post",
            dataType:"json",
            data:{_method:"PUT",token:'<?=$token?>'},
            success:function(data) {
                console.log();
                console.log(data);
                if(data.status==200){
                    showshop();
                    closetankuan('sy_waitins_doing');
                    tankuangparam('sy_waitins_choicetwo','取消指派任务成功');
                }else{
                    closetankuan('sy_waitins_doing');
                    tankuangparam('sy_waitins_choicetwo',data.message);
                }
            }
        });
    });

</script>
</body>
</html>
