<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<title>	监播报告	</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
		<link rel="stylesheet" href="/static/css/swiper.min.css">
		<link rel="stylesheet" href="/static/css/animate.min.css">
		<link rel="stylesheet" href="/static/css/com.css">
        <style>
            .inner{vertical-align: middle;float: none !important;}
        </style>
        <script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
		<script src="/static/js/report/swiper.min.js"></script>
		<script src="/static/js/report/swiper.animate.min.js"></script>
		<script src="/static/js/highcharts.js" type="text/javascript" charset="utf-8"></script>
	</head>
	<body>		
		<div class="swiper-container swiper-container-initialized swiper-container-vertical" id="swiper-container-v">
			<section class="poster_wrap load" id="load" style="display: none;">
				<div class="p_loading">
					<div class="p_loading_logo">
					</div>
					<p class="p_loading_tip">
						玉龙传媒监播报告
					</p>
				</div>
			</section>
			<div class="swiper-wrapper" id="swiper-wrappers" style="transform: translate3d(0px, 0px, 0px);">
				<!------------- 播放首页 slide1----------------->
				<section class="swiper-slide swiper-slide1 swiper-slide-v swiper-slide-active">
					<img class="imgs" src="/static/images/report/index.jpg" >
					<div class="report_bottom_box">
						<img class="imgs" src="/static/images/report/report_bottom1.png" >				
					</div>
				</section>
				<!------------- 播放基本信息 slide2----------------->
				<section class="swiper-slide swiper-slide2 swiper-slide-v swiper-slide-active">
					<img class="imgs" src="/static/images/report/report_top.jpg" >
					<div class="report_bottom_box">
						<img class="imgs" src="/static/images/report/report_bottom.png" >
						<ul class="essential_con">
							<li><em>广告订单编号</em><span></span></li>
							<li><em>业务对接人</em><span></span></li>
							<li><em>投放时间</em><span><i class="gold"></i> 至 <i class="gold"></i></span></li>
                            <li><em>广告位置</em><span><i class="gold"></i><span class="inner"></span></span></li>
                            <li><em>广告时长</em><span><i class="gold"></i> <span class="inner">秒</span></span></li>
                            <li><em>广告频次</em><span><i class="gold"></i> 频次/小时</span></li>
                            <li><em>广告投放地区</em><span></span></li>
						</ul>
					</div>
				</section>				
				<!------------- 概况1 slide----------------->
				<section class="swiper-slide swiper-slide3 swiper-slide-v swiper-slide-active">
					<img class="imgs" src="/static/images/report/play_survey.jpg" >
					<div class="report_bottom_box">
						<img class="imgs" src="/static/images/report/report_bottom.png" >
						<ul class="essential_con">
                            <li><em>您投放的广告总计应播放次数</em><span><i class="gold"></i> 次</span></li>
                            <li><em>实际播放广告次数</em><span><i class="gold"></i> 次</span></li>
                            <li><em>总播放时长</em><span><i class="gold"></i> 秒</span></li>
                            <li><em>到达率</em><span><i class="gold"></i> %</span></li>
                            <li><em>播放率高达</em><span><i class="gold"></i> %</span></li>
						</ul>
					</div>
				</section>
				<!--------------- 概况2 slide-------------->
				<section class="swiper-slide swiper-slide4 swiper-slide-v swiper-slide-next" style="height: 1008px;">
					<img class="imgs" src="/static/images/report/play_survey.jpg" >
					<div class="report_bottom_box">
						<img class="imgs" src="/static/images/report/report_bottom.png" >
						<div class="essential_con">
							<ul >
                                <li><em>直接观看人次</em><span><i class="gold"></i> 人次</span></li>
                                <li><em>每人次平均观看次数</em><span><i class="gold"></i> 次</span></li>
                                <li><em>不重复观看人数</em><span><i class="gold"></i> 人</span></li>
                                <li><em>平均每人观看次数</em><span><i class="gold"> </i> 次</span></li>
                                <li><em>广告总辐射人数</em><span><i class="gold"></i> 人次</span></li>
							</ul>
							<br>
							<p class="centers"><small class="beizhu">注：此页数据为理论值，实际情况数据会有所偏差。</small></p>
						</div>
						
					</div>
				</section>
				<!---------------- 概况3 slide-------------->
				<section class="swiper-slide swiper-slide5 swiper-slide-v" style="height: 1008px;">
					<img class="imgs" src="/static/images/report/play_survey.jpg" >
					<div class="report_bottom_box">
						<img class="imgs" src="/static/images/report/play_survey_bottom3.png" >
						<ul class="essential_con play_survey_tt">
                            <li><em>理发店总数</em><span><i class="gold"></i> 个</span></li>
                            <li><em>屏幕总数</em><span><i class="gold"></i> 块</span></li>
                            <li><em>辐射镜面数</em><span><i class="gold"></i> 块</span></li>
                            <li><em>屏幕平均在线时长</em><span><i class="gold"></i> 小时</span></li>
						</ul>
					</div>
				</section>
				<!---------------- 概况4 slide-------------->
				<section class="swiper-slide swiper-slide6 swiper-slide-v" style="height: 1008px;">
					<img class="imgs" src="/static/images/report/play_survey.jpg" >
					<div class="report_bottom_box">
						<img class="imgs" src="/static/images/report/play_survey_bottom4.png" >
						<ul class="essential_con play_survey_tt">
							<li><em><img class="ico_img" src="/static/images/report/gold_star.png" ><span></span></em><span><i class="gold"></i> 次</span></li>
							<li><em><img class="ico_img" src="/static/images/report/silver_star.png" ><span></span></em><span><i class="gold"></i> 次</span></li>
							<li><em><img class="ico_img" src="/static/images/report/cu_star.png" ><span></span></em><span><i class="gold"></i> 次</span></li>
						</ul>
					</div>
				</section>
				<!------------- 地区1 slide1----------------->
				<section class="swiper-slide swiper-slide7 swiper-slide-v swiper-slide-active">
					<img class="imgs" src="/static/images/report/play_area.jpg" >
					<div class="report_bottom_box">
						<img class="imgs" src="/static/images/report/report_bottom.png" >
						<div class="essential_con">
							<ul>
                                <li class="add_screen">本次播放地区共覆盖：</li>
                                <li><em>市</em><span><i class="gold"></i> 个</span></li>
                                <li><em>区</em><span><i class="gold"></i> 个</span></li>
                                <li><em>街道及乡镇</em><span><i class="gold"></i> 个</span></li>
							</ul>
							<br>
							<p class="centers"><img class="world_map" src="/static/images/report/world_map.png" ></p>
						</div>						
					</div>
				</section>
				<!--------------- 地区2 slide2-------------->
				<section class="swiper-slide swiper-slide8 swiper-slide-v swiper-slide-next" style="height: 1008px;">
					<img class="imgs" src="/static/images/report/play_area.jpg" >
					<div class="report_bottom_box">
						<img class="imgs" src="/static/images/report/play_area2.png" >
						<div class="essential_con">
							<div  id="container" style="min-width: 310px; height: 400px; max-width: 600px; margin: 0 auto"></div>
						</div>
					</div>
				</section>
				<!---------------- 地区3 slide3-------------->
				<section class="swiper-slide swiper-slide9 swiper-slide-v" style="height: 1008px;">
					<a id="survey_a">
                    <img class="imgs" src="/static/images/report/play_area.jpg" >
					<div class="report_bottom_box">
						<img class="imgs" id="survey" src="/static/images/report/play_area3.png" >
                        <ul class="essential_con play_survey_tt ss">
                            <li>
                                <em>
                                    <img class="ico_img" src="/static/images/report/gold_star.png">
                                </em>
                                <span><i class="gold"></i> 次</span>
                            </li>
                            <li style="visibility: hidden;"><em><img class="ico_img" src="/static/images/report/silver_star.png"></em><span><i class="gold"></i> 次</span></li>
                            <li style="visibility: hidden;"><em><img class="ico_img" src="/static/images/report/cu_star.png"></em><span><i class="gold"></i> 次</span></li>
					</div>
                    </a>
				</section>
				<!-- 新增屏幕 -->
				<section class="swiper-slide swiper-slide10 swiper-slide-v" id="swiper-slide10" style="height: 1008px;">
				<a class="add_screena" href="">
					<img class="imgs" src="/static/images/report/add_screen.jpg" >
					<div class="report_bottom_box">
						<img class="imgs"  src="/static/images/report/add_screen.png" >
						<div class="essential_con">
                            <ul >
                                <li class="add_screen">在您选择的地区和时间段内</li>
                                <li><em>新增理发店</em><span><i class="gold"></i> 家</span></li>
                                <li><em>新增屏幕</em><span><i class="gold"></i> 块</span></li>
                                <li><em>增加播放量</em><span><i class="gold"></i> 次</span></li>
                                <li><em>增加直接观看人数</em><span><i class="gold"></i> 人次</span></li>
                                <li><em>增加辐射观看人数</em><span><i class="gold"></i> 人次</span></li>
                            </ul>
							<br>
							<p class="centers"><small class="beizhu">新增屏幕及播放量为玉龙传媒额外赠送，不收取用户费用。</small></p>
						</div>	
						
					</div>	
					</a>
				</section>
			</div>
			<div class="arrow-box">
				<img src="/static/images/report/arrow.png" id="array">
			</div>
			<!-- <div class="swiper-pagination"></div> -->
			<div class="swiper-button-next"></div>
			
		</div>
        <script>
            var incrNum = <?=$incrNum?>;
        </script>
		<script src="/static/js/report/code.min.js?v=20190115"></script>
		<script type="text/javascript">
            function shu(jsdata) {
                Highcharts.chart('container', {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        backgroundColor: 'rgba(0,0,0,0)',
                        type: 'pie'
                    },
                    title: {
                        text: null
                    },
                    tooltip: {
                        formatter: function(){
                            return '<b>' + this.point.name + '</b><br>'+this.point.more+'次<br>' + this.point.y+'%';
                        },
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                // distance: 14,
                                formatter:function(){
                                    return '<b >' + this.point.name + '</b><br>'+this.point.more+'次<br>' + this.point.y+'%';
                                },
                                style: {
                                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                                }
                            }
                        }
                    },
                    colors: ['#f5ea6a', '#abc434', '#118eea', '#f5c23c','#43bbd3'
                    ],
                    credits: {
                        enabled: false
                    },
                    series: [{
                        name: 'Brands',
                        colorByPoint: true,
                        data: jsdata
                    }]
                });
            }

            var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
            $(function () {
                //获取数据
                $.ajax({
                    url:baseApiUrl+"/report/all",
                    type:"get",
                    dataType:"json",
                    data:{order_id:<?=$order_id?>,
                        token:'<?=$token?>',
                    },
                    success:function (phpdata) {
                        if(phpdata == null){
                            $('.sy-installed-ts').html('获取数据失败');
                            tippanel();
                            return;
                        }
                        if(phpdata.status == 200 && !$.isEmptyObject(phpdata.data)){
                            //base
                            $('.essential_con:first li:first span').html(phpdata.data.base.order_code);
                            $('.essential_con:first li:eq(1) span').html(phpdata.data.base.salesman_name);
                            $('.essential_con:first li:eq(2) i:first').html(phpdata.data.base.start_at);
                            $('.essential_con:first li:eq(2) i:last').html(phpdata.data.base.end_at);
                            if(phpdata.data.base.advert_name.length > 0){
                                var a = phpdata.data.base.advert_name.substring(0,1);
                                var b = phpdata.data.base.advert_name.substring(1);
                                $('.essential_con:first li:eq(3) i:first').html(a);
                                $('.essential_con:first li:eq(3) .inner').html(b);
                            }
                            if(phpdata.data.base.advert_time.length > 0){
                                num = phpdata.data.base.advert_time.replace(/[^0-9]/ig,'');
                                txt = phpdata.data.base.advert_time.match(/[\u4e00-\u9fa5]/g);
                                $('.essential_con:first li:eq(4) .gold').html(num);
                                $('.essential_con:first li:eq(4) .inner').html(txt.join(''));
                            }
                            $('.essential_con:first li:eq(5) .gold').html(phpdata.data.base.advert_rate);
                            $('.essential_con:first li:last span').html(phpdata.data.base.throw_area);
                            //survey
                            $('.essential_con:eq(1) li:first .gold').html(phpdata.data.base.total_order_play_number);
                            $('.essential_con:eq(1) li:eq(1) .gold').html(phpdata.data.base.total_play_number);
                            $('.essential_con:eq(1) li:eq(2) .gold').html(phpdata.data.base.total_play_time);
                            $('.essential_con:eq(1) li:eq(3) .gold').html(phpdata.data.base.total_arrival_rate);
                            $('.essential_con:eq(1) li:last .gold').html(phpdata.data.base.total_play_rate);
                            $('.essential_con:eq(2) li:first .gold').html(phpdata.data.base.total_watch_number);
                            $('.essential_con:eq(2) li:eq(1) .gold').html(phpdata.data.base.total_people_watch_number);
                            $('.essential_con:eq(2) li:eq(2) .gold').html(phpdata.data.base.total_no_repeat_watch_number);
                            $('.essential_con:eq(2) li:eq(3) .gold').html(phpdata.data.base.people_watch_number);
                            $('.essential_con:eq(2) li:last .gold').html(phpdata.data.base.total_radiation_number);
                            $('.essential_con:eq(3) li:first .gold').html(phpdata.data.base.throw_shop_number);
                            $('.essential_con:eq(3) li:eq(1) .gold').html(phpdata.data.base.throw_screen_number);
                            $('.essential_con:eq(3) li:eq(2) .gold').html(phpdata.data.base.throw_mirror_number);
                            $('.essential_con:eq(3) li:last .gold').html(phpdata.data.base.screen_run_time);
                            //日播放情况
                            $('.essential_con:eq(4) li:first em span').html(phpdata.data.rand[0].date);
                            $('.essential_con:eq(4) li:eq(0) .gold').html(phpdata.data.rand[0].throw_number);
                            if(phpdata.data.rand[1]){
                                $('.essential_con:eq(4) li:eq(1) em span').html(phpdata.data.rand[1].date);
                                $('.essential_con:eq(4) li:eq(1) .gold').html(phpdata.data.rand[1].throw_number);
                                $('.essential_con:eq(4) li:eq(1)').css('visibility','visible');
                            }else{
                                $('.essential_con:eq(4) li:eq(1)').remove();
                            }
                            if(phpdata.data.rand[2]){
                                $('.essential_con:eq(4) li:last em span').html(phpdata.data.rand[2].date);
                                $('.essential_con:eq(4) li:last .gold').html(phpdata.data.rand[2].throw_number);
                                $('.essential_con:eq(4) li:last').css('visibility','visible');
                            }else{
                                $('.essential_con:eq(4) li:last').remove();
                            }
                            //地区概况
                            $('.essential_con:eq(5) li:eq(1) .gold').html(phpdata.data.base.throw_city_number);
                            $('.essential_con:eq(5) li:eq(2) .gold').html(phpdata.data.base.throw_area_number);
                            $('.essential_con:eq(5) li:last .gold').html(phpdata.data.base.throw_street_number);
                            /*饼状图*/
                            var jsdata = [];
                            for (var i=0; i<phpdata.data.area.re.length; i++){
                                var ob = {};
                                ob.name = phpdata.data.area.re[i]['area'];
                                ob.y = phpdata.data.area.re[i]['rate'];
                                ob.more = phpdata.data.area.re[i]['throw_number'];
                                jsdata.push(ob);
                            }
                            shu(jsdata);
                            /*播放排名*/
                            $('.ss li:first').replaceWith('<li><em><img class="ico_img" src="/static/images/report/gold_star.png">'+phpdata.data.area.rank[0].area_name+'</em><span><i class="gold">'+phpdata.data.area.rank[0].throw_number+'</i> 次</span></li>');
                            if(phpdata.data.area.rank[1]){
                                $('.ss li:eq(1)').replaceWith('<li style="visibility: hidden;"><em><img class="ico_img" src="/static/images/report/silver_star.png">'+phpdata.data.area.rank[1].area_name+'</em><span><i class="gold">'+phpdata.data.area.rank[1].throw_number+'</i> 次</span></li>');
                                $('.ss li:eq(1)').css('visibility','visible');
                            }
                            if(phpdata.data.area.rank[2]){
                                $('.ss li:last').replaceWith('<li style="visibility: hidden;"><em><img class="ico_img" src="/static/images/report/silver_star.png">'+phpdata.data.area.rank[2].area_name+'</em><span><i class="gold">'+phpdata.data.area.rank[2].throw_number+'</i> 次</span></li>');
                                $('.ss li:last').css('visibility','visible');
                            }
                            $('.essential_con:eq(-2) li:first .gold').html(phpdata.data.area.rank[0].throw_number);
                            //最后
//                            if(!phpdata.data.base.give_shop_number){
                                $('.essential_con:last li:eq(1) .gold').html(phpdata.data.base.give_shop_number);
                                $('.essential_con:last li:eq(2) .gold').html(phpdata.data.base.give_screen_number);
                                $('.essential_con:last li:eq(3) .gold').html(phpdata.data.base.give_play_number);
                                $('.essential_con:last li:eq(4) .gold').html(phpdata.data.base.give_watch_number);
                                $('.essential_con:last li:last .gold').html(phpdata.data.base.give_radiation_number);
//                            }else{
//                                $('.swiper-slide:last').css({'display':'none','height':'0'});
//
//                            }

                        }else{
                            $('.sy-installed-ts').html('获取数据失败，请确认订单是否存在');
                            tippanel();
                        }
                    },error:function (phpdata) {
                        $('.sy-installed-ts').html('获取数据失败');
                        tippanel();
                    }
                })
            })
		</script>
	</body>

</html>
