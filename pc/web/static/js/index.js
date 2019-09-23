$(function(){
	/*轮播图滚动*/
	var $ul = $('#list');
	var timer = null;
	var m = 0;
	//$ul.find('li').eq(0).clone().appendTo($ul);
	var $li = $ul.find('li');
	$ul.width($li.width()*$li.length);
	//获取屏幕可视宽度
	var cw = $(window).outerWidth(true);


	var imgs = $ul.find('img');
	//图片宽度
	var imgW = imgs.eq(0).outerWidth();
	//console.log('imgW===='+imgW);
	//图片居中
	function imgCenter(){
		if(cw<1920){
			$ul.find('img').css('left',-(imgW - cw)/2);
		}
	}
	//imgCenter();
	$(window).resize(function () {
       // imgCenter();
       // console.log('cw===='+cw);
    });
	loop();
	function loop() {
        timer = setInterval(function(){
            m++;
            if(m>=$li.length){
                //$ul.css('left',0);
                m=0;
            }
           /* if(m==$li.length-1){
                $ul.next().find('span').eq(0).attr('class','active').siblings().attr('class','');
			}else{*/
                $ul.next().find('span').eq(m).attr('class','active').siblings().attr('class','');
			//}
           // $ul.next().find('span').eq(m).attr('class','active').siblings().attr('class','');
            $ul.animate({'left':-$li.width()*m},'normal',function(){})
        },5000);
    }


	/*点击切换轮播图*/
	$ul.next().find('span').on('click',function () {

        //console.log($(this).index());
  		if(!$(this).hasClass('active')){
            clearInterval(timer);

            $ul.next().find('span').eq($(this).index()).attr('class','active').siblings().attr('class','');
            $ul.animate({'left':-$li.width()*$(this).index()},'normal',function(){})
            m = $(this).index();
            console.log('m====='+m);
            loop();
		}
    });

	/*a标签跳转*/
	//var scroll1 = $('.cooperation').offset().top -90;
	//var scroll2 = $('.about').offset().top - 90;
	var scroll3 = $('.case').offset().top - 90;
	var mUl = $('.top .wrap .right').find('ul');
	mUl.find('li').on('click',function(){
		//隐藏其他a标签的样式
		$(this).siblings().find('span').attr('class','');
		//显示当前的a标签的样式
		$(this).find('span').attr('class','active');
		//console.log($(this).text());
		if($(this).find('a').text() == '合作方式'){
			$('body,html').animate({scrollTop:scroll1},500);
		}else if($(this).find('a').text() == '关于我们'){
			$('body,html').animate({scrollTop:scroll2},500);
		}else if($(this).find('a').text() == '品牌案例'){
			$('body,html').animate({scrollTop:scroll3},500);
		}else{
			$('body,html').animate({scrollTop:0},500);
		}
		
	});

    /*滚动条监听*/
    $(window).scroll(function () {
		//console.log($(window).scrollTop());
		var scrollTop = $(window).scrollTop();
        var scrollLeft = $(window).scrollLeft();
        //console.log($(window).scrollLeft());

		if(scrollLeft >0){
            $('.top').css({'left':-scrollLeft});
		}
		if(scrollTop < scroll1){
            mUl.find('li').eq(0).siblings().find('span').attr('class','');
            mUl.find('li').eq(0).find('span').attr('class','active');
		}else if(scrollTop >= scroll1 && scrollTop < scroll2){
            mUl.find('li').eq(1).siblings().find('span').attr('class','');
            mUl.find('li').eq(1).find('span').attr('class','active');
		}else if(scrollTop >= scroll2 && scrollTop < scroll3){
            mUl.find('li').eq(2).siblings().find('span').attr('class','');
            mUl.find('li').eq(2).find('span').attr('class','active');
		}else{
            mUl.find('li').eq(3).siblings().find('span').attr('class','');
            mUl.find('li').eq(3).find('span').attr('class','active');
		}
    })


	/*解决ie8提示'console'未定义错误*/
    window.console = window.console || (function(){
        var c = {};
        c.log = c.warn = c.debug = c.info = c.error = c.time = c.dir = c.profile
            = c.clear = c.exception = c.trace = c.assert = function(){};
        return c;
    })();
})