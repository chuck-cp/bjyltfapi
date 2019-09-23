<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>视频上传-通用页面</title>
    <link rel="stylesheet" type="text/css" href="/static/css/upload.css">
    <link href="/static/css/tcplayer.css " rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="/static/images/icon.ico" />
</head>
<body>
<div class="head">
    <div class="wrap">
        <div class="yx_logo">
            <img src="/static/images/logo_name.png">
        </div>
    </div>
</div>
<div class="wrap">
    <!--左侧菜单-->
    <div class="yx_left_menu fl">
        <!--用记信息-->
        <div class="yx_exit">
            <p class="yx_tcdl"><a href="/index/logout">退出登录</a></p>
            <p class="yx_photo"><img src="<?=\Yii::$app->user->identity->avatar;?>"></p>
            <p class="yx_name"><a href="javascript:;"><?=\Yii::$app->user->identity->name;?></a></p>
        </div>
        <!--菜单内容-->
        <ul class="yx_menu_nr">
            <li class="yx_gaoliang"><span><img src="/static/images/tfgl.png"></span><a href="/delivery/index">投放素材</a></li>
            <li><span><img src="/static/images/cqgl.png"></span><a href="/property/index">产权管理</a></li>
            <li><span><img src="/static/images/lsgl.png"></span><a href="/history/delivery">历史管理</a></li>
        </ul>
    </div>
    <!--右侧菜单-->
    <div class="yx_right_menu fr">
        <!--当前状态-->
        <div class="sy_curr_status">
            <p class="status">
                <strong>当前状态</strong>
                <?php switch($data['examine_status']){
                    case 0:
                        echo '<span class="wsz">未设置</span>';
                        break;
                    case 1:
                        echo '<span class="dss">审核中</span>';
                        break;
                    case 2:
                        echo '<span class="bbh">被驳回</span>';
                        break;
                    case 3:
                        echo '<span class="dtt">待投放</span>';
                        break;
                    case 4:
                        echo '<span class="tfz">投放中</span>';
                        break;
                }?>
            </p>
            <?php if($data['examine_status']==2){
                echo ' <p class="reson">'.$data['examine_desc'].'</p>';
            }?>
        </div>
        <!--购买信息-->
        <div class="yx_gmxx fl">
            <p>购买信息</p>
            <dl>
                <dt>订单号：</dt>
                <dd><?php echo $data['order_code'];?></dd>
            </dl>
            <dl>
                <dt>购买地区：</dt>
                <dd class="multiline-spillover">
                    <?php if($data['deal_price']==0){echo "无购买地区";}else{?>
                        <a href="/delivery/throwarea?orderid=<?php echo $data['id'];?>" target="_blank">查看购买区域</a>
                    <?php }?>
                </dd>
            </dl>
            <dl>
                <dt>购买广告类型：</dt>
                <dd><span><?php echo $data['advert_name'];?></span> <span id="advert_time"><?php echo $data['advert_time'];?></span></dd>
            </dl>
            <dl>
                <dt>投放日期：</dt>
                <dd>起<?php echo $data['start_at'];?> 止<?php echo $data['end_at'];?></dd>
            </dl>
            <dl>
                <dt>播放频次：</dt>
                <dd><?php echo $data['rate'];?>次/天</dd>
            </dl>
        </div>
        <input type="hidden" id="orderid" value="<?php echo $data['id'];?>">
        <input type="hidden" id="resourcetype" value="<?php echo $data['type'];?>">
        <input type="hidden" id="videoSize" value="<?php echo $data['resource_attribute']['size']?>">
        <!--<input type="hidden" id="videoSha1" value="<?php /*echo $data['resource_attribute']['sha1Sum']*/?>">-->
        <input type="hidden" id="myFolder" value="<?php echo "/member/".$data['member_id']."/";?>">
        <input type="hidden" id="videofileId" value="<?php echo $data['video_id']?>">
        <input type="hidden" id="videoName" value="<?php echo $data['resource_attribute']['name']?>">
        <input type="hidden" id="videoUrl" value="<?php echo $data['resource']?>">
        <input type="hidden" id="videoimg" value="<?php echo $data['resource_thumbnail']?>">
        <input type="hidden" id="examine_status" value="<?php echo $data['examine_status']?>">
        <input type="hidden" id="start_at" value="<?php echo $data['start_at']?>">
        <input type="hidden" id="advert_time" value="<?php echo $data['advert_time']?>">
        <input type="hidden" id="duration" value="<?php echo $data['resource_duration']?>">
        <input type="hidden" id="lock" value="<?php echo $data['lock']?>">
        <input type="hidden" id="advert_key" value="<?php echo $data['advert_key']?>">
        <input type="hidden" id="video_trans_url" value="<?php echo $data['video_trans_url']?>">
        <?php if($data['type']==1){?>
            <!--上传视频-->
            <div class="yx_scsp fl" >
                <p class="yx_scsp_bt">上传视频</p>
                <!-- 等待加载  -->
                <div class="yx_loading">
                    <p>
                        <span><img  src="/static/images/loading.gif"></span>
                        <span>视频转码中...</span>
                    </p>
                </div>
                <!-- 等待加载 end -->
                <!--点击上传  style="display: none"-->
                <div class="yx_djsc"  <?php if($data['examine_status']>0){echo "style=\"display: none\"";}?>>
                    <img class="csh_img" src="/static/images/scsp.png">
                    <!--上传-->
                    <form id="form1" class="sp_sc_an">

                        <input id="uploadVideoNow-file" type="file" />
                        <video style="display:none;" controls="controls" id="aa" oncanplaythrough="myFunction(this)"></video>
                    </form>
                    <div class="">
                        <a id="uploadVideoNow" href="javascript:void(0);" class="djscsp">直接上传视频</a>
                        <div class="row" id="crspslt" style="display:none">
                            <div id="video" class="w50" style="display:none"></div>
                            <div id="imgSmallView" class="w50" style="display:none"></div>
                        </div>
                    </div>

                    <div class="row" id="resultBox">
                        <!--上传 弹框-->
                        <div class="yx_scjdxq">
                            <!-- 标题 -->
                            <div class="yx_wjsc_bt">
                                <p class="fl">文件上传</p>
                                <p class="fr">
                                    <!--  <span id="abc"><img src="images/zuixiao.png"></span> -->
                                    <span><img src="/static/images/guanbi.png"></span>
                                </p>
                            </div>
                            <!-- 进度条部分 -->
                            <div class="shangcjdt" id="jdtxq">
                                <p class="wjsc_bt">文件上传中</p>
                                <div class="jdt_xqing">
                                    <div class="jdtiao">
                                        <cite class="jindguzhi">0%</cite>
                                        <p class="jdt_bj"></p>
                                        <p class="jdt_bfb"></p>
                                    </div>
                                    <div class="wjxianqing">
                                        <p class="fl">文件大小：<span id="sp_size">00</span>KB</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="yx_spgssm">A屏内容广告格式为MP4  1560*810  100M以内<br>A屏视频广告格式为MP4  1560*810  60M以内                </p>
                </div>
                <!--视频 播放  style="display:block"-->
                <div class="shipingbofang" <?php if($data['examine_status']>0){echo "style=\"display: block\"";}?>>
                    <p class="bdspingmc">
                        <?php if($data['examine_status']<3){?>
                        <input id="videoNameB" type="text" value="<?php echo $data['resource_name']?>"> </p><?php }else{?>
                        <?php echo $data['resource_name'];?>
                    <?php }?>
                    <div class="shiping">
                        <?php if($data['examine_status']<3){?>
                            <cite class="del_sp"><img src="/static/images/sc_sp.png"></cite>
                        <?php }?>
                        <div id="playerdiv">
                            <video id="player-container-id" preload="auto"  src="<?php echo $data['video_trans_url']?>"  class="shiping_zs" width="100%" height="100%" playsinline webkit-playinline x5-playinline></video>
                        </div>
                    </div>
                </div>
                            <div class="yx_banqsm">
                                <span><a href="/delivery/standard" target="_blank">查看素材上传规范</a></span>
                                <!--<span><a href="/property/banquan" target="_blank">版权声明</a></span>
                                <span><a href="/property/complaint" target="_blank">广告驳回声明</a></span>-->
                            </div>
            </div>
            <!--上传视频结束-->
        <?php }else{?>
            <!--上传图片开始--->
            <div class="yx_scsp fl">
                <p class="yx_scsp_bt">上传图片</p>
                <!--上传图片存放位置 图片上传成功之后 name和url会 显示到bdspingmc  和 del_sp中 提交表单后验证是否为空即可-->

                <div class="sy_upimg_position" <?php if($data['examine_status']>0){echo "style=\"display: block\"";}?>>
                    <p class="bdspingmc">
                        <?php if($data['examine_status']<3){?>
                        <input id="videoNameB" type="text" value="<?php echo $data['resource_name']?>"> </p><?php }else{?>
                        <?php echo $data['resource_name'];?>
                    <?php }?>
                    </p>
                    <p class="img-p">
                        <?php if($data['examine_status']<3){?>
                            <cite class="del_upimg"><img src="/static/images/sc_sp.png"></cite>
                        <?php }?>

                        <img class="img-posit" src="<?php echo $data['resource'];?>">
                    </p>
                </div>
                <div class="yx_djsc" <?php if($data['examine_status']>0){echo "style=\"display: none\"";}?>>
                    <img class="csh_img" src="/static/images/scsp.png">
                    <!--上传-->
                    <form id="form1" class="sp_sc_an">
                        <input class="Upload-imginput" data-type="type1" type="file" />
                    </form>
                    <div class="">
                        <a id="uploadVideoNow" href="javascript:void(0);" class="djscsp">上传图片</a>
                    </div>
                    <div class="row" id="resultBox">
                        <!--上传 弹框-->
                        <div class="yx_scjdxq">
                            <!-- 标题 -->
                            <div class="yx_wjsc_bt">
                                <p class="fl">文件上传</p>
                                <p class="fr">
                                    <!--  <span id="abc"><img src="images/zuixiao.png"></span> -->
                                    <span><img src="/static/images/guanbi.png"></span>
                                </p>
                            </div>

                            <!-- 进度条部分 -->
                            <div class="shangcjdt" id="jdtxq">
                                <p class="wjsc_bt">文件上传中</p>
                                <div class="jdt_xqing">
                                    <div class="jdtiao">
                                        <cite class="jindguzhi">0%</cite>
                                        <p class="jdt_bj"></p>
                                        <p class="jdt_bfb"></p>
                                    </div>
                                    <div class="wjxianqing">
                                        <p class="fl">文件大小：15M</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <p class="yx_spgssm">B屏图片格式为JPG、JPEG、PNG   360*1080  5M以内</p>
                </div>
                <div class="yx_banqsm">
                    <span><a href="/delivery/standard" target="_blank">查看素材上传规范</a></span>
                    <!--<span><a href="/property/banquan" target="_blank">版权声明</a></span>
                    <span><a href="/property/complaint" target="_blank">广告驳回声明</a></span>-->
                </div>
            </div>
            <!--上传图片结束--->
        <?php }?>
        <p class="blank10"></p>
        <!--知识产权上传-->
        <div>
            <div class="yx_zscqsc clearfix">
                <p class="yx_scsp_bt">知识产权上传</p>

                <div class="cqtplb" id="sy_appendimg">
                    <ul>
                        <?php
                        if(!empty($data['copyright'])){
                            foreach($data['copyright'] as $k=>$v){?>
                                <li class="cqslt">
                                    <input type="hidden" value='<?php echo "{\"id\":\"".$v['id']."\",\"name\":\"".$v['name']."\",\"image_url\":\"".$v['image_url']."\"}";?>'>
                                    <p class="cqslt_img">
                                        <img src="<?php echo $v['image_url']."?imageView2/0/w/200/h/130";?>">
                                        <?php if($data['examine_status']<3){?>
                                            <span class="del_img">删除</span>
                                        <?php }?>
                                    </p>
                                    <span class="cqslt_nmae"><?php echo $v['name'];?></span>
                                </li>
                            <?php   }}?>
                        <?php if($data['examine_status']<3){?>
                            <li class="cqslt" id='sy_add_upimg'>
                                <p class="djtjcq">
                                    <img src="/static/images/dj_tjcq.png">
                                </p>
                            </li>
                        <?php }?>
                    </ul>
                </div>
            </div>
            <div class="yx_tjcqzl clearfix">
                <?php if($data['examine_status']<3){?>
                    <button type="button" class="yx_tjcq">提交</button>
                    <button type="button" id="goback" class="yx_qxcq_cliked">返回</button>
                <?php }else{?>
                    <button type="button" id="gobackB" class="btn_goback">返回</button>
                <?php }?>
            </div>
        </div>
    </div>

    <!-- 提示框 -->
    <div class="yx_tctskuan">
        <!-- 标题 -->
        <div class="yx_tctskuan_bt">
            <p class="fl" id="tsk_namet">提示</p>
            <p class="fr">
                <!--  <span id="abc"><img src="images/zuixiao.png"></span> -->
                <span id="tctskuan"><img src="/static/images/guanbi.png"></span>
            </p>
        </div>
        <div class="scttsk_nr"></div>
    </div>
    <!--上传图片弹框-->
    <div class="sy_upimg_panel">
        <div class="title">
            <p class="fl">产权库</p>
            <p class="fr">
                <span><img src="/static/images/guanbi.png"></span>
            </p>
        </div>
        <div class="sy_upimg_con">
            <div class="sy_up_input"><p class="sy_upload-p"><input type="file" class="Upload-imginput" data-type="type2">+添加产权证明</p></div>
            <div class="sy_upimg_list clearfix">
                <ul class="sc_copyright_img">


                </ul>
            </div>
        </div>
    </div>
    <div class="sy_tctskuan">
        <!-- 标题 -->
        <div class="sy_tctskuan_bt">
            <p class="fl" id="tsk_namet">提示</p>
        </div>
        <div class="sy_scttsk_nr" style="display: block;">
            <p>确定要取消吗</p>
            <p class="sy_tctskuan_an">
                <button type="button"  class="sy_tjcq">确定</button>
                <button type="button" class="sy_qxcq">取消</button>
            </p>
        </div>
    </div>
    <!--新添加弹框-->
    <div class="syy_tctskuan">
        <!-- 标题 -->
        <div class="syy_tctskuan_bt">
            <p class="fl" id="tsk_namet">提示</p>
        </div>
        <div class="syy_scttsk_nr">
            <p>您的订单已超过期限，不能设置素材，请联系客服！</p>
            <p class="syy_tctskuan_an">
                <button type="button" >确定</button>
            </p>
        </div>
    </div>
    <!--其它-->
    <script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
    <script type="text/javascript" src="/static/js/sha1.js"></script>
    <script src="/static/js/sc_js.js"></script>
    <script src="/static/js/ugcUploader.js"></script>

    <!--上传图片-->
    <script type="text/javascript" src="/static/js/cos-js-sdk-v4.js"></script>
    <script type="text/javascript" src="/static/js/slt-upload.js"></script>
    <script type="text/javascript" src="/static/js/pcimg-upload.js"></script>
    <!--视频播放-->
    <!-- 如需在IE8、9浏览器中初始化播放器，浏览器需支持Flash并在页面中引入 -->
    <!--[if lt IE 9]>
    <script src="/static/js/videojs-ie8.js "></script>
    <![endif]-->
    <!-- 如果需要在 Chrome Firefox 等现代浏览器中通过H5播放hls，需要引入 hls.js -->
    <script src="/static/js/hls.min.0.8.8.js"></script>
    <!-- 引入播放器 js 文件 -->
    <script src="/static/js/tcplayer.min.js"></script>

    <script type="text/javascript">
        /**
         * 检测订单是否超出上传时间
         **/
        var lock=$('#lock').val();
        var examine_status=$('#examine_status').val();
        if(lock==1&&examine_status<3){//订单是否被锁定，锁定后不允许上传素材
            $(".syy_tctskuan").show();
        }
        $(".syy_tctskuan_an button").click(function(){
            location.href="/delivery/index";
        });
        var index = 0;
        var cosBox = [];
        var fileaddress="";
        var sp_name=""
        var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
        var timelog=0;
        /**
         * 计算签名
         **/
        var getSignature = function(callback){
            $.ajax({
                url: baseApiUrl+'/system/sign-video',
                type: 'get',
                dataType: 'json',
                success: function(res){
                    if(res.token) {
                        callback(res.token);
                    } else {
                        return '获取签名失败';
                    }
                }
            });
        };

        /**
         * 添加上传信息模块
         */

        var addUploaderMsgBox = function(type){
            var html = '<div class="uploaderMsgBox ycspnr" name="box'+index+'">';
            if(!type || type == 'hasVideo') {
                html +='<span name="videoname'+index+'"></span>' +
                    /*'计算sha进度：<span name="videosha'+index+'">0%</span>；' +
                     '上传进度：<span name="videocurr'+index+'">0%</span>；' +
                     'fileId：<span name="videofileId'+index+'">   </span>；' +
                     '上传结果：<span name="videoresult'+index+'">   </span>；<br>' +
                     '地址：<span name="videourl'+index+'">   </span>；'+*/
                    '<a href="javascript:void(0);" name="cancel'+index+'" cosnum='+index+' act="cancel-upload" class="quxiaosc"></a><br>';
            }

            if(!type || type == 'hasCover') {
                html += '封面名称：<span name="covername'+index+'"></span>；' +
                    '计算sha进度：<span name="coversha'+index+'">0%</span>；' +
                    '上传进度：<span name="covercurr'+index+'">0%</span>；' +
                    '上传结果：<span name="coverresult'+index+'">   </span>；<br>' +
                    '地址：<span name="coverurl'+index+'">   </span>；<br>' +
                    '</div>'
            }
            html += '</div>';
            $('#resultBox').append(html);
            return index++;
        };

        /**
         * 示例1：直接上传视频
         **/
        $('#uploadVideoNow-file').on('change', function (e) {
//            //获取视频时长
//            var video = this.files[0];
//            var url = URL.createObjectURL(video);
//            document.getElementById("aa").src = url;
//            timelog=298;
//            var advert_time=$("#advert_time").val();
//            alert(advert_time);
//            if(advert_time.indexOf("m") > 0){
//                advert_time=advert_time.substring(0,advert_time.length-1);
//                advert_time=advert_time*60;
//            }else{
//                advert_time=advert_time.substring(0,advert_time.length-1);
//            }
//            if(timelog<advert_time-1||timelog>advert_time+1)
//            {
//                $(".yx_tctskuan").show();
//                $(".yx_tctskuan").find("#tsk_namet").html("上传错误提示");
//                $(".yx_tctskuan").find(".scttsk_nr").html("上传的视频素材时长与购买广告时长不一致，请上传"+advert_time+"秒时长广告素材。");
//                $(".yx_tctskuan").find(".yx_tctskuan_an").hide();
//                return false;
//               // alert(")
//            }
////            var duration=$("#duration").val();
////            alert(duration);
////            alert(timelog);
//           // alert(advert_time)
//            return;
            //获取视频时长end

//            var files = this.files,
//                video = $('#video').find('video'),
//                videoURL = null,
//                windowURL = window.URL || window.webkitURL;;
//            if (files && files[0]) {
//                videoURL = windowURL.createObjectURL(files[0]);
//                $('#video').html('<video src="' + videoURL + '" controls="controls"></video>');
//                setTimeout(function() {
//                    createIMG();
//                }, 500);
//            }
//
//            var createIMG = function() {
//                video = $('#video').find('video')[0],
//                canvas = document.createElement("canvas"),
//                canvasFill = canvas.getContext('2d');
//                canvas.width = video.videoWidth ;
//                canvas.height = video.videoHeight ;
//                canvasFill.drawImage(video, 0, 0, canvas.width, canvas.height);
//                 src = canvas.toDataURL("image/jpeg");
//                $('#imgSmallView').html('<img id="imgSmallViewB" src="' + src + '" alt="预览图" />');
//            }


            var num = addUploaderMsgBox('hasVideo');
            var videoFile = this.files[0];
            fileaddress=videoFile;
            //文件大小
            var videosize=parseInt((videoFile.size/1024).toFixed(2));
            /*sha1File(videoFile);*/
            $('#videoSize').val(videoFile.size);
            //文件格式
            var gspd=videoFile.name;
            var filename;
            if(gspd.indexOf(".")>0)//从最后一个"."号+1的位置开始截取字符串
            {
                filename=gspd.substring(gspd.lastIndexOf(".")+1,gspd.length);
            }
            var file_gs=filename.toString().toLowerCase();

            if(file_gs!="mp4"){
                $(".yx_tctskuan").show();
                $(".yx_tctskuan").find("#tsk_namet").html("上传错误提示");
                $(".yx_tctskuan").find(".scttsk_nr").html("视频内容仅支持MP4格式");
                $(".yx_tctskuan").find(".yx_tctskuan_an").hide();
                return false;
            }else{

            }
            var advert_key=$('#advert_key').val();
            advert_time=<?php echo $data['advert_time2']?>;
            if(advert_key=='A1'){
                if(advert_time==60){
                    if(videosize>51200){
                        var adopt=false;
                    }else{
                        var adopt=true;
                    }
                }else if(advert_time==120){
                    if(videosize>61440){
                        var adopt=false;
                    }else{
                        var adopt=true;
                    }
                }else if(advert_time==150){
                    if(videosize>66560){
                        var adopt=false;
                    }else{
                        var adopt=true;
                    }
                }else if(advert_time==180){
                    if(videosize>71680){
                        var adopt=false;
                    }else{
                        var adopt=true;
                    }
                }else if(advert_time==240){
                    if(videosize>87040){
                        var adopt=false;
                    }else{
                        var adopt=true;
                    }
                }else if(advert_time==300){
                    if(videosize>102400){
                        var adopt=false;
                    }else{
                        var adopt=true;
                    }
                }
                var advert_size=100000;
            }
            if(advert_key=='A2'){
                if(advert_time==5){
                    if(videosize>7168){
                        var adopt=false;
                    }else{
                        var adopt=true;
                    }
                }else if(advert_time==10){
                    if(videosize>15360){
                        var adopt=false;
                    }else{
                        var adopt=true;
                    }
                }else if(advert_time==15){
                    if(videosize>20480){
                        var adopt=false;
                    }else{
                        var adopt=true;
                    }
                }else if(advert_time==20){
                    if(videosize>25600){
                        var adopt=false;
                    }else{
                        var adopt=true;
                    }
                }else if(advert_time==30){
                    if(videosize>35840){
                        var adopt=false;
                    }else{
                        var adopt=true;
                    }
                }else if(advert_time==40){
                    if(videosize>40960){
                        var adopt=false;
                    }else{
                        var adopt=true;
                    }
                }else if(advert_time==50){
                    if(videosize>46080){
                        var adopt=false;
                    }else{
                        var adopt=true;
                    }
                }else if(advert_time==60){
                    if(videosize>51200){
                        var adopt=false;
                    }else{
                        var adopt=true;
                    }
                }
            }
            //视频大小上传时显示
            $("#sp_size").html(videosize);
            //视频大小格式判断
            if(adopt==false){
                advert_size=advert_size/1000;
                $(".yx_tctskuan").show();
                $(".yx_tctskuan").find("#tsk_namet").html("上传错误提示");
                $(".yx_tctskuan").find(".scttsk_nr").html("素材过大请确认后重新上传");
                $(".yx_tctskuan").find(".yx_tctskuan_an").hide();
                return false;
            }

            //去除回车
            $('#result').append(videoFile.name +　'\n');
            $('#resultBox').show();
            var resultMsg = qcVideo.ugcUploader.start({
                videoFile: videoFile,
                getSignature: getSignature,
                allowAudio: 1,
                success: function(result){
                    if(result.type == 'video') {
                        $('[name=videoresult'+num+']').text('上传成功');
                        $('[name=cancel'+num+']').remove();
                        cosBox[num] = null;
                    } else if (result.type == 'cover') {
                        $('[name=coverresult'+num+']').text('上传成功');
                    }
                    // $(".shipingbofang").show();
                    $('.yx_loading').show();
                    $(".yx_djsc").hide();
                    $('#resultBox').hide();
                    $(".bdspingmc").html("<input id=\"videoNameB\" type=\"text\" value=\""+sp_name+"\">")

                    //进度条初始化
                    /*进度条的位置及数字*/
                    $(".jindguzhi").html('0%');
                    /*进度条的百分比*/
                    $(".jdt_bfb").css("width","0px")
                    $(".jindguzhi").css("left","0px")

                },
                error: function(result){
                    if(result.type == 'video') {
                        $('[name=videoresult'+num+']').text('上传失败>>'+result.msg);
                    } else if (result.type == 'cover') {
                        $('[name=coverresult'+num+']').text('上传失败>>'+result.msg);
                    }
                },
                progress: function(result){
                    if(result.type == 'video') {
                        //视频名称
                        sp_name=result.name
                        /*$('[name=videoname'+num+']').text(result.name);
                         $('[name=videosha'+num+']').text(Math.floor(result.shacurr*100)+'%');
                         $('[name=videocurr'+num+']').text(Math.floor(result.curr*100)+'%');*/
                        $('[name=cancel'+num+']').attr('taskId', result.taskId);
                        /*进度条的位置及数字*/
                        $(".jindguzhi").html(Math.floor(result.shacurr*100)+'%');
                        /*进度条的百分比*/
                        var jd_banfenbi=Math.floor(result.shacurr*100);
                        var jdt_bfb=480*(jd_banfenbi/100);
                        $(".jdt_bfb").css("width",jdt_bfb+"px")
                        $(".jindguzhi").css("left",jdt_bfb-30+"px")
                        cosBox[num] = result.cos;
                    } else if (result.type == 'cover') {
                        $('[name=covername'+num+']').text(result.name);
                        $('[name=coversha'+num+']').text(Math.floor(result.shacurr*100)+'%');
                        $('[name=covercurr'+num+']').text(Math.floor(result.curr*100)+'%');
                    }

                },
                finish: function(result){
                    $('#videofileId').val(result.fileId);
                    $('#videoName').val(result.videoName);
                    $('#videoUrl').val(result.videoUrl);
                    $('[name=videofileId'+num+']').text(result.fileId);
                    $('[name=videourl'+num+']').text(result.videoUrl);
                    if(result.message) {
                        $('[name=videofileId'+num+']').text(result.message);
                    }
                    var fileId=result.fileId;
                    // var fileId='5285890781020084748';
                    $.ajax({
                        url:"/delivery/transcoding",
                        type:"get",
                        dataType:"json",
                        data:{fileid:fileId},
                        success:function(data) {
                            console.log(data);
                            if(data.status==200){
                                $('.yx_djsc').hide();
                                //  $('.shipingbofang').hide();
                                $('.yx_loading').show();
                                jz_time = setInterval(function(){ iszhuamatrue(fileId) }, 10000);
                            }
//                            else{
//                                alert("提交失败");
//                            }
                        }
                    });
                }
            });
            if(resultMsg){
                $('[name=box'+num+']').text(resultMsg);
            }
            $('#form1')[0].reset();
        });
        $('#uploadVideoNow').on('click', function () {
            $('#uploadVideoNow-file').click();
        });
        //获取视频时长计时为分和秒
        function myFunction(ele) {
            //      alert('a');
            //   timelog=Math.round(ele.duration);
            //   console.log(ele.duration);
            //    alert(timelog);
            //   $('#duration').val(timelog);
        }

        /*
         * 取消上传绑定事件，示例一与示例二通用
         */

        $('#resultBox').on('click', '[act=cancel-upload]', function() {
            /*	var cancelresult = qcVideo.ugcUploader.cancel({
             cos: cosBox[$(this).attr('cosnum')],
             taskId: $(this).attr('taskId'),
             });*/
            var r=confirm("确认要停止上传吗");
            if (r==true)
            {
                $('#resultBox').hide();
                $(".jindguzhi").html('0%');
                $(".jindguzhi").css("left","0px");
                $(".jdt_bfb").css("width","0px");
                //console.log("You pressed OK!");
                var cancelresult = qcVideo.ugcUploader.cancel({
                    cos: cosBox[$(this).attr('cosnum')],
                    taskId: $(this).attr('taskId')
                });
            }
            else
            {
                console.log("You pressed Cancel!");
            }
        });

        $(function (){
            $("#tctskuan").click(function (){
                $(".yx_tctskuan").hide();
                $(".yx_tctskuan").find("#tsk_namet").html("");
                //$(".yx_tctskuan").find(".scttsk_nr").html("");
                $(".yx_tctskuan").find(".yx_tctskuan_an").show();
            });
            $(".del_sp").click(function (){
                $(".shipingbofang").hide();
                $(".yx_djsc").show();
                $(".bdspingmc").html();
                $('#videofileId').val("");
                $('#videoName').val("");
                $('#videoUrl').val("");
                // $("#playerdiv").html("");
            })
        })

    </script>
    <script>
        var player = TCPlayer("player-container-id", { // player-container-id 为播放器容器ID，必须与html中一致
            fileID: "<?php echo $data['video_id']?>", // 请传入需要播放的视频filID 必须
            appID: "1252719796", // 请传入点播账号的appID 必须
            autoplay: true //是否自动播放
            //其他参数请在开发文档中查看
        });
    </script>
    <script>
        var _mtac = {};
        (function() {
            var mta = document.createElement("script");
            mta.src = "//pingjs.qq.com/h5/stats.js?v2.0.2";
            mta.setAttribute("name", "MTAH5");
            mta.setAttribute("sid", "500586412");
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(mta, s);
        })();
    </script>
    <script>
        $(function(){
            //购买信息左侧--大图图片上传----图片上传------图片删除
            //图片上传
            $('.Upload-imginput').click(function(){
                uploadimg('Upload-imginput');
            })
            //上传的图片删除
            $('.del_upimg').click(function(){
                $('.sy_upimg_position').hide();
                $('.sy_upimg_position .img-posit').attr('src','');
                $('.yx_djsc').show();
                $('#videofileId').val("");
                $('#videoName').val("");
                $('#videoUrl').val("");
            })

            //知识产权上传---------点击加号弹出弹框---弹窗关闭---点击弹框中的图片追加到知识产权div中----知识产权div中---
            //点击加号弹出弹框
            $('#sy_add_upimg').click(function(){
                var objw=$(window);//当前窗口
                var objc=$('.sy_upimg_panel');//当前对话框
                var brsw=objw.width();
                var brsh=objw.height();
                var sclL=objw.scrollLeft();
                var sclT=objw.scrollTop();
                var curw=objc.width();
                var curh=objc.height();
                //计算对话框居中时的左边距
                var left=parseInt(sclL+(brsw -curw)/2);
                var top=parseInt(sclT+(brsh-curh)/2);
                //设置对话框居中
                objc.css({"left":left,"top":top});
                var html="";
                $.ajax({
                    url:"/delivery/copyright",
                    type:"get",
                    dataType:"json",
                    data:{uid:1},
                    success:function(data) {
                        console.log(data);
                        if(data.status==200){
                            $.each(data.data,function(i,value){
                                html=html+"<li><img src=\""+value.image_url+"?imageView2/0/w/200/h/130\"><p>"+value.name+"</p></li>";
                            });
                            $('.sc_copyright_img').html(html);
                        }
                    }
                });


                $('.sy_upimg_panel').show();
            })
            //弹窗关闭
            $('.sy_upimg_panel .fr').click(function(){
                $('.sy_upimg_panel').hide();
            })
            //点击弹框中的图片追加到知识产权div中
            $('.sy_upimg_list li').live('click',function(){
                var imgurl=$(this).find('img').attr('src');
                var imgurlB=imgurl.replace('?imageView2/0/w/200/h/130','');
                var imgtxt=$(this).find('p').text();
                $("<li class=\"cqslt\"><input type=\"hidden\" value='{\"id\":\"0\",\"name\":\"" + imgtxt + "\",\"url\":\"" + imgurlB + "\"}'><p class=\"cqslt_img\"><img src=" + imgurl + "><span class=\"del_img\">删除</span></p><span class=\"cqslt_nmae\">" + imgtxt + "</span></li>").insertBefore("#sy_add_upimg");
                $('.sy_upimg_panel').hide();
            })
            //知识产权div中img删除
            $('#sy_appendimg .del_img').live('click',function(){
                var resourceid=JSON.parse($(this).parents('.cqslt_img').siblings('input').val().toString());
                var resourcetype=$('#resourcetype').val();
                if(resourcetype!=4){
                    $.ajax({
                        url:"/delivery/deleteresource",
                        type:"get",
                        dataType:"json",
                        data:{resourceid:resourceid.id}
                    });
                }
                $(this).parents('.cqslt').remove();
            })
        })
    </script>
    <script>
        $('.yx_tjcq').click(function(){
            $(this).attr('disable',true);
            var videofileId=$('#videofileId').val();
            if($('#videoNameB').val()==""){
                var videoName=$('#videoName').val();
            }else{
                var videoName=$('#videoNameB').val();
            }
            var resourcetype=$('#resourcetype').val();
            var video_trans_url=$('#video_trans_url').val();
            var videoUrl=$('#videoUrl').val();
            //var videoSha1 = $('#videoSha1').val();
            /*if (videoSha1 == "") {
                alert('正在计算文件密钥,请稍后在试');
                return false;
            }*/
            var videoSize = $('#videoSize').val();
            if(videoUrl=="" || videoName==""){
                $(".yx_tctskuan").show();
                $(".yx_tctskuan").find(".scttsk_nr").html("素材不能为空,请上传!");
                $(".yx_tctskuan").find(".yx_tctskuan_an").hide();
                return false;
            }

//            if(resourcetype==1){ //广告类型，1为视频 替换网址
//                 videoUrl=replaceCosUrl(videoUrl,"http://m0.bjyltf.com");
//            }else{
//                 videoUrl=replaceCosUrl(videoUrl,"http://i1.bjyltf.com");
//            }
            var orderid=$('#orderid').val();
            //  var videoimg=replaceCosUrl($('#videoimg').val(),"http://i1.bjyltf.com");
            var videoimg=$('#videoimg').val();
            if(videoimg==""){
                videoimg="/static/images/flv.jpg";
            }
            var copyright="[";
            var is_copyright=0;
            var duration=$('#duration').val();//实际素材播放时长
            $("#sy_appendimg input").each(function(){
                is_copyright=is_copyright+1;
                copyright=copyright+$(this).val()+",";
            });
            copyright= copyright.substr(0,copyright.length - 1);
            copyright=copyright+"]";
            if(is_copyright==0){
                $(".yx_tctskuan").show();
                $(".yx_tctskuan").find(".scttsk_nr").html("知识产权不能为空,请上传!");
                $(".yx_tctskuan").find(".yx_tctskuan_an").hide();
                return false;
            }
            if(resourcetype!=4){
                $.ajax({
                    url:"/delivery/resource",
                    type:"get",
                    dataType:"json",
                    data:{videoSize:videoSize,orderid:orderid,resource:videoUrl,resource_name:videoName,video_id:videofileId,copyright:copyright,resourcetype:resourcetype,videoimg:videoimg,duration:duration,video_trans_url:video_trans_url},
                    success:function(data) {
                        console.log(data);
                        if(data.status==200){
                            location.href="/delivery/complete";
                        } else {
                            alert('提交失败');
                        }
                    }
                });
            }

        })
    </script>
    <script>
        $('#gobackB').click(function(){
            location.href="/delivery/index";
        });
        $('#goback').click(function(){
            boxmiddle("sy_tctskuan");
            $('.sy_tctskuan').show();
        });
        $('.sy_tjcq').click(function(){
            location.href="/delivery/index";
        });
        $('.sy_qxcq').click(function(){
            $('.sy_tctskuan').hide();

        });
        //居中弹框
        function boxmiddle(classname){
            var objw=$(window);//当前窗口
            var objc=$('.'+classname);//当前对话框
            var brsw=objw.width();
            var brsh=objw.height();
            var sclL=objw.scrollLeft();
            var sclT=objw.scrollTop();
            var curw=objc.width();
            var curh=objc.height();
            //计算对话框居中时的左边距
            var left=parseInt(sclL+(brsw -curw)/2);
            var top=parseInt(sclT+(brsh-curh)/2);
            //设置对话框居中
            objc.css({"left":left,"top":top});
            $('.'+classname).show();

        }
    </script>
    <script>
        //
        function iszhuamatrue(fileid) {
            $.ajax({
                url:"/delivery/gettranscoding",
                type:"get",
                dataType:"json",
                data:{fileid:fileid},
                success:function(data) {
                    console.log(data);
                    if(data.status==200){
                        clearInterval(jz_time);
                        $('.yx_loading').hide();
                        $('#video_trans_url').val(data.url);
                        var playerid="player-container-id"+fileid;
                        $("#playerdiv").html("<video id=" + playerid + " preload=\"auto\"  src=\""+data.url+"\"  class=\"shiping_zs\" width=\"100%\" height=\"100%\" playsinline webkit-playinline x5-playinline></video>");
                        //上传视频后播放
                        var  player = TCPlayer(playerid, { // player-container-id 为播放器容器ID，必须与html中一致
                            fileID: fileid, // 请传入需要播放的视频filID 必须
                            appID: "1252719796", // 请传入点播账号的appID 必须
                            autoplay: false //是否自动播放
                            //其他参数请在开发文档中查看
                        });
                        $('.shipingbofang').show();
                    }
                }
            });
        }
    </script>

</html>
