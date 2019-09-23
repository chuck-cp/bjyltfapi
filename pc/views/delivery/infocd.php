<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>视频上传-通用页面</title>
    <link rel="stylesheet" type="text/css" href="/static/css/acd_upload.css">
    <link rel="icon" type="image/x-icon" href="/static/images/icon.ico" />
    <script type="text/javascript" src="/static/js/sha1.js"></script>
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
                <dt>购买广告类型：</dt>
                <dd><?php echo $data['advert_name']."    ".$data['advert_time'];?></dd>
            </dl>
            <dl>
                <dt>投放日期：</dt>
                <dd>起<?php echo $data['start_at'];?> 止<?php echo $data['end_at'];?></dd>
            </dl>
            <dl>
                <dt>播放频次：</dt>
                <dd><?php echo $data['rate'];?>次/天</dd>
            </dl>
            <dl class="dizhi">
                <dt>购买地区：</dt>
                <dd> <?php if($data['deal_price']==0){echo "无购买地区";}else{?>
                    <a href="/delivery/throwarea?orderid=<?php echo $data['id'];?>" target="_blank">查看购买区域</a>
                    <?php }?>
                </dd>
            </dl>
        </div>
        <input type="hidden" id="orderid" value="<?php echo $data['id'];?>">
        <input type="hidden" id="resourcetype" value="<?php echo $data['type'];?>">
        <input type="hidden" id="myFolder" value="<?php echo "/member/".$data['member_id']."/";?>">
        <input type="hidden" id="videofileId" value="<?php echo $data['video_id']?>">
        <input type="hidden" id="videoNameC" value="<?php echo $data['resource_nameC']?>">
        <input type="hidden" id="videoNameD" value="<?php echo $data['resource_nameD']?>">
        <input type="hidden" id="videoUrlC" value="<?php echo $data['resourceC']?>">
        <input type="hidden" id="videoUrlD" value="<?php echo $data['resourceD']?>">
        <input type="hidden" id="videoimgC" value="<?php echo $data['resource_thumbnailC']?>">
        <input type="hidden" id="videoimgD" value="<?php echo $data['resource_thumbnailD']?>">
        <input type="hidden" id="examine_status" value="<?php echo $data['examine_status']?>">
        <input type="hidden" id="start_at" value="<?php echo $data['start_at']?>">
        <input type="hidden" id="advert_time" value="<?php echo $data['advert_time']?>">
        <input type="hidden" id="duration" value="<?php echo $data['resource_duration']?>">
        <input type="hidden" id="lock" value="<?php echo $data['lock']?>">
        <input type="hidden" id="advert_key" value="<?php echo $data['advert_key']?>">
        <!--素材上传-->
        <div class="yx_scsp fl">
            <p class="yx_scsp_bt">素材上传</p>
            <!--点击上传-->
            <div class="yx_djsc">
                <!--上传   c屏-->
                <div class="c_p_img_sc fl" id="sy_upimg_positionC">
                    <div class="sc_sucai" <?php if($data['examine_status']>0){echo "style=\"display: none\"";}?>>
                        <p class="c_p_img">
                            <input id="uploadVideoNow-file" type="file"  class="c_p_imginput" data-type="type1" />
                            <a id="uploadVideoNow" href="javascript:void(0);" class="djscsp">上传</a>
                        </p>
                        <p class="sc_scsm">c屏素材上传-图片格式为JPG 分辨率1560*135</p>
                    </div>
                    <div class="sy_upimg_position" <?php if($data['examine_status']>0){echo "style=\"display: block\"";}?>>
                        <p class="bdspingmc">
                            <input id="videoNamec" class="img_name" type="text" value="<?php echo $data['resource_nameC']?>">
                        </p>

                        <p class="img-p">
                            <?php if($data['examine_status']<3){?>
                            <cite class="del_upimg"><img src="/static/images/sc_sp.png"></cite>
                            <?php }?>
                            <img class="img-posit" src="<?php echo $data['resourceC']?>">
                        </p>
                    </div>
                </div>
                <!--上传 d屏-->
                <div class="c_p_img_sc fl"  id="sy_upimg_positionD">
                    <div class="sc_sucai" <?php if($data['examine_status']>0){echo "style=\"display: none\"";}?>>
                        <p class="c_p_img">
                            <input id="uploadVideoNow-file" type="file"  class="c_p_imginput" data-type="type2" />
                            <a id="uploadVideoNow" href="javascript:void(0);" class="djscsp">上传</a>
                        </p>
                        <!--<p class="sc_scsm">d屏素材上传-图片格式为JPG 分辨率1560*135</p>-->
                    </div>
                    <div class="sy_upimg_position" <?php if($data['examine_status']>0){echo "style=\"display: block\"";}?>>
                        <p class="bdspingmc">
                            <input id="videoNamed"  class="img_name" type="text" value="<?php echo $data['resource_nameD']?>">
                        </p>
                        <p class="img-p">
                            <?php if($data['examine_status']<3){?>
                                <cite class="del_upimg"><img src="/static/images/sc_sp.png"></cite>
                            <?php }?>
                            <img class="img-posit" src="<?php echo $data['resourceD']?>">
                        </p>
                    </div>
                </div>
                <div class="yx_banqsm">
                    <span><a href="/delivery/standard" target="_blank">查看素材上传规范</a></span>
                    <!--<span><a href="/property/banquan" target="_blank">版权声明</a></span>
                    <span><a href="/property/complaint" target="_blank">广告驳回声明</a></span>-->
                </div>
            </div>
        </div>
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
</div>


