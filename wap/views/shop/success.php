<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>安装申请提交成功</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/sy_azywlb.css" />
</head>
<body>
<div class="sy_nbbox">
    <div class="sy_nbapplysuc">
        <p class="img"><img src="/static/image/sy_bnapplyscuess.png"></p>
        <?if($modify == 'modify'):?>
            <?if($shop_operate_type == 4):?>
                <p class="txt">总部申请修改成功</p>
            <?else:?>
                <p class="txt">修改店铺成功</p>
            <?endif;?>
        <?else:?>
            <p class="txt">安装申请提交成功</p>
        <?endif;?>
    </div>
    <div class="sy_nbbtn">
<!--        --><?//if($modify == 'modify'):?>
<!--            <a class="continu" href="--><?//=\yii\helpers\Url::to(['inner-install/index','token'=>$token,'wechat_id'=>$wechat_id])?><!--")?>完成</a>-->
<!--        --><?//else:?>
<!--            <a class="continu" href="--><?//=\yii\helpers\Url::to(['shop/choose-shop-type','token'=>$token,'wechat_id'=>$wechat_id,'active_id'=>$active_id])?><!--")?>完成</a>-->
            <a class="continu" href="javascript:void(0);">完成</a>
<!--        --><?//endif;?>
    </div>
</div>
</body>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script type="text/javascript">
    $('.sy_nbbtn .continu').on('click',function () {
        var ua = navigator.userAgent.toLowerCase();
        var result = {"action":"closewebview"}
        if(/android/.test(ua)) {
            window.jsObj.HtmlcallJava(JSON.stringify(result));
        }else{
            webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
        }
    })
</script>
</html>