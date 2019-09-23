<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>申请记录详情</title>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet" href="/static/css/record.css" />
</head>
<body class="sy-body">
<div class="sy-wrapper">
    <div class="sy-header">
        <h2>申请记录详情</h2>
    </div>
    <div class="sy-top-blank"></div>
    <div class="sy-feedback">
        <div class="yx_jlxq_nr">
            <ul>
                <li id="create_at"></li>
                <li id="status"></li>
                <li id="logistics"></li>
            </ul>
        </div>
        <div class="yx_sqjl_xq">
            <dl>
                <dt>申请人</dt>
                <dd id="apply_name"></dd>
            </dl>
            <dl>
                <dt>身份证号码</dt>
                <dd id="identity_card_num"></dd>
            </dl>
            <dl>
                <dt>手机号码</dt>
                <dd id="apply_mobile"></dd>
            </dl>
            <dl>
                <dt class="yx_ywlxr">联系人/业务合作人</dt>
                <dd id="member_name"></dd>
            </dl>
            <div class="yx_az_address">
                <span>安装地址</span>
                <p class="dzxq" id="area"></p>
            </div>
            <dl>
                <dt>店铺名称</dt>
                <dd id="shop_name"></dd>
            </dl>
            <dl>
                <dt>公司名称</dt>
                <dd id="company_name"></dd>
            </dl>
            <dl>
                <dt>店铺面积</dt>
                <dd id="acreage"></dd>
            </dl>
            <dl>
                <dt>安装数量</dt>
                <dd id="screen_number"></dd>
            </dl>
            <dl>
                <dt>镜面数量</dt>
                <dd id="mirror_account"></dd>
            </dl>
        </div>
    </div>
</div>
<p class="sy-installed-ts">提交成功</p>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script>
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    function tippanel(){
        $('.sy-installed-ts').show();
        setTimeout(function(){$('.sy-installed-ts').hide()},2000);
    }
    $(function(){
        $.ajax({
            url:baseApiUrl+"/v1/member/recordinfo",
            type:"get",
            dataType:"json",
            data:{id:<?=$shopid?>},
            success:function(data) {
                console.log(data);
                if(data.status==200&&data.data!=null){
                    $("#create_at").html("申请时间："+data.data.create_at);
                    $("#status").html("状态："+data.data.status);
                    if(data.data.logisticsname!=''){
                       $("#logistics").html("物流："+data.data.logisticsname+" "+data.data.logistics_id);
                    }
                    $("#apply_name").html(data.data.apply_name);
                    $("#identity_card_num").html(plusXing(data.data.identity_card_num,10,4));
                    $("#apply_mobile").html(plusXing(data.data.apply_mobile,3,4));
                    $("#member_name").html(data.data.member_name);
                    $("#area").html(data.data.area_name+data.data.address);
                    $("#shop_name").html(data.data.name);
                    $("#company_name").html(data.data.company_name);
                    $("#acreage").html(data.data.acreage+"平米");
                    $("#screen_number").html(data.data.screen_number+"台");
                    $("#mirror_account").html(data.data.mirror_account+"面");
                }else{
                    $('.sy-installed-ts').text('数据不存在');
                    tippanel();
                    setTimeout(function() {  location.href="/screen/login" },1000);
                }
            }
        });
        $(".sy-feedback-btn").click(function (){
            location.href="/shop/record";
        })
    });
    function plusXing (str,frontLen,endLen){
          var len = str.length-frontLen-endLen;
          var xing = '';
         for (var i=0;i<len;i++) {
             xing+='*';
             }
         return str.substring(0,frontLen)+xing+str.substring(str.length-endLen);
         }


</script>
</body>
</html>
