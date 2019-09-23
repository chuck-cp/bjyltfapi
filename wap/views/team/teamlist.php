<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>成员列表</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet" href="/static/css/sy_groupmember.css" />
</head>
<body class="sy-body">
<div class="sy_loadingpage">
    <img src="/static/images/loading.gif">
    <p>正在加载...</p>
</div>
<div class="sy_gpmember" style="display: none;">
    <div class="sy_parmember">
        <h3 class="prison">组长</h3>
        <div class="sy_dplist" id="parmember">
            <div class="sy_gpmember_box">
                <div class="img">
                    <img src="images/pic/tempalte.jpg">
                </div>
                <div class="sy_waitin_txt">
                    <p class="name">小二</p>
                    <p class="number">17465462918</p>
                    <p class="xqq">已安装0家店铺，0台屏幕。</p>
                </div>
                <div class="sy_waitin_corpra">
                    <p class="screen">待安装</p>
                    <p class="date">0家</p>
                </div>
            </div> <!---->
        </div>
    </div>
    <div class="sy_childmember" id="sy_childmember_no" style="display: none;">
        <p class='sy_memberblank'>暂无组员加入该安装小组</p>
    </div>
    <div class="sy_childmember" id="sy_childmember_yes" style="display: none;">
        <h3 class="prison">组员</h3>
        <div class="sy_dplist" id="childmember">
<!--            <div class="sy_gpmember_box">-->
<!--                <div class="img">-->
<!--                    <img src="images/pic/tempalte.jpg">-->
<!--                </div>-->
<!--                <div class="sy_waitin_txt">-->
<!--                    <p class="name">小二</p>-->
<!--                    <p class="number">17465462918</p>-->
<!--                    <p class="xqq">已安装0家店铺，0台屏幕。</p>-->
<!--                </div>-->
<!--                <div class="sy_waitin_corpra">-->
<!--                    <p class="screen">待安装</p>-->
<!--                    <p class="date">0家</p>-->
<!--                </div>-->
<!--            </div> <!---->

        </div>
    </div>
</div>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script type="text/javascript" src="/static/js/sy_installteampub.js" ></script>
<script>
    //获取数据
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    //初始化数据
    $(function(){
        $.ajax({
            url:baseApiUrl+"/team/teamlsit/<?=$team_id?>?token=<?=$token?>",
            type:"get",
            dataType:"json",
            success:function(data) {
                //初始化待指派的店铺
                if(data.status==200&&data.data!=null){
                    var parmemberhtml="";
                    var childmemberhtml="";
                    $.each(data.data,function(i,value){
                        var temporaryhtml=" <div class='sy_gpmember_box'> " +
                            "<div class='img'> " +
                            "<img src='"+value.avatar+"?imageView2/0/w/66'> " +
                            "</div> " +
                            "<div class='sy_waitin_txt'> " +
                            "<p class='name'>"+value.member_name+"</p> " +
                            "<p class='number'>"+value.member_mobile+"</p> " +
                            "<p class='xqq'>已安装"+value.install_shop_number+"家店铺，"+value.install_screen_number+"台屏幕。</p> " +
                            "</div> " +
                            "<div class='sy_waitin_corpra'> " +
                            "<p class='screen'>待安装</p> " +
                            "<p class='date'>"+value.wait_shop_number+"家</p> " +
                            "</div> " +
                            "</div>";
                        if(value.member_type == 2){
                            parmemberhtml=parmemberhtml+temporaryhtml;
                        }
                        if(value.member_type == 1){
                            childmemberhtml=childmemberhtml+temporaryhtml;
                        }
                    });
                    $("#parmember").html(parmemberhtml);
                    if(childmemberhtml==""){
                        $("#sy_childmember_no").show();
                    }else{
                        $("#childmember").html(childmemberhtml);
                        $("#sy_childmember_yes").show();
                    }
                    $(".sy_loadingpage").hide();
                    $(".sy_gpmember").show();
                }
            }
        });
    });
</script>
</body>
</html>
