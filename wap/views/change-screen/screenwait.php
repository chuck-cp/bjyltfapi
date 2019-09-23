<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>change设备安装反馈</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/reset.css" />
    <link rel="stylesheet" type="text/css" href="/static/css/wait_examine.css?v=181221"/>
</head>
<body class="yx_body">
<div class="sy_loadingpage" style="display: none;">
    <img src="/static/images/loading.gif">
    <p>正在加载...</p>
</div>
<div class="yx_az_xx">
    <span><img src="/static/images/azxxtj_cg.png"></span>
    <h5>安装信息提交成功</h5>
    <p class="yx_azcgts">您的安装信息已提交成功，请耐心等待<br/>此过程大约需:180秒</p>
    <div class="yx_sccg_time">
        <span class="yx_djs_time">180</span>
    </div>

</div>
<div class="yx_shehe_jg">
    <a href="javascript:;" class="yx_shenghe_cheng set-grey" id="shuaxin">刷新</a>
</div>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js" ></script>
<script>
    //安装信息提交成功倒计时
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
    var countdown=180;
    var jiange=1;
    var change = '<?=$change?>';
    function settime(){
        if (countdown == 0) {
            countdown = 180;
            $(".yx_djs_time").html(0);
            $('.yx_shenghe_cheng').removeClass('set-grey');
            $('.install-box .btn').css('background','#ed8102');
            //倒计时完成后可点击
            $('#shuaxin').click(function(){
                $.ajax({
                    url:baseApiUrl+"/screeninstall/screenstatus/<?=$shopid?>",
                    type:"get",
                    dataType:"json",
                    data:{token:'<?=$token?>'},
                    success:function(data) {
                        if(data.status){
                            if(data.data.status){
                                location.href="/screen/screenover?shopid=<?=$shopid?>&token=<?=$token?>&istrue=1";
                            }else{
                                if(change == 'change'){
                                    location.href="/screen/screenover?shopid=<?=$shopid?>&token=<?=$token?>&istrue=0&change=<?=$change?>&replace_id=<?=$replace_id?>";
                                }else{
                                    location.href="/screen/screenover?shopid=<?=$shopid?>&token=<?=$token?>&istrue=0";
                                }
                            }
                        }else{
                            location.href="/screen/screenshoplist?token=<?=$token?>";
                        }
                    }
                });
            })
            return false;
        } else {
            $(".yx_djs_time").html(countdown);
            countdown--;
            jiange=jiange+1;
            if(jiange>=6){
                jiange=1;
                $.ajax({
                    url:baseApiUrl+"/screeninstall/screenstatus/<?=$shopid?>",
                    type:"get",
                    dataType:"json",
                    data:{token:'<?=$token?>'},
                    success:function(data) {
                        if(data.status){
                            if (data.data.status) {
                                location.href = "/screen/screenover?shopid=<?=$shopid?>&token=<?=$token?>&istrue=1&";
                                return false;
                            }
                        }
                    }
                });
            }

        }
        setTimeout(function() {settime()},1000)
    }
    settime()
</script>
</body>
</html>
