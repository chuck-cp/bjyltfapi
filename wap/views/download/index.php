<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>玉龙传媒APP下载页</title>
<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
<style>
/*标签初始化*/
* {margin: 0;padding: 0 }
table {border-collapse: collapse;border-spacing: 0}
h1,h2,h3,h4,h5,h6 {font-size: 100%}
ul,ol,li {list-style: none}
em,i {font-style: normal}
img {border: 0;display:inline-block}
input,img {vertical-align: middle; border:none; }
a {color: #333;text-decoration: none;-webkit-tap-highlight-color:transparent;}
input,button,textarea{-webkit-tap-highlight-color:transparent;outline: none;  }
article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {display: block;}
body{ -webkit-overflow-scrolling: touch; background:#f1c8b3 url(/static/image/H5bg.png) no-repeat; background-size:100% auto ;}
p{ margin: 0; padding: 0;}
.download-wrap .logo{ width: 35%; height: auto; position: absolute; top: 6%; left:50%; margin-left: -17.5%;}
.download-wrap .logo img,.download-wrap .detail img,.download-wrap .person img,.download-wrap .btn img,.download-wrap .copy img{ width: 100%;}
.download-wrap .detail{ width: 86.67%; position: absolute; left: 50%; top: 30%; margin-left: -43.335%;}
.download-wrap .person{width: 82.67%; position: absolute; left: 50%; top: 35%; margin-left: -41.335%;}
.download-wrap .btn{ width: 53.33%; position: absolute;left: 50%;top: 86%; margin-left: -26.665%;}
.download-wrap .copy{ width: 58.667%;position: absolute;left: 50%;top:94%;margin-left: -29.33%;}
/*提示弹框*/
.mask{width:100%;background:#000;position:fixed;top: 0px;left:0px;display:none;z-index:100;}
.ti_img{z-index:110; position: relative; display: none;}
.ti_img img{ width: 100%;}

</style>
</head>
<body>
<div class="download-wrap">
	 <div class="logo"><img src="/static/image/sy_download_logo.png"></div>
	
	 <!--<div class="detail"><img src="/static/image/wenzi.png"></div>-->
	 <div class="person"><img src="/static/image/xgperson.png"></div>
	 <div class="btn downbtn"><a href="javascript:;"><img src="/static/image/btn.png"></a></div>
	 <div class="copy"><img src="/static/image/copy.png"></div>
</div>
<!--提示弹框-->
<div class="mask"></div>
<div class="ti_img"><img src="/static/image/wx_downloada.png"></div>
<script src="/static/js/jquery-1.7.2.min.js"></script>
<script>
$(function(){
    var is_weixin = (function(){return navigator.userAgent.toLowerCase().indexOf('micromessenger') !== -1})();	
	if(is_weixin){
		$(".downbtn").click(function(){
			tis_mask()
		})
	}else{
        $(".downbtn").click(function(){
            download()
        })
    }
	//微信提示弹框	
	function tis_mask(){
		$(".mask").css('opacity','0.3').show(); 
	    var ymheight=$(document).height()+ "px";
	    $(".mask").css("height",ymheight);
	    $('.ti_img').show();
	}

    //下载
    function download(){
        $('body').css('background','none');
        $('.download-wrap').css('visibility','hidden');
        var ua = navigator.userAgent.toLowerCase();
        if (/iphone|ipad|ipod/.test(ua)) {
            window.location ='<?=$ios?>'
        }
        else {
            window.location ='<?=$android?>'
        }
    }
	$('.mask,.ti_img').click(function(){
		 $('.ti_img').hide();
		 $(".mask").hide();
	})  
})	
</script>
</body>
</html>
