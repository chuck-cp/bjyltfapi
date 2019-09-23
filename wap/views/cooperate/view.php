<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>业务合作政策</title>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/cooperate.css" />
</head>
<body class="sy-body">
<div class="cooperate">
    <?if(!isset($_GET['header'])):?>
    <!--返回导航条-->
    <div class="cp-return">
        <a href="<?=\yii\helpers\Url::to(['/index'])?>" class="icon"><img src="/static/image/fanhui.png"></a>
        <h3>业务合作政策</h3>
    </div>
    <?endif?>
    <div class="cooperate-tit"><img src="/static/image/cooperation-title.jpg"></div>
    <div class="nav-blank"></div>
    <div class="cop-nav clearfix">
        <ul>
            <li <?if($action == 1):?>class="hover"<?endif?>>合作方式之一<span></span></li>
            <li <?if($action == 2):?>class="hover"<?endif?>>合作方式之二<span></span></li>
            <li <?if($action == 3):?>class="hover"<?endif?>>合作方式之三<span></span></li>
        </ul>
    </div>
    <div class="cop-content">
        <!--第一部分-->
        <div class="part-one part-con" style="display: <?=$action == 1 ? 'block' : 'none'?>">
            <h3 class="title">成为【安装LED屏的联系人】</h3>
            <p class="small-title">1.成为【安装LED屏的联系人】的条件：</p>
            <p class="wz-nr">无条件成为【安装LED屏的联系人】，无论您身处大城市、小城市甚至是乡镇，只要您有意愿，只要您身边有正规注册，并有wifi的理发店，或者在其他地方能联系上这样的理发店，都可以免费为其安装LED屏。</p>
            <p class="small-title">2. 成为【安装LED屏的联系人】的好处：</p>
            <p class="wz-nr">成为安装LED屏的联系人，在理发店免费安装LED屏，每安装一家理发店，领红包100元。安装超过6家店（含）马上可以成为玉龙传媒的业务合作人，可即刻领红包300元。更重要的是，玉龙传媒的业务合作人能代表玉龙传媒承接广告业务，有可能赚大钱，因为业务合作人的广告佣金为广告费的6%（这是税后净得的佣金，玉龙传媒已经为你承担了相应的税金），而且还有一系列特权。</p>
            <p class="small-title">3.能免费为其安装LED屏的理发店：</p>
            <p class="wz-nr">正规注册，并有wifi的理发店都可为其安装LED屏，并且玉龙传媒每年都会给店家支付一定的店铺费用，首次安装一次性支付第一年的店铺费用，从第二年开始按月发放店铺费用，而且，除了店铺费用外，玉龙传媒还每月支付给店家设备维护费，全国统一每年每块屏幕72元，即每月每块屏6元，与店家的具体合作条件见:<a href="/appagreement/install_condition"  class="cp-condition">《LED屏合作条件》</a> 。</p>
            <p class="small-title">4.操作步骤（如何操作/如何领红包）：</p>

            <p class="step-title">第一步：扫码下载玉龙传媒APP，填写手机号、验证码、邀请码、密码，注册成为安装LED屏的联系人。</p>
            <p class="img-nr"><img width="40%" src="/static/image/cop-one1.png"></p>
