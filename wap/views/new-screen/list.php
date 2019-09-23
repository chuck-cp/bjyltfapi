<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>设备安装</title>
    <link rel="stylesheet" type="text/css" href="/static/css/reset.css"/>
    <style type="text/css">
        body{background: #f0f0f0;}
        .shop_ul li{background: #fff; width: 90%; margin: 0 auto; padding: 12px 2%; border-radius: 6px; margin-top: 14px;box-shadow: 1px 4px 8px #D8D8D8;}
        .shop_ico{width: 5%; margin-right: 2%;}
        .shop_tt{display: inline-block; max-width: 42%; overflow: hidden; white-space: nowrap; text-overflow: ellipsis; font-size: .92rem;      margin-right: 2px;  vertical-align: text-top;}
        .biz_status{color: #ff7d09; background: #ffecdc; border-radius: 20px; padding: 2px 6px; font-size: 12px; display: inline-block;vertical-align: top;}
        .biz_time{color: #999999;float: right;}
        .shop_tt_box{overflow: hidden;}
        .shop_con_box{overflow: hidden; position: relative; border-top: 1px #E5E5E5 solid; margin-top: 12px; padding-top: 12px;}
        .shop_con_box div{float: left;}
        .shop_con_img{width: 20.7vw; margin-right: 2%; height: 11.7vh;}
        .shop_con_img img{width: 100%;height: 100%}
        .shop_con_num{position: absolute; bottom: 0; right: 0;}
        .detailed_address{color: #999999; margin-top: 2%; margin-bottom: 4%;}
        .screen_status{text-align: center; border: 1px #ff7d09 solid; color: #ff7d09; border-radius: 20px; display: inline-block; padding: 2px 10px;}
        .shop_con_num span{background: #eeeeee; display: inline-block; padding: 4px 10px; border-radius: 3px;}
        .shop_con_text{width: 74%;}
        .shop_con_adds,.detailed_address{width: 100%;overflow: hidden;white-space: nowrap;text-overflow: ellipsis;}
        .img{text-align: center;    margin-top: 20%;}
        .img img{width: 40%}
        .txt{text-align: center;margin-top: 10%}
    </style>
</head>
<body >
<div class="sy_loadingpage" style="display: none;">
    <img src="/static/images/loading.gif">
    <p>正在加载...</p>
</div>
<!--暂无待安装业务-->
<div class='sy_nowaitins_shop' style="display: none;">
    <p class='img'>
        <img src='/static/images/waitinstalled_shop.png'>
    </p>
    <p class='txt'>暂无待安装店铺</p>
</div>
<ul class="shop_ul" id="listli">
</ul>
<br />
<script src="/static/js/jquery-1.7.2.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    //用来判断类型的函数   1、店铺入驻 2、更换屏幕 3、拆除屏幕 4、新增屏幕 shop  2、待安装 3、安装待审核 4、安装未通过
    function judgeType(type,shop) {
        if(shop == 'shop'){
            wordType = '新店安装';
            return wordType;
        }
        switch (type){
            case "1":
                return '新店安装';
                break;
            case "2":
                return '屏幕更换';
                break;
            case "3":
                return '屏幕拆除';
                break;
            case "4":
                return '屏幕新增';
                break;
        }
    }
    //用来判断状态的函数  0.申请维护，1.待安装(指派)，2.待审核，3.审核未通过，4.维护完成   0、未激活 1、已激活
    function judeStatus(status,screen_status,shop,main_type) {
        if(shop == 'shop'){
            var word = '';
            switch (status){
                case "2":
                    word = '未完成';
                    break;
                case "3":
                    if(screen_status > 0){
                        word = '待审核';
                    }else{
                        word = '安装错误';
                    }
                    break;
                case "4":
                    word = '审核未通过';
                    break;
            }
            return word;
        }
        switch (status){
            case "1":
                return '未完成';
                break;
            case "2":
                if(main_type == 3){
                    return '待审核';
                }
                if(screen_status == 0){
                    return '安装错误';
                }else{
                    return '待审核';
                }
                break;
            case "3":
                return '审核未通过';
                break;
        }
    }
    //判断跳转地址的函数   1、店铺入驻 2、更换屏幕 3、拆除屏幕 4、新增屏幕
    function judgeUrl(type,shop,status) {
        var toUrl = '';
        if(shop == 'shop'){
            return '/screen/screencheck?operate=1&';
        }
        switch (type){
            case "2":
                if(status == 1){
                    toUrl = '/screen/hd?operate=2&status=1&';
                }else {
                    toUrl = '/screen/change?operate=2&';
                }

                break;
            case "3":
                toUrl = '/screen/remove-view?operate=3&';
                break;
            case "4":
                if(status == 1){
                    toUrl = '/screen/screencheck?operate=4&';
                }else{
                    toUrl = '/screen/new-hd?operate=4&';
                }
                break;
        }
        return toUrl;
    }
    //专门判断公园或楼宇海报LED状态的函数
    function judgeBuildParkSataus(status) {
        var statusName = '';
        switch (status){
            case '1':
                statusName = '审核未通过';
            break;
            case '2':
                statusName = '未完成';
                break;
            case '3':
                statusName = '待审核';
                break;
            case '4':
                statusName = '安装错误';
                break;
            default:
                statusName = '';
        }
        return statusName;
    }
    //获取数据
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    function showshop(){
        $.ajax({
            url:baseApiUrl+"/screeninstall/screenshoplist?token=<?=$token?>",
            type:"get",
            dataType:"json",
            success:function(data) {
                //console.log(data);
                //初始化待指派的店铺  status="<div class='cooperate nowaited'>待更换</div> ";
                if(data.status == 200 && data.data != null){
                    var html="";
                    var installType="";
                    var installStatus = '';
                    var installToUrl = '';
                    var area_name = '';
                    var area_address = '';
                    var shop_name = '';
                    var screen_number = '';
                    var an_time = '';
                    var is_build = 0;
                    var id = 0;
                    var equipment = 0;
                    $.each(data.data,function(i,value){
                        an_time = value.assign_at;
                        if(value.maintain_type){
                            is_build = 0;
                            shop_name = value.shop_name;
                            area_name = value.shop_area_name;
                            area_address = value.shop_address;
                            screen_number = value.replace_screen_number;
                            installType = judgeType(value.maintain_type);
                            installStatus = judeStatus(value.status,value.screen_status,'',value.maintain_type);
                            installToUrl = judgeUrl(value.maintain_type,'',value.status)+'token=<?=$token?>&shopid='+value.shop_id+'&replace_id='+value.id+'&status='+value.status;
                        }else if(value.mongo_id){//线下维护
                            is_build = 0;
                            shop_name = value.shop_name;
                            area_name = value.shop_area_name;
                            area_address = value.shop_address;
                            screen_number = value.screen_number;
                            installType = '广告维护';
                            installStatus = '未完成';
                            installToUrl = '/screen/maintain-confirm?token=<?=$token?>&shop_id='+value.shop_id+'&mongo_id='+value.mongo_id+'&id='+value.id;
                        }else if(value.build){//build led or poster
                            shop_name = value.shop_name;
                            area_name = value.shop_area_name;
                            area_address = value.address;
                            screen_number = value.screen_number;
                            installType = value.led ? 'LED' : '海报';
                            equipment = value.led ? '1' : '2';
                            //1、申请未通过 2、待安装 3、安装待审核 4、安装未通过
                            installStatus = '';
                            installStatus = judgeBuildParkSataus(value.status);
                            //判断是楼宇还是公园
                            is_build = 1;
                            installToUrl = 'javascript:void(0);';
                            id = value.id;
                        }else if(value.park){
                            shop_name = value.shop_name;
                            area_name = value.shop_area_name;
                            area_address = value.address;
                            screen_number = value.screen_number;
                            installType = '海报';
                            equipment = '2';
                            installStatus = judgeBuildParkSataus(value.status);
                            is_build = 2;
                            installToUrl = 'javascript:void(0);';
                            id = value.id;
                        }else {
                            is_build = 0;
                            shop_name = value.name;
                            area_name = value.area_name;
                            area_address = value.address;
                            screen_number = value.screen_number;
                            installType = judgeType(value.status,'shop',value.screen_status);
                            installStatus = judeStatus(value.status, value.screen_status, 'shop');
                            installToUrl = judgeUrl(value.maintain_type, 'shop')+'token=<?=$token?>&shopid='+value.id+'&status='+value.status;
                        }
                        html = html + '<li><div class="shop_tt_box">' +
                               ' <img class="shop_ico" src="/static/images/shop_ico.png" ><span class="shop_tt">'+shop_name+'</span><span class="biz_status">'+installType+'</span><span class="biz_time">'+an_time+'</span></div>' +
                               '<a href="'+installToUrl+'">' +
                               '<div class="shop_con_box" data-id="'+id+'" eq="'+equipment+'" data-type="'+is_build+'">' +
                               '<div class="shop_con_img">' +
                               '<img src="'+value.shop_image+'" >' +
                               '</div>' +
                               '<div class="shop_con_text">' +
                               '<p class="shop_con_adds">'+area_name+'</p>' +
                               '<p class="detailed_address">'+area_address+'</p>' +
                               '<p class="screen_status">'+installStatus+'</p>' +
                               '</div>' +
                               '<div class="shop_con_num">' +
                               '<span>'+screen_number+'台</span>' +
                               '</div>' +
                               '</div></a></li>';
                    });
                    if(html == ""){
                        $(".sy_loadingpage").hide();
                        $(".sy_nowaitins_shop").show();
                    }else{
                        $("#listli").html(html);
                        $(".sy_loadingpage").hide();
                        $(".sy_screamins").show();
                    }
                } else if (data.status == 731) {
                    // 已离职成员
                    $(".sy_loadingpage").hide();
                    $(".sy_nowaitins_shop").find('.txt').html('暂无权限查看此模块');
                    $(".sy_nowaitins_shop").show();
                } else{
                    $(".sy_loadingpage").hide();
                    $(".sy_nowaitins_shop").show();
                }
            }
        });
    }
    //初始化数据
    $(function(){
        showshop();
    });
    //若是楼宇或公园点击与移动端交互
    $('.shop_con_box').live('click', function () {
        var is_build = $(this).attr('data-type');
        if(is_build > 0){
                var id = $(this).attr('data-id');
                var eq = $(this).attr('eq');
                var ua = navigator.userAgent.toLowerCase();
                var result = {"action":is_build == 1 ? 'building' : 'park', 'id':id, 'equipment':eq}
                if(/android/.test(ua)) {
                    window.jsObj.HtmlcallJava(JSON.stringify(result));
                }else{
                    webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
                }
        }
    })
</script>
</body>
</html>