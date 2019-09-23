<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>奖励金明细</title>
    <link rel="stylesheet" type="text/css" href="/static/css/mreset.css"/>
    <style type="text/css">
			.qianyue li{padding-top:16px;padding-bottom:16px;border-bottom:1px dashed #e6e6e6}
			.qianyue li:last-child{border-bottom:none}
			.qianyue b{display:inline-block;width:66%;vertical-align:middle;font-weight:normal;margin-right:2%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;margin-left:4%;font-size: 14px;}
			.qianyue span{display:inline-block;width:23%;vertical-align:middle;color:#fe7e09;border:1px solid #fe7e09;border-radius:10px;text-align:center;font-size:14px;padding-top:2px;padding-bottom:2px;margin-right:4%}
			.bonus_img{width:12%}
			.qianyue .fendian li{overflow:hidden;border-bottom:1px solid #e6e6e6;padding:8px 0}
			.qianyue .fendian span{font-size: 12px; float: right; width: auto; padding: 2px 6px;}
			.qianyue li{position:relative}
			.fendian{position:absolute;width:92%;background:#fff;z-index:1;padding:0 4%;left:0;display:none;    box-shadow: 0 6px 6px #e9e9e9;margin-top: 10px;}
			.fendian span{margin-right:0}
		.fendian i{overflow: hidden; text-overflow: ellipsis; white-space: nowrap; width: 70%; display: inline-block;font-size: 12px;}
		.qianyue .qianyue_kong{background: #fe7e09; color: #fff;}
		.fendian li:last-child{border-bottom: 0;}
    </style>
</head>
<body style="background: #f0f0f0;">
<img src="/static/images/bg.jpg" style="width: 100%;display: block;">
<div class="" style="position: absolute; top: 9%;    width: 100%;">
    <p style="text-align: center;"><span style="font-size: 3.3rem;color: #fff;vertical-align: sub;" id="price">0</span></p>
    <p style="text-align: center;color: #f0f0f0;">累计获得奖励金（元）</p>
    <div class="" style="width: 89%;margin: 0 auto;    margin-top: 6%;border-radius: 5px;background: #fff;    padding-bottom: 6%;">
        <p style="padding-top: 14px; padding-bottom: 14px; background: #323232;padding-left: 4%;border-radius: 5px 5px 0 0;"><span style="border-left: 4px solid #ff8207;color: #fff; padding-left: 3%;">推荐历史</span></p>
        <p style="text-align: center;margin-top: 60px; margin-bottom: 82px;display: none" id="noneData"><img src="/static/images/no_prize.png" style="width: 50%;" ></p>
        <ul class="qianyue" >
        </ul>
        <p id="no-more" style="color: #afafaf; text-align: center;margin-top: 6%; display: none;">没有更多店铺啦</p>
    </div>
</div>
<script src="/static/js/cookie.js" type="text/javascript" charset="utf-8"></script>
<script src="/static/js/jquery-1.7.2.min.js" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    $.ajax({
        url:baseApiUrl+'/activity/detail?activity_token='+getCookie('activity_token'),
        type:'get',
        async:true,
        success:function (data) {
            if (data.status == 200) {
                var data = data.data;
                $('#price').html(data.price);
                if (data.shop_list == '') {
                    $('#noneData').show();
                } else {
                    var shop_type = {0:'未签约',1:'已签约',2:'签约失败',3:'查看'};
                    $.each(data.shop_list,function (index,item) {
                        if (item.status == 3) {
                            var html = '';
                            html += "<li><b >"+item.shop_name+"</b><span class='qianyue_kong'>"+shop_type[item.status]+" <img class='bonus_img' src='/static/images/bonus_sin.png' ></span>";
                            html += "<ol class='fendian' state='false'>";
                            $.each(item.list,function (index,item) {
                                var shopStatus = '未签约';
                                if (item.status == 5) {
                                    shopStatus = '已签约';
                                }
                                html += "<li><i>" + item.branch_shop_name + "</i><span>"+ shopStatus +"</span></li>";
                            });
                            html += '</ol></li>';
                            $('.qianyue').append(html);
                        } else {
                            $('.qianyue').append("<li><b >"+item.shop_name+" </b><span>"+shop_type[item.status]+"</span></li>");
                        }
                    })
                }

            } else {
                alert(data.message);
            }
        },error:function (data) {
            alert("服务器错误");
        }
    });
    //滑动加载
    var page = 2;
    $(window).scroll(function(){
        var doc_height = $(document).height();
        var scroll_top = $(document).scrollTop();
        var window_height = $(window).height();
        if(scroll_top + window_height >= doc_height){
            if($('#no-more').css('display') == 'none'){
                $.ajax({
                    url:baseApiUrl+'/activity/detail?page='+page+'&activity_token='+getCookie('activity_token'),
                    type:'get',
                    async:true,
                    success:function (data) {
                        if (data.status == 200) {
                            var data = data.data;
                            $('#price').html(data.price);
                            if (data.shop_list == '') {
                                $('#no-more').show();
                            } else {
                                var shop_type = {0:'未签约',1:'已签约',2:'签约失败',3:'查看'};
                                $.each(data.shop_list,function (index,item) {
                                    if (item.status == 3) {
                                        var html = '';
                                        html += "<li><b>"+item.shop_name+"</b><span class='qianyue_kong'>"+shop_type[item.status]+" <img class='bonus_img' src='/static/images/bonus_sin.png' ></span>";
                                        html += "<ol class='fendian' state='false'>";
                                        $.each(item.list,function (index,item) {
                                            var shopStatus = '未签约';
                                            if (item.status == 5) {
                                                shopStatus = '已签约';
                                            }
                                            html += "<li><i>" + item.branch_shop_name + "</i><span>"+ shopStatus +"</span></li>";
                                        });
                                        html += '</ol></li>';
                                        $('.qianyue').append(html);

                                    } else {
                                        $('.qianyue').append("<li><b>"+item.shop_name+" </b><span>"+shop_type[item.status]+"</span></li>");
                                    }
                                })
                            }
                            page ++;
                        } else {
                            alert(data.message);
                        }
                    },error:function (data) {
                        alert("服务器错误");
                    }
                });
            }


        }
    });
	$('.qianyue .qianyue_kong').live('click',function () {
		var state_cur = $(this).parent().find('.fendian').attr('state');
		if ('false' == state_cur) {
			$(this).parent().find('.fendian').show();
			$(this).parent().find('.bonus_img').attr('src','/static/images/bonus_more.png');
			$(this).parent().siblings().find('.bonus_img').attr('src','/static/images/bonus_sin.png');
			$(this).parent().find('.fendian').attr('state','true');
			$(this).parent().siblings().find('.fendian').hide();
			$(this).parent().siblings().find('.fendian').attr('state','false');
		} else{
			$(this).parent().find('.fendian').hide();
			$(this).parent().find('.bonus_img').attr('src','/static/images/bonus_sin.png');
			$(this).parent().find('.fendian').attr('state','false');
		}
	})
</script>
</body>
</html>