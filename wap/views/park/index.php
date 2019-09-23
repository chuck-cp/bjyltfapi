<?php
use yii\helpers\Url;
?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>公园申请</title>
    <link rel="stylesheet" type="text/css" href="/static/css/reset.css">
    <link rel="stylesheet" type="text/css" href="/static/css/logo_install.css">
    <link rel="stylesheet" type="text/css" href="/static/css/swiper.min.css">
    <link rel="stylesheet" type="text/css" href="/static/css/apply.css">
    <style type="text/css">
        h3{font-size: 16px;padding: 14px 4%;border-bottom: 1px solid #F0F0F0;}
        .park_apple{margin-top: 4%;}
        .park_apple li{background: #FFFFFF;margin-bottom: 14px;box-shadow: 0px 2px 6px #d8d8d8;}
        .biz_box{text-align: center;padding-bottom: 14px;}
        .park_apple img{width: 93%;margin-top: 14px;}
        .left_label{border-left: 4px solid #ff7d09;font-size: 12px;}
        /* 遮罩 */
        .mask_box{position: fixed;left: 0;top: 0;bottom: 0;right: 0;background: rgba(0,0,0,.3);display: none;}
        /* 设备选择 */
        .equipment_box{position: fixed;bottom: -240px;left: 0;right: 0;background: #FFFFFF;display: none; z-index: 999;}
        .equipment_list{height: 192px;overflow-y: auto;}
        .equipment_list li{padding: 14px 2%;font-size: 14px;border-bottom: 1px #F0F0F0 solid;}
        .btn_box{text-align: center;}
        .cancel_btn{height: 42px;width: 100%;background: -webkit-linear-gradient(left, #ff7d09 , #ef4f08); background: -o-linear-gradient(right, #ff7d09, #ef4f08); background: -moz-linear-gradient(right, #ff7d09, #ef4f08); background: linear-gradient(to right, #ff7d09 , #ef4f08);}
        .equipment_list::-webkit-scrollbar{display: none;}
    </style>
</head>
<body>
<ul class="park_apple" id="list_box">
    <li>
        <h3><span class="left_label">&nbsp;</span>签约信息</h3>
        <div class="biz_box" >
            <a href="<?=Url::to(['/build/company-create','token'=>$token])?>">
                <img src="/static/image/more_apply/subscription_information.png" >
            </a>
            <a href="<?=Url::to(['/park/park-create','token'=>$token])?>">
                <img src="/static/image/more_apply/device_info.png" >
            </a>
        </div>

    </li>
    <li>
        <h3><span class="left_label">&nbsp;</span>设备申请</h3>
        <div class="biz_box" >
            <div class="equipment_type" id="poster_equipment">
                <img src="/static/image/more_apply/poster_equipment.png" >
            </div>
        </div>

    </li>
</ul>
<!-- 遮罩 -->
<div class="mask_box"></div>
<!-- 生成公司地址 -->
<div class="equipment_box">
    <ul class="equipment_list"></ul>
    <div class="btn_box">
        <button class="cancel_btn" type="button">取消</button>
    </div>
</div>
<script type="text/javascript">
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    var token= "<?=$token?>";
</script>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/tip/tip.js"></script>
<script src="/static/js/tab_change.js" type="text/javascript" charset="utf-8"></script>
</body>
</html>
<script type="text/javascript">
    var sel_inc1; //公司地址
    var sel_inc2; //公司地址
    var id; //config_id top
    $('.biz_box .equipment_type').on('click',function () {

        var __this = $(this).index();
        $.ajax({
            url: baseApiUrl + "/park-wap/choose-parks?token="+token,
            type: "POST",
            async: false,
            success:function (phpdata) {
                if(phpdata){
                    if(__this == 0){
                        sel_inc1 = phpdata.data.list;

                    }else{
                        sel_inc2 =phpdata.data.list;
                    }
                    id = phpdata.data.config_id;
                }else{
                    $('.sy-installed-ts').text('没有可选择的公园！');
                    tippanel();
                }

            },
            error:function (phpdata) {
                $('.sy-installed-ts').text('请求错误，请重试！');
                tippanel();
            }
        });
    })

    var sel_def = '';

    function list_info(a,b) {
        //遍历数据
        sel_def = '';
        $.each(a, function(index, value) {
            if(b == 1){
                sel_def += "<a href='/park/scene-post?token="+token+"&park_id="+index+"&id="+id+"&screen_type=2&shop_type=2'><li>" + value + "</li></a>";
            }else if(b ==2){
                sel_def += "<a href='/park/scene-led?token="+token+"&park_id="+index+"&id="+id+"&screen_type=1&shop_type=2'><li>" + value + "</li></a>";
            }
        });
    }
    function screen_type(e) {
        //筛选数据
        switch (e) {
            case 'poster_equipment':
                list_info(sel_inc1,1);
                break;
            case 'led_equipment':
                list_info(sel_inc2,2);
                break;
        }
    }
    $('#list_box .equipment_type').click(function () {
        var self_me = $(this).attr('id');
            screen_type(self_me); //筛选数据
        $('.equipment_list').html(sel_def);
    })
</script>