<!--            <p class="wz-nr">邀请码是记录发展下线（一至六级下线）的识别依据，如您是其他业务人发展的下线，注册时，请填写邀请码（即您的上线业务人）的手机号码，系统会自动记录您的上下线关系，业务合作人六级下线的广告提成会根据此记录进行计算。如您无上线发展人，则注册时“邀请码”字段可以不添加。</p>-->
<!--            <p class="wz-nr">在您注册玉龙传媒APP后，注册时所用的手机号码即是您发展下线的邀请码，您发展的下线人员在注册玉龙传媒APP时，请您将此邀请码告知并让下线人员填写（注册页面中的邀请码字段），则下线人员注册成功后，系统会自动记录您与下线人员的等级关系。一至六级下线关系以此记录，您直接发展的下线是您的一级下线，您的一级下线直接发展的下线则是您的二级下线，以此类推直至六级下线。</p>-->

            <p class="step-title">第二步：联系理发店，指导理发店申请安装LED屏。</p>
            <p class="wz-nr">9. 关注“玉龙传媒”微信公众号，点击“LED屏申请”，或下载玉龙传媒APP，点击“工作-我的店铺”模块的“申请安装”按钮，填写信息并提交。正规注册，并有wifi的理发店都可免费为其安装LED屏。店家需准备的主要资料有：身份证正面照、营业执照照片、店铺门脸照片、室内全景照片。</p>
            <p class="wz-nr"><em class="red">注意：</em>填写信息时，请提醒理发店在联系人/业务合作人处填写您的手机号码。这是作为您的业绩统计的依据。</p>
            <p class="img-nr"><img width="40.8%" src="/static/image/cop-one2.png"></p>
            <p class="step-title">第三步：等待审核。</p>
            <p class="wz-nr">屏幕申请审核通过后，玉龙传媒会将屏幕寄送给理发店，LED屏到店后，玉龙传媒会指派装机人员上门安装。联系人可在玉龙传媒APP内随时查看LED屏幕的最新安装动态。</p>
            <p class="img-nr"><img width="34.8%" src="/static/image/cop-one3.png"></p>
            <p class="step-title">第四步：屏幕安装完成，领红包。</p>
            <p class="wz-nr">每安装成功一家店的LED屏幕，玉龙传媒APP账户里就会相应到账100元。联系人可在玉龙传媒APP内，点击工作模块的“业绩与提现”按钮，查看账户余额和明细，并申请提现。同时玉龙传媒玉龙传媒每年都会给店家支付一定的店铺费用，首次安装一次性支付第一年的店铺费用，从第二年开始按月发放店铺费用，而且，除了店铺费用外，玉龙传媒还每月支付给店家设备维护费，全国统一每年每块屏幕72元，即每月每块屏6元。<a href="/appagreement/install_condition"  class="cp-condition">见:《LED屏合作条件》</a>。</p>
            <p class="img-nr"><img width="40%" src="/static/image/cop-one4.png"></p>
            <p class="small-title">5、说明</p>
            <p class="wz-nr">玉龙传媒更详细的安装LED屏联系人更详细的合作政策见<a href="<?=\yii\helpers\Url::to(["appagreement/concurrent_post_agreement"])?>" class="cp-condition">《玉龙传媒业务合作政策》</a>（在玉龙传媒APP的“业务合作政策”模块），以上内容与《玉龙传媒业务合作政策》有矛盾之处以《玉龙传媒业务合作政策》为准。玉龙传媒合作政策的解释权在北京玉龙腾飞影视传媒有限公司。</p>
        </div>
        <!--第二部分-->
        <div class="part-two part-con" style="display: <?=$action == 2 ? 'block' : 'none'?>">
            <h3 class="title">成为【业务合作人】</h3>
            <p class="small-title">1.成为【业务合作人】的条件：</p>
            <p class="wz-nr">联系安装6家以上（含）理发店的LED屏，就可成为玉龙传媒【业务合作人】。</p>
            <p class="small-title">2. 成为【业务合作人】的好处/特权：</p>
            <p class="cop-privilege"><img src="/static/image/privilege.png" width="16px">特权一</p>
            <p class="wz-nr">成为业务合作人即刻领300元现金红包。</p>
            <p class="wz-nr">成为业务合作人后，可在玉龙传媒APP“业绩与提现”中看到自己的账户余额即刻增加300元，并可申请提现。</p>
            <p class="img-nr"><img width="40%" src="/static/image/cop-two1.png"></p>
            <p class="cop-privilege"><img src="/static/image/privilege.png" width="16px">特权二</p>
            <p class="wz-nr">成为业务合作人后安装一家理发店酬劳为150元（备注：普通人安装一家理发店酬劳为100元）。特别提示：玉龙传媒每年都会给店家支付一定的店铺费用，首次安装一次性支付第一年的店铺费用，从第二年开始按月发放店铺费用，而且，除了店铺费用外，玉龙传媒还每月支付给店家设备维护费，全国统一每年每块屏幕72元，即每月每块屏6元，与店家的具体合作条件见：<a href="/appagreement/install_condition"  class="cp-condition">《LED屏合作条件》</a>。</p>
            <p class="cop-privilege"><img src="/static/image/privilege.png" width="16px">特权三</p>
            <p class="wz-nr">业务合作人可以代表玉龙传媒联系广告业务，且佣金是广告费的6%（这是税后净得的佣金，玉龙传媒已经为你承担了相应的税金）。非业务合作人没有权利承接玉龙传媒的广告业务，也不能获得相应的提成。只有玉龙传媒的业务合作人才有此特权。</p>
            <p class="img-nr"><img width="34.93%" src="/static/image/cop-two2.png"></p>
            <p class="cop-privilege"><img src="/static/image/privilege.png" width="16px">特权四</p>
            <p class="wz-nr">享有总公司400业务电话的转接权。总公司接到LED屏幕安装或广告业务电话，优先转接给相应的业务合作人。</p>
            <p class="cop-privilege"><img src="/static/image/privilege.png" width="16px">特权五</p>
            <p class="wz-nr">享有专门负责的区域一定的业务配合费。业务合作人员可选择自己的负责区域，如有其他人在自己负责的区域联系到广告客户（不是投放广告），则可获得此广告业务合作人提成的5%作为配合费。</p>
            <p class="img-nr"><img width="40%" src="/static/image/cop-two3.png"></p>
            <p class="cop-privilege"><img src="/static/image/privilege.png" width="16px">特权六</p>
            <p class="wz-nr">业务突出者有可能成为玉龙传媒正式员工或区域总代理。</p>
            <p class="small-title">3.业务合作人的责任和义务：</p>
            <p class="wz-nr">3.1  业务合作人有责任巡查负责区域内的LED屏，以免被破坏，且要保障数量不减少。</p>
            <p class="wz-nr">3.2  如负责区域内的LED屏有故障，业务合作人协助公司处理LED屏的故障问题。</p>
            <p class="small-title">4、说明</p>
            <p class="wz-nr">玉龙传媒更详细的业务合作人的合作政策见<a href="<?=\yii\helpers\Url::to(["appagreement/concurrent_post_agreement"])?>" class="cp-condition">《玉龙传媒业务合作政策》</a>（在玉龙传媒APP的“业务合作政策”模块），以上内容与《玉龙传媒业务合作政策》有矛盾之处以《玉龙传媒业务合作政策》为准。玉龙传媒合作政策的解释权在北京玉龙腾飞影视传媒有限公司。</p>
        </div>
        <!--第三部分-->
        <div class="part-three part-con" style="display: <?=$action == 3 ? 'block' : 'none'?>">
            <h3 class="title">【介绍业务合作人】详情介绍</h3>
            <p class="small-title">1.【介绍业务合作人】的条件：</p>
            <p class="wz-nr">任何人都有介绍业务合作人的权利，你所介绍的业务合作人将记录在案，但只有当你自己联系安装6家以上（含）理发店的LED屏，也就是你已经成为玉龙传媒业务合作人后，才能享受介绍业务合作人的相应佣金。</p>
            <p class="small-title">2. 【介绍业务合作人】的好处：</p>
            <p class="wz-nr">玉龙传媒将额外拿出一部分佣金奖励介绍业务合作人者，介绍业务合作人者将可轻松赚取长期稳定的额外佣金，其佣金是你所介绍的业务合作人所拿直接佣金的10%，以上是税后净得的佣金，玉龙传媒已经为你承担了相应的税金。</p>
            <p class="wz-nr">需要说明的是你所介绍的业务合作人也可能再介绍业务合作人，他（她）也可享受介绍业务合作人应拿的额外佣金，他（她）的这部分佣金不计算在你的10%的佣金基数中，即你所拿的10%的额外佣金的计算基数只是你介绍的业务合作人自己直接承揽业务所拿的佣金，玉龙传媒没有设立多级享受业务提成的政策。</p>
