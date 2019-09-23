

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>玉龙传媒</title>
    <meta name="apple-touch-fullscreen" content="YES" />
    <meta name="format-detection" content="telephone=no" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta http-equiv="Expires" content="-1" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/mreset.css" />
    <link rel="stylesheet"  href="/static/css/sy-mindex.css?v=180917" />
</head>
<body class="sy-body">
<!--头部-->
<div class="index-header">
    <span class="logo"><img src="/static/image/header-logo.png"></span>
    <span class="menu"><img src="/static/image/header-menu.jpg"></span>
    <!--导航-->
    <div class="index-nav">
        <p class="close"><img src="/static/image/header-navmenu.jpg"</p>
        <ul class="nav">  <!--<li data-menuanchor="firstPage" class="menuList"><a href="#firstPage">-->
            <li><a href="#firstPage">首页</a></li>
            <li><a href="#fourthPage">品牌案例</a></li>
        </ul>
    </div>
</div>
<!--first-screen-->
<div class="section first-screen" id="firstPage">

	    <p class="sec-one-hy"><img src="/static/image/sc-one-nrj1.png"></p>
	    <div class="sec-one-tit">
	    	 <p class="txta">玉龙传媒</p>
	    	 <p class="txtb">LED屏广告</p>
	    	 <p class="linedan"></p>
	    	 <p class="lineshuang"></p>
		     <div class="sec-one-con">
		        公司简介：
		        <p>玉龙传媒即北京玉龙腾飞影视传媒有限公司，是主要从事各类公共场所LED屏深度建设和拓展的影视广告媒体公司。</p>
		        <p>玉龙传媒致力于提供国际一流标准的LED屏影视广告传播服务，为国内外用户提供优质的媒体内容，成为全球用户终身信赖的媒体资源，并努力使我们的用户在我们的服务中真正获得所期望的经济效益和社会效益。</p>
		     </div>
	    </div>
	    <p class="sec-one-img"><img src="/static/image/sc-one-nrj2.png"></p>
	    <div class="sec-one-arrow">
	        <img src="/static/image/Indicator-icon.png">
	        <p>上滑查看更多</p>
	    </div>
</div>

<div class="section four-screen" id="fourthPage">
    <div class="sec-four-wap _hide">
        <h2 class="title">品牌案例</h2>
        <div class="con">
            <ul>
                <li><img src="/static/image/temp/img02.jpg"></li>
                <li><img src="/static/image/temp/img01.jpg"></li>
                <li><img src="/static/image/temp/img05.png"></li>
                <li><img src="/static/image/temp/img25.jpg"></li>
                <li><img src="/static/image/temp/img06.jpg"></li>
                <li><img src="/static/image/temp/img03.png"></li>
                <li><img src="/static/image/temp/img04.png"></li>
                <li><img src="/static/image/temp/img22.jpg"></li>
                <li><img src="/static/image/temp/img15.jpg"></li>
                <li><img src="/static/image/temp/img14.jpg"></li>
                <li><img src="/static/image/temp/img07.jpg"></li>
                <li><img src="/static/image/temp/img10.jpg"></li>
                <li><img src="/static/image/temp/img08.jpg"></li>
                <li><img src="/static/image/temp/img21.jpg"></li>
                <li><img src="/static/image/temp/img17.jpg"></li>
                <li><img src="/static/image/temp/img23.jpg"></li>
                <li><img src="/static/image/temp/img16.png"></li>
                <li><img src="/static/image/temp/img09.jpg"></li>
                <li><img src="/static/image/temp/img11.jpg"></li>
                <li><img src="/static/image/temp/img13.jpg"></li>
                <li><img src="/static/image/temp/img18.jpg"></li>
                <li><img src="/static/image/temp/img19.jpg"></li>
                <li><img src="/static/image/temp/img20.jpg"></li>
                <li><img src="/static/image/temp/img24.jpg"></li>
            </ul>
        </div>
    </div>
</div>
<!--five-screen-->
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

<script type="text/javascript" src="/static/js/jquery.js" ></script>
<script>
    $(function(){
        //设置第一屏的高度
        var scH=$(window).height();
        $('.first-screen').css('height',scH);
        //导航
        $('.index-header .menu').click(function(){
            $(this).hide();
            $('.index-nav').animate({right:0},500)
        })
        //导航关闭
        $('.index-nav .close,.index-nav li').click(function(){
            $('.index-nav').animate({right:"-44%"},500);
            setTimeout(function(){$('.index-header .menu').show()},400)
        })
        //显示
        setTimeout(function(){$('._hide').show()},200);
    })
</script>
</body>
</html>
