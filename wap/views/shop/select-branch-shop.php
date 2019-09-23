<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>连锁店铺</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet"  href="/static/css/sy_selectshop.css?v=20180823" />
</head>
<body class="sy-body">
<div class="sy_selectshop">
    <h3 class="title">请选择对应分店信息</h3>
    <div class="sy_spsearch">
        <span class="icon"><img src="/static/images/sy_selectshop_search.png"></span>
        <input type="text" class="sy_searchtxt" oninput="searchfun()">
    </div>
    <div id="none_search_result" class="sy_select_blank display_none">
        <img src="/static/images/sy_selectshop_search.jpg">
        <p>无搜索结果</p>
    </div>
    <div id="none_branch_shop" class="sy_select_blank display_none">
        <img src="/static/images/sy_selectshop_noshop.jpg">
        <p>无分店店铺</p>
    </div>
    <div class="sy_spsearch_con">
        <ul>
        </ul>
    </div>
    <button class="sy_selectsp_btn" type="button">申请店铺</button>
</div>
<!--mask遮罩层-->
<div class="mask"></div>
<!--不可同时选择两种状态弹框-->
<div class="sy_waitins_choicetwo">
    <div class="img">
        <p></p>
        <img class="bgimg" src="/static/images/sy_waitinstall_panelbgtwo.png">
        <span class="sy_waitins_close"><img src="/static/images/sy_waitinstall_close.png"></span>
    </div>
</div>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script type="text/javascript" src="/static/js/sy_selectshop.js"></script>
<script>
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    $.ajax({
        url: baseApiUrl+"/shop-head/shop-branch-list?token=<?=$token?>&wechat_id=<?=$wechat_id?>&headquarters_id=<?=$headquarters_id?>",
        type: "GET",
        async: false,
        success:function (data) {
            var item = data.data;
            if(item == null){
                $('#none_branch_shop').removeClass("display_none");
                $('.sy_spsearch').addClass("display_none");
            }else{
                var i;
                var html = "";
                for(i in item){
                    html += '<li class="sy_shopaddress"><div class="listbox"><p class="name">'+item[i].branch_shop_name+'</p><p class="adress">'+item[i].branch_shop_area_name+'</p><p class="detailadress">'+item[i].branch_shop_address+'</p></div><p class="unselected"><input type="radio" name="shopname" value="'+item[i].id+'"></p></li>';
                }
                $('.sy_spsearch_con ul').append(html);
            }
        },
        error:function (data) {
            tankuangparam('sy_waitins_choicetwo','服务器错误')
        }
    });
    var flag = true;
    $('.sy_searchtxt').on('compositionstart',function(){
        flag = false;
    })
    $('.sy_searchtxt').on('compositionend',function(){
        flag = true;
    })
    $('.sy_searchtxt').on('input',function(){
        var keyword = $('.sy_searchtxt').val();
        setTimeout(function(){
            if(flag){
                $.ajax({
                    url: baseApiUrl+"/shop-head/shop-branch-list?token=<?=$token?>&wechat_id=<?=$wechat_id?>&headquarters_id=<?=$headquarters_id?>&keyword="+keyword,
                    type: "GET",
                    async: false,
                    success:function (data) {
                        $('.sy_spsearch_con ul').html("");
                        var item = data.data;
                        if(item == null){
                            $('#none_search_result').removeClass("display_none");
                        }else{
                            $('#none_search_result').addClass('display_none');
                            var i;
                            var html = "";
                            for(i in item){
                                html += '<li class="sy_shopaddress"><div class="listbox"><p class="name">'+item[i].branch_shop_name+'</p><p class="adress">'+item[i].branch_shop_area_name+'</p><p class="detailadress">'+item[i].branch_shop_address+'</p></div><p class="unselected"><input type="radio" name="shopname" value="'+item[i].id+'"></p></li>';
                            }
                            $('.sy_spsearch_con ul').append(html);
                        }
                    },
                    error:function (data) {
                        tankuangparam('sy_waitins_choicetwo','服务器错误')
                    }
                });
            }
        },0)
    })

    $('.sy_selectsp_btn').click(function(){
        var branch_shop_id = 0;
        $('.sy_shopaddress').find('input').each(function(){
            if($(this).prop('checked')==true){
                branch_shop_id = $(this).val();
            }
        })
        if(branch_shop_id == 0){
            tankuangparam('sy_waitins_choicetwo','请选择对应分店信息')
            return;
        }
        window.location.href="<?=\yii\helpers\Url::to(['shop/branch-install','token'=>$token,'wechat_id'=>$wechat_id,'dev'=>$dev])?>&headquarters_id=<?=$headquarters_id?>&branch_id="+branch_shop_id;
    })
</script>

<script>
</script>
</body>
</html>
