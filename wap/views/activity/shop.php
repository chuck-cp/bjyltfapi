<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <title>填写店铺信息</title>
    <link rel="stylesheet" type="text/css" href="/static/css/mreset.css"/>
    <link rel="stylesheet" type="text/css" href="/static/css/reset.css?v=20180804">
    <script src="/static/js/jquery-1.7.2.min.js" type="text/javascript" charset="utf-8"></script>
    <script src="/static/js/cookie.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="/static/js/exif.js?v=1.2"></script>
    <script type="text/javascript" src="/static/js/cos-js-sdk-v4.js"></script>
    <script src="/static/js/img-upload.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="/static/js/jQuery.validate.min.js"></script>
    <style type="text/css">
        body{background: #f0f0f0;}
        .shop_info{margin-top: 4%;}
        .shop_info li{padding-top: 14px;padding-bottom: 14px;    margin-bottom: 1px;background: #fff;padding-left: 2%; padding-right: 2%;overflow: hidden;font-size: 14px;}
        .shop_info b{float: left;width:30% ;font-weight: normal;}
        .shop_info span{float: right;width: 70%;text-align: left;color: #999999;}
        .submission{position: fixed; background: #f86908; font-size: 16px; bottom: 0; color: #fff; width: 100%; text-align: center; padding-top: 16px; padding-bottom: 16px;border: 0;}
        .shop_info input{width: 100%;}
        /*选择地区弹层*/
        .express-area-box { position: absolute; bottom: -100%; left: 50%; z-index: 15; width: 320px; margin-left: -160px; background-color: #fff; color: #4d525d; display:none }
        .express-area-box header { position: relative; border-top: 1px solid #e2e2e2; border-bottom: 1px solid #e2e2e2; }
        .express-area-box header h3 { margin: 0 1.675rem; text-align: center; font-size: .8rem; line-height: 2.25rem; }
        .express-area-box header .back,
        .express-area-box header .close { position: absolute; top: 0; width: 1.675rem; height: 2.25rem; }
        .express-area-box header .back { display: none; left: 0; background: url(/static/image/back.png) no-repeat center;   background-size: .45rem .75rem; }
        .express-area-box header .close { right: 0; background: url(/static/image/close-area.png) no-repeat center; background-size: .675rem .675rem; }
        .express-area-box article { height: 210px; overflow-y: scroll; }
        .area-list li { padding: .5rem; border-bottom: 1px solid #e2e2e2; text-align: justify; font-size: .7rem; line-height: 1.25rem; }
        @media (min-width: 721px) and (max-width: 1300px) {
            .express-area-box { position: fixed; left: 0; width: 100%; margin-left: 0; }
        }
        @media (max-width: 720px) {
            .express-area-box { position: fixed; left: 0; width: 100%; margin-left: 0; }
        }
        .yx_srnr_dqu{font-style: normal; font-size: 13px;color: #b2b2b2;width: 100%; display: inline-block;}
        .upload_img_box{width: 19%;margin-top: 14px;margin-bottom: 14px;}
        .sy_bgzsytp{ position: absolute; left: 0; top: 0; width: 100%; height: 100%;}
        /* 上传 */
        .upload{ width: 19%;  box-sizing: border-box; position: relative;   margin: 3% 0;}
        .upload .imgspace{ width: 100%; height:auto; overflow: hidden;}
        .upload .addimg{ width: 100%; height: 100%; position: absolute; left: 0; top: 0;}
        .upload .Upload-imginput{width: 100%;height: 100%;position: absolute;left: 0;top: 0;opacity: 0; cursor: pointer;}
		/* 提示弹框 */
		.sy-installed-ts{top: 14%;}
		/* 苹果输入框 */
		.shop_info input{line-height: 1.4;border-radius:0 ;}
		/* 遮挡了上传功能 */
		.upload{width: 24%;}
		.sc_fail_ts{width: 126%; font-size: 12px;    top: -5%;}
    </style>
</head>
<body >
<form id="idform">
    <ul class="shop_info">
        <li><b>店铺名称</b><span><input autocomplete="off" type="text" name="shop_name" id="shop_name" value="" placeholder="填写店铺名称" nullmsg="shopname" datatype="*" /></span></li>
        <li><b>店铺联系人</b><span><input autocomplete="off" type="text" name="apply_name" id="apply_name" value="" placeholder="填写店铺联系人姓名" nullmsg="nameblank" datatype="*" /></span></li>
        <li><b>手机号码</b><span><input autocomplete="off" errormsg="errorphone" type="number" name="apply_mobile" id="apply_mobile" value="" placeholder="填写手机联系方式"  datatype="phone"  nullmsg="numbphone" /></span></li>
        <li>
            <b>安装地址</b>
            <span class="activity_install_area">
			<input type="hidden" id="area_id" placeholder="请选择地区" nullmsg="installarea" datatype="*" value="">
            <cite id="area_name" class="yx_srnr_dqu">请选择地区</cite>
        </span>
            <!--选择省市区地区弹层-->
            <section id="areaLayer" class="express-area-box">
                <header>
                    <h3>选择地区</h3>
                    <a id="backUp" class="back" href="javascript:void(0)" title="返回"></a>
                    <a id="closeArea" class="close" href="javascript:void(0)" title="关闭"></a>
                </header>
                <article id="areaBox">
                    <ul id="areaList" class="area-list"></ul>
                </article>
            </section>
        </li>
        <li><b></b><span><input autocomplete="off" type="text" name="address" id="address" value="" placeholder="填写详细地址" nullmsg="installaddress" datatype="*"/></span></li>
        <li><b>镜面数量</b><span style="color: #333;"><i style="color: #999999;"><input autocomplete="off" style="width: 90%;" errormsg="error_mirror_account" name="mirror_account" id="mirror_account" value="" placeholder="填写镜面数量"  nullmsg="mirrornum" datatype="*" type="number"/></i>&nbsp;面</span></li>
        <li>
            <p>店铺门脸照片</p>
            <p>
                <label>
                    <div class="upload" id="upload1">
                        <input id="shop_image" class="update_input" type="hidden" nullmsg="shopimage" datatype="*">
                        <img class="imgspace" src="/static/images/up_img.png">
                        <img class="addimg" src="/static/images/up_img.png">
                        <?if ($from == 'app'):?>
                            <p class="Upload-imginput" id="up2"></p>
                        <?else:?>
                            <input type="file" class="Upload-imginput" accept="image/*" id="up1">
                        <?endif;?>
                        <div class="progress-bar" step="0"></div>
                    </div>
                    </div>
                </label>
            </p>
        </li>
    </ul>
    <button class="submission" type="submit">确认提交</button>
</form>
<!--提交失败提示-->
<p class="sy-installed-ts">提交失败</p>
<script>
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
</script>
<script type="text/javascript" src="/static/js/jquery.area.js?v=1.6"></script>
<script type="text/javascript">
    var myFolder = '/member/activity/';
    //图片上传Upload-imginput
    $('.Upload-imginput').click(function(){
        if(getCookie('from') == 'app'){
            var ua = navigator.userAgent.toLowerCase();
            var result = {"action":"upload1"};
            if (/iphone|ipad|ipod/.test(ua)) {
                webkit.messageHandlers.ylcm.postMessage(JSON.stringify(result));
            }else if(/android/.test(ua)) {
                window.jsObj.HtmlcallJava(JSON.stringify(result));
            }
        }else{
            uploadimg('Upload-imginput');
        }
    });
    $("#area_name").click(function (){
        zxcs("activity_install_area");
    })
    //表单验证
    function tippanel(){
        $('.sy-installed-ts').show();
        setTimeout(function(){$('.sy-installed-ts').hide()},2000);
    }
    $("#idform").Validform({
        tipSweep:true,
        tiptype:function(msg,o,cssctl){
            if(msg=='shopname'){
                $('.sy-installed-ts').text('店铺名称不能为空');
                tippanel();
            };
            if(msg=='nameblank'){
                $('.sy-installed-ts').text('店铺联系人姓名不能为空');
                tippanel();
            };
            if(msg=='numbphone'){
                $('.sy-installed-ts').text('申请人手机号码不能为空');
                tippanel();
            };
            if(msg=='errorphone'){
                $('.sy-installed-ts').text('请输入正确的手机号');
                tippanel();
            };
            if(msg=='installarea'){
                $('.sy-installed-ts').text('请选择地区');
                tippanel();
            };
            if(msg=='installaddress'){
                $('.sy-installed-ts').text('详细地址不能为空');
                tippanel();
            };
            if(msg=='mirrornum'){
                $('.sy-installed-ts').text('镜面数量不能为空');
                tippanel();
            };
            if(msg=='error_mirror_account'){
                $('.sy-installed-ts').text('镜面数量不能大于10000');
                tippanel();
            };
            if(msg=='shopimage'){
                $('.sy-installed-ts').text('请上传店铺门脸照片');
                tippanel();
            };
        },
        beforeSubmit:function(curform){
            var postData = {
                'shop_name':$('#shop_name').val(),
                'apply_name':$('#apply_name').val(),
                'apply_mobile':$('#apply_mobile').val(),
                'area_id':$('#area_id').val(),
                'address':$('#address').val(),
                'mirror_account':$('#mirror_account').val(),
                'shop_image':$('#shop_image').val()
            };
            $.ajax({
                url:baseApiUrl+'/activity/shop?activity_token='+getCookie('activity_token'),
                type:'POST',
                data:postData,
                async:true,
                success:function (data) {
                    if (data.status == 200) {
                        $('.sy-installed-ts').text('添加成功');
                        tippanel();
                        setTimeout(function(){
                            window.location.href = '/activity';
                        },2000)
                    } else {
                        alert(data.message);
                    }
                },error:function (data) {
                    $('.sy-installed-ts').text('服务器错误');
                    tippanel();
                }
            });
            return false;
        },
        datatype:{
            "IDcard":/^[1-9]\d{7}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}$|^[0-9]\d{5}[1-9]\d{3}((0\d)|(1[0-2]))(([0|1|2]\d)|3[0-1])\d{3}([0-9]|X)$/i,
            "phone":/^1[34578]\d{9}$/,
            'j_cont':/^[1-9]$|^[1-9]\d$|^[1-9]\d{2}$|^[1-9]\d{3}$/
        }
    })
</script>
</body>
</html>