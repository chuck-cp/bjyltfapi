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
    <h3 class="title">请选择所属连锁公司名称</h3>
    <div class="sy_spsearch">
        <span class="icon"><img src="/static/images/sy_selectshop_search.png"></span>
        <input type="text" class="sy_searchtxt">
    </div>

    <div id="none_head_shop" class="sy_select_blank display_none">
        <img src="/static/images/sy_selectshop_nocompany.jpg">
        <p>无连锁公司</p>
    </div>
    <div id="none_search_result" class="sy_select_blank display_none">
        <img src="/static/images/sy_selectshop_search.jpg">
        <p>无搜索结果</p>
    </div>

    <div class="sy_spsearch_con">
        <ul>

        </ul>
    </div>
    <button class="sy_selectsp_btn" type="button">选择公司</button>
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
        url: baseApiUrl+"/shop-head/shop-head-list?token=<?=$token?>&wechat_id=<?=$wechat_id?>",
        type: "GET",
        async: false,
        success:function (data) {
            var item = data.data;
            if(item == null){
                $('#none_head_shop').removeClass("display_none");
                $('.sy_spsearch').addClass("display_none");
            }else{
                var i;
                var html = "";
                for(i in item){
                    if (item[i].activity_detail_id > 0) {
                        html += '<li class="sy_shoplist"><p class="name">'+item[i].company_name+' <font color="red">推荐订单</font></p><p class="unselected"><input type="radio" name="shopname" value="'+item[i].id+'"></p></li>';
                    } else {
                        html += '<li class="sy_shoplist"><p class="name">'+item[i].company_name+' </p><p class="unselected"><input type="radio" name="shopname" value="'+item[i].id+'"></p></li>';
                    }
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
                    url: baseApiUrl+"/shop-head/shop-head-list?token=<?=$token?>&wechat_id=<?=$wechat_id?>&keyword="+keyword,
                    type: "GET",
                    async: false,
                    success:function (data) {
                        $('.sy_spsearch_con ul').html("");
                        var i;
                        var item = data.data;
                        if(item == null){
                            $('#none_search_result').removeClass("display_none");
                        }else{
                            $('#none_search_result').addClass('display_none');
                            var html = "";
                            for(i in item){
                                html += '<li class="sy_shoplist"><p class="name">'+item[i].company_name+'</p><p class="unselected"><input type="radio" name="shopname" value="'+item[i].id+'"></p></li>';
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
        var headquarters_id = 0;
        $('.sy_shoplist').find('input').each(function(){
            if($(this).prop('checked')==true){
                headquarters_id = $(this).val();
            }
        })
        if(headquarters_id == 0){
            tankuangparam('sy_waitins_choicetwo','请选择所属连锁公司名称')
            return;
        }
        window.location.href="<?=\yii\helpers\Url::to(['shop/select-branch-shop','token'=>$token,'wechat_id'=>$wechat_id,'dev'=>$dev])?>&headquarters_id="+headquarters_id;
    })
</script>

<script>
</script>
</body>
</html>