<!-- 提示框 -->
<div class="yx_tctskuan">
    <!-- 标题 -->
    <div class="yx_tctskuan_bt">
        <p class="fl" id="tsk_namet">提示</p>
        <p class="fr">
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
        <div class="sy_up_input"><p class="sy_upload-p"><input type="file" class="Upload-imginput" data-type="type3">+添加产权证明</p></div>
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


</body>
<script>
    var baseApiUrl = "<?=Yii::$app->params['baseApiUrl']?>";
</script>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script src="/static/js/sc_js.js"></script>
<script src="/static/js/ugcUploader.js"></script>
<script src="/static/js/cping_img.js"></script>

<script>
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

    $(function (){

        //c屏d屏上传图片
        $('.c_p_imginput').click(function(){
            uploadimg('c_p_imginput');
        })
        //闭关提示
        $("#tctskuan").click(function (){
            $(".yx_tctskuan").hide();
            $(".yx_tctskuan").find(".scttsk_nr").html("");
        });


        //知识产权上传图片上传
        $('.Upload-imginput').click(function(){
            uploadimg('Upload-imginput');
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
        });

        //弹窗关闭
        $('.sy_upimg_panel .fr').click(function(){
            $('.sy_upimg_panel').hide();
        });
        //点击弹框中的图片追加到知识产权div中
        $('.sy_upimg_list li').live('click',function(){
            var imgurl=$(this).find('img').attr('src');
            var imgurlB=imgurl.replace('?imageView2/0/w/200/h/130','');
            var imgtxt=$(this).find('p').text();
            $("<li class=\"cqslt\"><input type=\"hidden\" value='{\"id\":\"0\",\"name\":\"" + imgtxt + "\",\"url\":\"" + imgurlB + "\"}'><p class=\"cqslt_img\"><img src=" + imgurl + "><span class=\"del_img\">删除</span></p><span class=\"cqslt_nmae\">" + imgtxt + "</span></li>").insertBefore("#sy_add_upimg");
            $('.sy_upimg_panel').hide();
        });
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
        });
        //sy_upimg_positionC  del_upimg
        $("#sy_upimg_positionC .del_upimg").click(function (){
            $("#sy_upimg_positionC").find(".sc_sucai").show();
            $('#sy_upimg_positionC').find(".sy_upimg_position").hide();
            $('#duration').val("30");//图片播放时长30;
            $('#videoNameC').val("");
            $('#videoUrlC').val("");
            $('#videoimgC').val("");
        })
        $("#sy_upimg_positionD .del_upimg").click(function (){
            $("#sy_upimg_positionD").find(".sc_sucai").show();
            $('#sy_upimg_positionD').find(".sy_upimg_position").hide();
            $('#duration').val("30");//图片播放时长30;
            $('#videoNameC').val("");
            $('#videoUrlC').val("");
            $('#videoimgC').val("");
        })
    })
</script>
<script>
    $('.yx_tjcq').click(function(){
        var orderid=$('#orderid').val();
        var videoUrlC=$('#videoUrlC').val();
        var videoUrlD=$('#videoUrlD').val();
        var videoimgC=$('#videoimgC').val();
        var videoimgD=$('#videoimgD').val();
        var videoNameC=$('#videoNameC').val();
        var videoNameD=$('#videoNameD').val();
        if(videoUrlC=="" || videoNameC==""){
            $(".yx_tctskuan").show();
            $(".yx_tctskuan").find(".scttsk_nr").html("C屏素材不能为空,请上传!");
            $(".yx_tctskuan").find(".yx_tctskuan_an").hide();
            return false;
        }
        if(videoUrlD=="" || videoNameD==""){
            $(".yx_tctskuan").show();
            $(".yx_tctskuan").find(".scttsk_nr").html("D屏素材不能为空,请上传!");
            $(".yx_tctskuan").find(".yx_tctskuan_an").hide();
            return false;
        }
        var videoUrl=videoUrlC+","+videoUrlD;
        var videoName=videoNameC+","+videoNameD;
        var videoimg=videoimgC+","+videoimgD;
        var resourcetype=$('#resourcetype').val();
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
        var videofileId=$('#videofileId').val();
        $.ajax({
                url:"/delivery/resource",
                type:"get",
                dataType:"json",
                data:{orderid:orderid,resource:videoUrl,resource_name:videoName,video_id:videofileId,copyright:copyright,resourcetype:resourcetype,videoimg:videoimg,duration:duration},
                success:function(data) {
                    console.log(data);
                    if(data.status==200){
                        location.href="/delivery/complete";
                    }
                }
            });

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

</html>
