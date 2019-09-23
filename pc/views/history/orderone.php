<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>玉龙传媒</title>
    <link rel="stylesheet" type="text/css" href="/static/css/upload.css">
    <link rel="stylesheet" type="text/css" href="/static/css/public.css">
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
            <p class="yx_name"><a href=""><?=\Yii::$app->user->identity->name;?></a></p>
        </div>
        <!--菜单内容-->
        <ul class="yx_menu_nr">
            <li><span><img src="/static/images/tfgl.png"></span><a href="/delivery/index">投放素材</a></li>
            <li><span><img src="/static/images/cqgl.png"></span><a href="/property/index">产权管理</a></li>
            <li class="yx_gaoliang"><span><img src="/static/images/lsgl.png"></span><a href="/history/delivery">历史管理</a></li>
        </ul>    
    </div>
    <!--右侧菜单-->
    <div class="yx_right_menu fr">
        <div class="sy_curr_status">
            <p class="status">
                <strong>当前状态</strong>
                <span class="wsz">投放完成</span>    
            </p>
        </div>
       <!--购买信息-->
        <div class="yx_gmxx fl">
            <p>购买信息</p>
            <dl>
                <dt>订单号：</dt>
                <dd id="info_id"><?=$orderinfo['info']['order_code'];?></dd>
            </dl>
            <dl>
                <dt>购买地区：</dt>
                <dd><?=$orderinfo['info']['area_name'];?>
                </dd>
            </dl>
            <dl>
                <dt>所选区域屏幕总数：</dt>
                <dd><?=$orderinfo['info']['screen_number'];?></dd>
            </dl>                    
            <dl>
                <dt>购买广告类型：</dt>
                <dd><?=$orderinfo['info']['advert_name']; ?>   <?=$orderinfo['info']['advert_time']; ?></dd>
            </dl>                    
            <dl>
                <dt>投放日期：</dt>
                <dd>起<?=$orderinfo['info']['start_at']; ?>  止<?=$orderinfo['info']['end_at']; ?></dd>
            </dl>  
            <dl>
                <dt>播放频次：</dt>
                <dd><?=$orderinfo['info']['rate'];?>次/天</dd>
            </dl>  
        </div>
         <!--上传视频-->
         <div class="yx_scsp fl">
            <p class="yx_scsp_bt">上传视频</p>
            <!--点击上传-->
            <div class="yx_djsc">
                 <!--视频 播放-->
                    <div class="shipingbofang" style="display:block" >
                        <p class="bdspingmc"><?=$orderinfo['info']['resource_name']?></p>
                        <p class="shiping">
                            <!-- <cite class="del_sp"><img src="/static/images/sc_sp.png"></cite> -->
                            <?php if(@$orderinfo['vcode']){?>
                            <video id="player-container-id" preload="auto"  src="<?=$orderinfo['info']['resource']?>"  class="shiping_zs" width="100%" height="100%" playsinline webkit-playinline x5-playinline></video>
                            <?php }else{?>
                            <img src="<?=$orderinfo['info']['resource']?>"  class="shiping_zs" width="100%" height="100%" >
                            <?php }?>
                        </p>
                    </div>     
            
            </div>           
   
            <div class="yx_banqsm">
                <span><a href="/delivery/standard" target="_blank">查看素材上传规范</a></span>
                <!--<span><a href="">版权声明</a></span>
                <span><a href="">广告驳回声明</a></span>-->
            </div>
         </div>
        <p class="blank10"></p>
        <!--知识产权上传-->
        <div>
            <div class="yx_zscqsc clearfix">
                <p class="yx_scsp_bt">知识产权上传</p>
                
                <div class="cqtplb" id="sy_appendimg">
                    <ul>
                    <?php if(@$orderinfo['copy']){?>
                    <?php foreach($orderinfo['copy'] as $key=>$value){?>
                        <li class="cqslt" data="<?=$key+1;?>">
                            <p class="cqslt_img">
                                <img src="<?=$value['image_url'] ;?>">
                            </p>
                            <span class="cqslt_nmae"><?=$value['name'] ;?></span>
                        </li>
                    <?php }?>
                    <?php }?>
                    </ul>           
                </div> 
            </div>            
            <div class="yx_fuzhi_ad clearfix">