<!--            <p class="small-title">3.查看介绍的业务合作人：</p>-->
<!--            <p class="img-nr"><img width="40%" src="/static/image/cop-three1.png"></p>-->
<!--            <p class="wz-nr">在您注册玉龙传媒APP后，注册时所用的手机号码即是您发展下线的邀请码，您发展的下线人员在注册玉龙传媒APP时，请您将此邀请码告知并让下线人员填写（注册页面中的邀请码字段），则下线人员注册成功后，系统会自动记录您与下线人员的等级关系。一至六级下线关系以此记录，您直接发展的下线是您的一级下线，您的一级下线直接发展的下线则是您的二级下线，以此类推直至六级下线。</p>-->
            <p class="small-title">3.查看介绍的业务合作人：</p>
            <p class="wz-nr">在玉龙传媒APP内，进入工作模块，点击“伙伴列表”按钮，可查看自己所介绍的业务合作人详细信息。</p>
            <p class="img-nr"><img width="80.4%" src="/static/image/cop-three2.png"></p>
            <p class="small-title">4、说明</p>
            <p class="wz-nr">玉龙传媒更详细的介绍业务合作人的合作政策见<a href="<?=\yii\helpers\Url::to(["appagreement/concurrent_post_agreement"])?>" class="cp-condition">《玉龙传媒业务合作政策》</a>（在玉龙传媒APP的“业务合作政策”模块），以上内容与<a href="<?=\yii\helpers\Url::to(["appagreement/concurrent_post_agreement"])?>">《玉龙传媒业务合作政策》</a>有矛盾之处以<a href="<?=\yii\helpers\Url::to(["appagreement/concurrent_post_agreement"])?>">《玉龙传媒业务合作政策》</a>为准。玉龙传媒合作政策的解释权在北京玉龙腾飞影视传媒有限公司。</p>
        </div>
    </div>

