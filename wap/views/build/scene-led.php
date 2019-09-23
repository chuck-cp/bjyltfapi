<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>楼宇安装位置场景(led)</title>
    <link rel="stylesheet" type="text/css" href="/static/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/static/css/logo_install.css">
    <link rel="stylesheet" type="text/css" href="/static/css/swiper.min.css">
    <link rel="stylesheet" type="text/css" href="/static/css/apply.css">

    <style type="text/css">
        .building_installation li{background: #FFFFFF;margin-bottom: 1px;padding-top: 16px;padding-bottom: 16px;padding-left: 12px; padding-right: 12px;overflow: hidden;}
        .building_installation em{font-size:14px;float: left;}
        .building_installation span{float: right;font-size: 12px;color: #999999;}
        .building_add{width: 18px;}
        .building_installation .total_tt,.building_installation .type_tt{font-size: 12px;color: #323232;}
        .show_tt,.show_total,.show_type{overflow: hidden;}
        .total_tt,.total_ttt{margin-top: 20px;margin-bottom: 16px;}
        .shop_info span{width: auto;}
        /* 确认按钮样式 */
        .ok_btn{position: fixed;bottom: 0;width: 100%; height: 42px; display: block; color: #FFFFFF;  border: 0; background: -webkit-linear-gradient(left, #ff7d09 , #ef4f08); background: -o-linear-gradient(right, #ff7d09, #ef4f08); background: -moz-linear-gradient(right, #ff7d09, #ef4f08); background: linear-gradient(to right, #ff7d09 , #ef4f08);font-size: 16px;}
    </style>
</head>
<body>
<ul class="shop_info building_installation">
    <!-- 此模块显示总安装台数和设备类型 S -->
<!--    <li>-->
<!--        <div class="show_tt">-->
<!--            <em>大堂等候区</em><span><a href=""><img class="building_add" src="/static/image/revise_btn.png" ></a></span>-->
<!--        </div>-->
<!--        <div class="show_total">-->
<!--            <em class="total_tt">安装总数量</em><span class="total_ttt">30台</span>-->
<!--        </div>-->
<!--        <div class="show_type">-->
<!--            <em class="type_tt">设备类型</em><span>海报15.6</span>-->
<!--        </div>-->
<!--    </li>-->
    <!-- 此模块显示总安装台数和设备类型 E -->
    <!-- 添加设备 -->
<!--    <li>-->
<!--        <em>地下车库客梯等候区</em><span><a href=""><img class="building_add" src="/static/image/sy_sqfdadd.png" ></a></span>-->
<!--    </li>-->
    <!-- 添加设备 -->
</ul>
<br>
<br>
<br>
<br>
<br>
<br>
<button type="button" class="ok_btn">确认提交</button>
<p class="sy-installed-ts">提交失败</p>
<script type="text/javascript">
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    var token= "<?=$token?>";
    var build_id = "<?=$build_id?>";
</script>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/tip/tip.js" ></script>
<script type="text/javascript">
    $.ajax({
        url: baseApiUrl + "/build-wap/build-bill-scenes?token="+token+'&build_id='+build_id+'&screen_type=1',
        type: "GET",
        async: true,
        success:function (phpdata) {
            var data_length = phpdata.data.length;
            if(data_length > 0){
                var addhtml = '';
                for(var i=0; i<data_length; i++){
                    if(phpdata.data[i].screen_number){
                        addhtml += '<li>';
                        addhtml += '<div class="show_tt">';
                        addhtml += '<em>大堂等候区</em><span><a href="/build/detail-scene?token=<?=$token?>&screen_type=<?=$screen_type?>&shop_type=<?=$shop_type?>&id='+phpdata.data[i].id+'&shop_id='+build_id+'"><img class="building_add" src="/static/image/revise_btn.png" ></a></span>';
                        addhtml += '</div>';
                        addhtml += '<div class="show_total">';
                        addhtml += '<em class="total_tt">安装总数量</em><span class="total_ttt">'+phpdata.data[i].screen_number+'台</span>';
                        addhtml += '</div>';
                        addhtml += '<div class="show_type">';
                        addhtml += '<em class="type_tt">设备类型</em><span>海报'+phpdata.data[i].spec+'</span>';
                        addhtml += '</div>';
                        addhtml += '</li>';
                    }else{
                        addhtml += '<li>';
                        addhtml += '<em>'+phpdata.data[i].position_name+'</em>';
                        addhtml += '<span>';
                        addhtml += '<a href="/build/detail-scene?token=<?=$token?>&screen_type=<?=$screen_type?>&shop_type=<?=$shop_type?>&id='+phpdata.data[i].id+'&shop_id='+build_id+'"><img class="building_add" src="/static/image/sy_sqfdadd.png" ></a>';
                        addhtml += '</span>';
                        addhtml += '</li>';
                    }
                }
            }
            $('.building_installation').append(addhtml);
        },
        error:function (phpdata) {

        }
    });
    $('.ok_btn').on('click',function () {
        //1首先验证页面上是否至少有一台设备(LED或者POSTER)
        var deviceArr = getAttr($('.total_ttt'), '', [], false, 'html');
        console.log(deviceArr.length)
        if(deviceArr.length < 1){
            $('.sy-installed-ts').text('请至少提交一种安装位置的设备数量');
            tippanel();
            return false;
        }
        console.log(deviceArr);
        //return;
        //2提交
        $.ajax({
            url:baseApiUrl + '/build-wap/build-device-post?token='+token,
            data:{'id':build_id, 'screen_type':'<?=$screen_type?>', 'shop_type':'<?=$shop_type?>'},
            type:"POST",
            async:true,
            success:function (phpdata) {
                if(phpdata.status == 200){
                    $('.sy-installed-ts').text('申请安装信息提交成功，等待审核中');
                    tippanel('/build?token='+token);
                }else{
                    $('.sy-installed-ts').text(phpdata.message);
                    tippanel();
                    return false;
                }
            },error:function () {
                $('.sy-installed-ts').text('提交失败请重试');
                tippanel();
                return false;
            }
        });
    })
</script>
</body>
</html>