<!--                <span class="yx_copy_zl">将此广告资料复制至新订单中</span>-->
                <button type="button" class="fan_hui" onclick="javascript:window.history.back(-1);">返回</button>
            </div>
        </div>
    
    </div>

<!--弹出订单列表-->
<div class="yx_tfls_ddzs">
	<p class="yx_tfls_ddzs_bt">
    	<span class="fl">当前订单</span>
        <span class="fr close" id="gb_tfls_lb"><img src="/static/images/guanbi.png"></span>
    </p>
    <div class="yx_dqdd_lb">
        <table width="0" border="0">
          <tr class="yx_tfls_bg_bt">
            <td width="4%"><input type="checkbox" name="" class="yx_choice_dqdd_qx"></td>
            <td width="5%">序号</td>
            <td width="10%">订单号</td>
            <td width="18%">投放日期</td>
            <td width="11%">广告类型</td>
            <td width="8%">广告时常</td>
            <td width="25%">投放地区</td>
            <td width="10%">区域屏幕数量</td>
            <td width="9%">播放频次</td>
          </tr>
          <?php foreach($orderlist as $ke=>$va){?>
          <tr>
            <td><input type="checkbox" name="" class="yx_choice_dqdd"></td>
            <td><?=$ke+1;?></td>
            <td class="tf_dd_id" title=<?=$va['order_code'];?>><?=$va['order_code'];?></td>
            <td>起<?=$va['start_at'];?>-止<?=$va['end_at'];?></td>
            <td><?=$va['advert_name'];?></td>
            <td><?=$va['advert_time'];?></td>
            <td><?=$va['area_name'];?></td>
            <td><?=$va['screen_number'];?>台</td>
            <td><?=$va['rate'];?>次/天</td>
          </tr> 
          <?php }?>        
        </table>
    </div>
    <div class="yx_choice_dqdd_qd clearfix">
        <button type="button" class="yx_choice_tjcq">确定</button>
        <button type="button" class="yx_choice_qxcq">取消</button>
    </div>
</div>

<!--弹出订单列表-->
<div class="yx_sccg_ts">
    <p class="yx_sccg_ts_bt">
        <span class="fl">当前订单</span>
        <span class="fr close" id="scdb_qding"><img src="/static/images/guanbi.png"></span>
    </p>
    <div class="yx_tjdd_nr">
        <p class="sc_ture">已成功将历史内容复制至新订单中，请前往相关订单中<a href="/delivery/index" id="ckxgdd">查看</a></p>
        <p class="sc_false">历史内容未能复制失败，请刷新页面后重试</p>
    </div>
    <div class="yx_csccg_qd clearfix">
        <button type="button" class="scdb_qding">确定</button>
    </div>
</div>

 
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script src="/static/js/sc_js.js"></script>
<script src="/static/js/tflx_ck.js"></script>

<!--上传视频  腾迅云代码 播放-->
<!-- 如需在IE8、9浏览器中初始化播放器，浏览器需支持Flash并在页面中引入 -->
        <!--[if lt IE 9]>
        <script src="js/videojs-ie8.js "></script>
 <![endif]-->
 <!-- 如果需要在 Chrome Firefox 等现代浏览器中通过H5播放hls，需要引入 hls.js -->
<script src="/static/js/hls.min.0.8.8.js"></script>
<!-- 引入播放器 js 文件 -->
<script src="/static/js/tcplayer.min.js"></script>
<script>
    var player = TCPlayer("player-container-id", { // player-container-id 为播放器容器ID，必须与html中一致
        fileID: "7447398155020556809", // 请传入需要播放的视频filID 必须
        appID: "1255626690", // 请传入点播账号的appID 必须
        autoplay: false //是否自动播放
        //其他参数请在开发文档中查看
    });
</script>
</html>
