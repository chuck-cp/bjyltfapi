<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>玉龙传媒</title>
    <link rel="stylesheet" type="text/css" href="/static/css/tfls.css">
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
    <!--右侧菜单 搜索-->
        <div class="YX_tfls_ss">
            <div class="fl yx_ss_tj">
                <p><span>订单号</span> <input type="text" value="<?=$order_id;?>" name="order_id" class="YX_tf_input" /></p>
                <p><span>排序</span>  
                <select class="YX_dx_input" id="sort">
                <option value="1" <?php if($sort==1){ ?> selected <?php } ?>>全部</option>
                <option value="asc" <?php if($sort=="asc"){ ?> selected <?php } ?>>投放日期升序排列</option>
                <option value="desc" <?php if($sort=="desc"){ ?> selected <?php } ?>>投放日期降序排列</option>
                </select>
                 </p>  
                 <p><span>类型：</span>  
                <select id="type" class="YX_dx_input">
                <option value="0" <?php if($advert_id==0){?> selected <?php }?>>全部</option>
                <?php foreach($name as $k=>$v){?>
                <option value="<?=$v['id'];?>" <?php if($advert_id==$v['id']){?> selected <?php } ?> ><?=$v['name'];?></option>
                <?php }?>
                </select>
                 </p> 
             </div>
             <!-- <input name="_csrf-backend" type="hidden" id="_csrf" value="<?php echo Yii::$app->request->csrfToken ?>"> -->
             <span class="fr"><button type="submit"  class="YX_ss_button">搜索</button> </span>                                  
        </div>
        <?php use yii\widgets\LinkPager;?>
            <!--右侧菜单 列表-->            
        <div class="YX_tflb">
            <table width="0" border="0"">
              <tr class="yx_tfls_bg_bt">
                <td width="5%">序号</td>
                <td width="10%">订单号</td>
                <td width="20%">投放日期</td>
                <td width="9%">广告类型</td>
                <td width="10%">广告时长</td>
                <td width="20%">投放地区</td>
                <td width="11%">区域屏幕数量</td>
                <td width="8%">播放频次</td>
                <td width="7%">操作</td>
              </tr>
              <?php foreach($orderlist as $k=>$v){?>
              <tr>
                <td><?php echo $k+1; ?></td>
                <td class="xians" title=<?=$v['order_code']; ?>><?php echo $v['order_code']; ?></td>
                <td>起<?php echo $v['start_at'];?>-止<?php echo $v['end_at'];?></td>
                <td title=<?=$v['advert_name']; ?>><?php echo $v['advert_name'];?></td>
                <td><?php echo $v['advert_time'];?></td>
                <td><?php echo $v['area_name'];?></td>
                <td><?php echo $v['screen_number'];?></td>
                <td><?php echo $v['rate'];?>次/天</td>
                <td><a href="/history/orderone?order_id=<?=$v['order_code'];?>">查看</a></td>
              </tr>
              <?php };?>
            </table>
            <?php echo LinkPager::widget(['pagination' => $pagination]);?>
        </div>
    </div>
</div>
<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/sc_js.js"></script>
<script>
    $(function(){
        $(".YX_ss_button").click(function (){
            var order_id = $("input[name='order_id']").val();
            var sort=$("#sort option:selected").val();
            var advert_id=$("#type option:selected").val();
            var paths = '/history/search';
            //alert(url);
            window.location.href = paths+"?order_id=" + order_id + "&sort=" + sort + "&advert_id="+advert_id;
            //alert(sort);
            // $.ajax({
            //     type: "get",
            //     url: paths,
            //     data: {order_id:order_id,sort:sort,advert_name:advert_name},
            //     success: function(data) {
            //         data=eval("("+data+")");
            //         var addtr = '';
            //         var pj = data.page+1;
            //         addtr +='<table width="0" border="0""><tr class="yx_tfls_bg_bt"><td width="5%">序号</td><td width="10%">订单号</td><td width="20%">投放日期</td><td width="9%">广告类型</td><td width="10%">广告时常</td><td width="20%">投放地区</td><td width="11%">区域屏幕数量</td><td width="8%">播放频次</td><td width="7%">操作</td></tr>';
            //         for(var d in data.orderlist){
            //             addtr += '<tr>';
            //             addtr += '<td>'+d+'</td>';
            //             addtr += '<td>'+data.orderlist[d].order_code+'</td>';
            //             addtr += '<td>起'+data.orderlist[d].start_at+'-止'+data.orderlist[d].end_at+'</td>';
            //             addtr += '<td>'+data.orderlist[d].advert_name+'</td>';
            //             addtr += '<td>'+data.orderlist[d].advert_time+'</td>';
            //             addtr += '<td>'+data.orderlist[d].area_name+'</td>';
            //             addtr += '<td>'+data.orderlist[d].screen_number+'</td>';
            //             addtr += '<td>'+data.orderlist[d].rate+'</td>';
            //             addtr += '<td><a href="/history/orderone/">查看</a></td>';
            //             addtr += '</tr>';
            //         }
            //         addtr += '</table>';
            //         addtr += '<ul class="pagination"><li class="prev"><a href="http://pc.bjyltf.cc/history/search?page='+data.page-1+'"><span>&laquo;</span></a></li>';
            //         for(var p=1;p<=data.page_count;p++){
            //             addtr +='<li class="active"><a href="http://pc.bjyltf.cc/history/search?page='+p-1+'">'+p+'</a></li>';

            //         }
            //         addtr += '<li class="prev disabled"><a href="http://pc.bjyltf.cc/history/search?page='+pj+'"><span>&laquo;</span></a></li>';
            //         addtr +='</ul>';
            //         $(".YX_tflb").html(addtr);
            //         //console.log(dataObj);
            //         alert(data.page-1); 
            //     }
            // });
        })
    })
</script>
</html>
