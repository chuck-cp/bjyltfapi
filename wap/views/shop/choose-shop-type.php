<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>选择店铺类型</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet"  href="/static/css/sy_selectshop.css?v=20180925" />
</head>
<body class="sy-body">
<div class="sy_selshop_class">
    <ul>
        <li><a href="<?=\yii\helpers\Url::to(['/shop/head-office-create','token'=>$token,'dev'=>$dev,'active_id'=>$active_id])?>"><img src="/static/images/sy_shopclass1.png?v=20180925"></a></li>
        <?if(!$active_id || $active_id == 'null'):?>
            <li class="sy_xzls_cm"><a href="javascript:;"><img src="/static/images/sy_shopclass2.png?v=20180925"></a></li>
        <?endif?>
        <li><a href="<?=\yii\helpers\Url::to(['/shop/inner-create','token'=>$token,'dev'=>$dev,'shop_operate_type'=>2,'active_id'=>$active_id])?>"><img src="/static/images/sy_shopclass3.png?v=20180925"></a></li>
        <li><a href="<?=\yii\helpers\Url::to(['/shop/inner-create','token'=>$token,'dev'=>$dev,'shop_operate_type'=>1,'active_id'=>$active_id])?>"><img src="/static/images/sy_shopclass4.png?v=20180925"></a></li>
    </ul>

</div>
<div class="mask"></div>
<!--弹框-->
<div class="sy_selshop_tankan">
    <div class="content">
        <img class="bgimg" src="/static/images/sy_selectshop_bg.png">
        <div class="con">
            <p class="sy_cngne"> 选择连锁店铺所属公司后<br>即默认为受总部管理<br><em>店铺费用和设备维护费用由总部统一收取</em><br>相关店铺信息不可更改</p>
        </div>
    </div>
    <div class="sy_btn">
        <a href="javascript:;" class="cancel">取消</a>
        <a href="<?=\yii\helpers\Url::to(['/shop/select-head-shop','token'=>$token,'dev'=>$dev,'active_id'=>$active_id])?>" class="agree">同意</a>
    </div>

</div>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script type="text/javascript" src="/static/js/sy_selectshop.js"></script>
<script>
    //调用弹框
    $('.sy_xzls_cm').click(function(){
        tankuang('sy_selshop_tankan')
    })
    //关闭弹框
    //closetankuan('sy_selshop_tankan')
    $('.sy_btn .cancel').click(function(){
        closetankuan('sy_selshop_tankan')
    })
</script>
</body>
</html>






