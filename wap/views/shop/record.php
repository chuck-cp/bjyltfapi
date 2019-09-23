<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>申请记录</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <style>
        /*标签初始化*/
        /*标签初始化*/
        * {margin: 0;padding: 0 }
        table {border-collapse: collapse;border-spacing: 0}
        h1,h2,h3,h4,h5,h6 {font-size: 100%; font-weight: normal;}
        ul,ol,li {list-style: none}
        em,i {font-style: normal}
        img {border: 0;display:inline-block}
        input,img {vertical-align: middle; border:none; }
        a {color: #333;text-decoration: none;-webkit-tap-highlight-color:transparent;}
        input,button,textarea{-webkit-tap-highlight-color:transparent;outline: none;  }
        article, aside, details, figcaption, figure, footer, header, hgroup, menu, nav, section {display: block;}
        body{ background: #f0f0f0; -webkit-overflow-scrolling: touch; color: #333; font-size: 14px;min-width: 300PX;max-width: 640PX; margin: 0 auto; font-family: '微软雅黑';}
        /*内容*/
        .box-list{ background: #fff; margin-bottom: 5px;}
        .box-list .title{ padding: 6px 8px; overflow: hidden;border-bottom: 1px solid #dddddd;}
        .box-list .title .time{ float: left;}
        .box-list .title .status{ float: right; color: #3aa4d4;}
        .box-list .content{ padding: 5px 10px;}
        .box-list .content p{ line-height: 23px;}
        .box-list .content p span{ padding-left: 5px;}
        /*为空内容*/
        .box-list-wrapper .img{padding-top: 50%;text-align: center;}
        .box-list-wrapper .img img{ width: 23.2%; }
        .box-blank p{ text-align: center; padding-top: 10px; color: #2c2c2c;}
        .detail{display: block}
    </style>
</head>
<body>
<div class="box-list-wrapper">
</div>
<script type="text/javascript" src="/static/js/jquery.js" ></script>
<script>
    var page = 1;
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    var quan_wx_member_id=<?=$wx_member_id?>;
    var quan_member_id=<?=$member_id?>;
    $(function(){
        $.ajax({
            url:baseApiUrl+"/v1/member/record",
            type:"get",
            dataType:"json",
            data:{wx_member_id:quan_wx_member_id,member_id:quan_member_id,page:page},
            success:function(data) {
                if(data.status==200&&data.data!=null){
                    var  html="";
                    $.each(data.data,function(i,value){
                        statusa='';
                        switch(value.status)
                        {
                        //状态(0、申请待审核 1、申请未通过 2、待安装 3、安装待审核 4、安装未通过 5、已安装)',
                            case '0':
                                statusa='申请待审核';
                                break;
                            case '1':
                                statusa='申请未通过';
                                break;
                            case '2':
                                statusa='待安装';
                                break;
                            case '3':
                                statusa='安装待审核';
                                break;
                            case '4':
                                statusa='安装未通过';
                                break;
                            case '5':
                                statusa='已安装';
                                break;
                        }
                        if(value.status==1){
                            html=html+"<div class='box-list'><div class='title'><p class='time'>"+value.create_at+"</p><p class='status'>"+statusa+"</p></div><div class='content'><a href='/shop/modify-shop?shop_id="+value.id+"&wechat_id=<?=$wx_member_id?>&token=<?=$token?>&type=weixin' class='detail'> <p>申请人：<span>"+value.apply_name+"</span></p><p>安装地址：<span>"+value.area_name+value.address+"</span></p><p>安装数量：<span>"+value.apply_screen_number+"</span></p></a> </div></div>";
                        }else{
                            html=html+"<div class='box-list'><div class='title'><p class='time'>"+value.create_at+"</p><p class='status'>"+statusa+"</p></div><div class='content'><a href='/shop/recordinfo?shopid="+value.id+"' class='detail'> <p>申请人：<span>"+value.apply_name+"</span></p><p>安装地址：<span>"+value.area_name+value.address+"</span></p><p>安装数量：<span>"+value.apply_screen_number+"</span></p></a> </div></div>";
                        }
                    });
                    $(".box-list-wrapper").html(html);
                }
                else
                {
                    $(".box-list-wrapper").html("<div class='box-blank'><div class='img'><img src='/static/image/blank.png'></div><p>记录为空</p></div>");
                }
            }
        });
    });

    $(window).scroll(function(){
        var doc_height = $(document).height();
        var scroll_top = $(document).scrollTop();
        var window_height = $(window).height();
        //滑动加载更多
        if(scroll_top + window_height >= doc_height){
            page=page+1;
            $.ajax({
                url:baseApiUrl+"/v1/member/record",
                type:"get",
                dataType:"json",
                data:{wx_member_id:quan_wx_member_id,member_id:quan_member_id,page:page},
                success:function(data) {
                    if(data.status==200&&data.data!=null){
                        var  html="";
                        $.each(data.data,function(i,value){
                            statusa='';
                            switch(value.status)
                            {
                                case '0':
                                    statusa='申请待审核';
                                    break;
                                case '1':
                                    statusa='申请未通过';
                                    break;
                                case '2':
                                    statusa='待安装';
                                    break;
                                case '3':
                                    statusa='安装待审核';
                                    break;
                                case '4':
                                    statusa='安装未通过';
                                    break;
                                case '5':
                                    statusa='已安装';
                                    break;
                            }
                            if(value.status==1){
                                html=html+"<div class='box-list'><div class='title'><p class='time'>"+value.create_at+"</p><p class='status'>"+statusa+"</p></div><div class='content'><a href='/shop/modify-shop?shop_id="+value.id+"&wechat_id=<?=$wx_member_id?>&token=<?=$token?>&type=weixin' class='detail'> <p>申请人：<span>"+value.apply_name+"</span></p><p>安装地址：<span>"+value.area_name+value.address+"</span></p><p>安装数量：<span>"+value.apply_screen_number+"</span></p></a> </div></div>";
                            }else{
                                html=html+"<div class='box-list'><div class='title'><p class='time'>"+value.create_at+"</p><p class='status'>"+statusa+"</p></div><div class='content'><a href='/shop/recordinfo?shopid="+value.id+"' class='detail'> <p>申请人：<span>"+value.apply_name+"</span></p><p>安装地址：<span>"+value.area_name+value.address+"</span></p><p>安装数量：<span>"+value.apply_screen_number+"</span></p></a> </div></div>";
                            }

                        });
                        $(".box-list-wrapper").append(html);
                    }
                }
            });
        }
    });
</script>
</body>
</html>