</div>
<!--底部-->
<div class="section five-screen">
    <div class="sec-five-wap _hide">
        <div class="sec-five-cn1">
            <p><img src="/static/image/lx-tel.png"><span>联系电话<br><?php echo $service_phone;?></span></p>
            <p><img src="/static/image/lx-email.png"><span>邮箱：<br><?php echo $service_email;?></span></p>
        </div>
        <div class="sec-five-cn2">
            <img src="/static/image/lx-logo.png">玉龙传媒
        </div>
        <div class="sec-five-cn3">copyright©2018北京玉龙腾飞影视传媒有限公司</div>
    </div>
</div>
<!--返回顶部-->
<p class="goback"><img src="/static/image/goback.png"></p>
<script type="text/javascript" src="/static/js/jquery.js" ></script>
<script>
    /*置顶效果*/
    $(function(){
        /*tab标签切换*/
        $('.cop-nav li').click(function(){
            $(this).find('span').show();
            $(this).siblings().find('span').hide();
            $('.part-con').eq($(this).index()).show().siblings().hide();
        })

        /*置顶效果*/
        var top=$('.cooperate-tit').height();
        $(window).scroll(function(){
            var scrollTop=$(document).scrollTop();
            if(scrollTop<top)
            {
                $('.cop-nav').removeClass('fixed');
                $('.nav-blank').hide();
                $('.goback').hide();
            }
            else
            {
                $('.cop-nav').addClass('fixed');
                $('.nav-blank').show();
                $('.goback').show();
            }
        });
        /*返回顶部*/
        $('.goback').click(function(){
            $("html,body").animate({scrollTop:0}, 500)
        })

    })
</script>
</body>
</html>
