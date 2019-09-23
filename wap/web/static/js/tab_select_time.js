$(function(){	
	//获取当前时间
	var myDate = new Date();
	var xzYear=myDate.getFullYear();
	var xzMonth=myDate.getMonth()+1;
	var xzDay=myDate.getDate();
	//动态创建年份
	function createYears(){		
		$('#start .year-date').find('.swiper-wrapper').empty();		
		for ( var i=8; i<= 13; i++ ) {
			if(i<10){
			  $('#start .year-date').find('.swiper-wrapper').append( "<div class='swiper-slide' data-year='"+i+"'>"+'0'+i+"</div>" );
			}
			else{
			  $('#start .year-date').find('.swiper-wrapper').append( "<div class='swiper-slide' data-year='"+i+"'>"+i+"</div>" );
			}
	
		}
	  }
	//动态创建月份
	function createMouths(){
		$('.mouth-date').find('.swiper-wrapper').empty();
		for(var i=0; i<=50; i=i+10){
			if(i==0){
			  $('.mouth-date').find('.swiper-wrapper').append( "<div class='swiper-slide' data-month='"+i+"'>00</div>" );	
			}
			else{
			  $('.mouth-date').find('.swiper-wrapper').append( "<div class='swiper-slide' data-month='"+i+"'>"+i+"</div>" );				
			}
			
		}
	}createMouths();
	//设备关机时间
	
		//动态创建年份
		function endYears(){			
			$('#end .year-date').find('.swiper-wrapper').empty();
			for ( var i=16; i<= 23; i++ ) {
				if(i<16){
					$('#end .year-date').find('.swiper-wrapper').append( "<div class='swiper-slide' data-year='"+i+"'>"+'0'+i+"</div>" );
				}
				else{
					$('#end .year-date').find('.swiper-wrapper').append( "<div class='swiper-slide' data-year='"+i+"'>"+i+"</div>" );
				}
		
			}
			}

	//console.log(calendar.solarDays( 2017, 4))
	//时间选项 月
	var mySwiper = new Swiper('.mouth-date .swiper-container',{
		direction: 'vertical',
		slidesPerView: 3,
		centeredSlides: true,
		freeMode: true,
		freeModeFluid: true,
		freeModeSticky: true,
		grabCursor: true,
		initialSlide :1,
		observer:true,//修改swiper自己或子元素时，自动初始化swiper
		observeParents:true,//修改swiper的父元素时，自动初始化swiper
		onTransitionEnd: function() {
		      curYears = $('.year-date').find('.swiper-slide-active').attr('data-year');
			  curMonths = $('.mouth-date').find('.swiper-slide-active').attr('data-month');
			}
	})
	//时间选项 年
	var mySwiper = new Swiper('.year-date .swiper-container',{
		direction: 'vertical',
		slidesPerView: 3,
		centeredSlides: true,
		freeMode: true,
		freeModeFluid: true,
		freeModeSticky: true,
		grabCursor: true,
		initialSlide :1,
		observer:true,//修改swiper自己或子元素时，自动初始化swiper
		observeParents:true,//修改swiper的父元素时，自动初始化swiper
		onTransitionEnd: function() {
		      curYears = $('.year-date').find('.swiper-slide-active').attr('data-year');
			  curMonths = $('.mouth-date').find('.swiper-slide-active').attr('data-month');
			}
	})	
var open_min;// 开机时间（分钟）
var cur_list_index = null;//选中当前列表的索引值
//设备开机
$('.sy_selecttime').live('click',function(){
	cur_list_index = $(this).parents('.item_li').index();
	$('.riqi-select').attr('id','start');
	createYears();
		 $(".mask").css("opacity","0.3").show(); 
         var ymheight=$(document).height()+ "px";
         $(".mask").css("height",ymheight);
	     $(".riqi-select").animate({bottom:"0"});	     
	     scrollTop=($(window).scrollTop() || $("body").scrollTop());
	     $('body').addClass('modal-open');
         document.body.style.top = -scrollTop + 'px';
})
//开机限确定按钮
$('#start .select-confirm').live("click",function(){
    var rqyear=$('.year-date').find('.swiper-slide-active').text();
    var rqmonth=$('.mouth-date').find('.swiper-slide-active').text();
    //console.log(rqyear+'==>'+rqmonth)
	open_min = rqmonth;//开机分钟
	$('.item_li').eq(cur_list_index).find('.business-open').text(rqyear+':'+rqmonth);//多选
    // $('.business-open').text(rqyear+':'+rqmonth);//单选
    $('[name="screen_start_at"]').val(rqyear+':'+rqmonth)
    $('.business-open').css('color','#333');
    //当前时间
    var currenttime=parseInt(rqyear) + 10;
		var spilth_time = parseInt(rqyear*60) + parseInt(rqmonth);  //开机设定的总分钟数；
		//console.log(spilth_time)
		/*
		if (spilth_time>710) {
			//开机时间大于11.50(710分钟)，关机时间默认为23.50
			currenttime = 23;
			rqmonth = 50;
		}
		*/
    //关机时间
    if(currenttime>=24){
        currenttime=currenttime-24;
    }
    //console.log(currenttime +'==>'+ rqmonth)
	$('.item_li').eq(cur_list_index).find('.business-close').text(currenttime+':'+rqmonth);//多选
    // $('.business-close').text(currenttime+':'+rqmonth);//单选
    $('[name="screen_end_at"]').val(currenttime+':'+rqmonth);
    $('.business-close').css('color','#333');
    // $('.sy_setline').css('color','#333');
    $("#start").animate({bottom:"-999px"});
    $(".mask").hide();
    $('body').removeClass('modal-open');
    document.scrollingElement.scrollTop = scrollTop;
})						
//开机限取消按钮	
$('.riqi-select .select-cancel,.mask').click(function(){	
     $(".riqi-select").animate({bottom:"-999px"});
	 $(".mask").hide();
	 $('body').removeClass('modal-open');
     document.scrollingElement.scrollTop = scrollTop;
})
// 设备关机
$('.zhj_selecttime').live('click',function(){
	cur_list_index = $(this).parents('.item_li').index();
	$('.riqi-select').attr('id','end');
	endYears();
		 $(".mask").css("opacity","0.3").show(); 
         var ymheight=$(document).height()+ "px";
         $(".mask").css("height",ymheight);
	     $(".riqi-select").animate({bottom:"0"});	     
	     scrollTop=($(window).scrollTop() || $("body").scrollTop());
	     $('body').addClass('modal-open');
         document.body.style.top = -scrollTop + 'px';
})
//关机确定按钮
$('#end .select-confirm').live('click',function(){
    var rqyear=$('.year-date').find('.swiper-slide-active').text();//当前时间
    var rqmonth=$('.mouth-date').find('.swiper-slide-active').text();
		var open_time = $('.business-open').text();	//开机时间
		var cur_time = parseInt(rqyear) - parseInt(open_time); //时间差
		//默认时间差(开机) 
		var def_timed = open_time.substr(0,2);
		var def_timem = open_time.substr(3,2);
		var def_times = parseInt(def_timed)*60 + parseInt(def_timem);		
		var def_timex = def_times/60;
		var def_time_def = def_timex.toFixed(2);		
			// 选中的时间
			 var sel_time = parseInt(rqyear)*60 + parseInt(rqmonth);	
			var sel_timex = sel_time/60;
			var sel_time_sel = sel_timex.toFixed(2);			
			var sels_time = sel_time_sel - def_time_def;
			var sels_time_val = sels_time.toFixed(2);	
	//选中后时间差 	
   if (10 > sels_time_val) {
   	    err_time();
    } else {
    	$('.item_li').eq(cur_list_index).find('.business-close').text(rqyear+':'+rqmonth);
        $('[name="screen_end_at"]').val(rqyear+':'+rqmonth);
    	$('.business-close').css('color','#333');
    	// $('.sy_setline').css('color','#333');
    	$("#end").animate({bottom:"-999px"});
    	$(".mask").hide();
    	$('body').removeClass('modal-open');
    	document.scrollingElement.scrollTop = scrollTop;
    }
   
})
//开机限取消按钮	
$('.riqi-select .select-cancel,.mask').click(function(){	
     $(".riqi-select").animate({bottom:"-999px"});
	 $(".mask").hide();
	 $('body').removeClass('modal-open');
     document.scrollingElement.scrollTop = scrollTop;
})	

})
