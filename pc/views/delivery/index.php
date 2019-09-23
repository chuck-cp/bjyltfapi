<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>玉龙传媒</title>
    <link rel="stylesheet" type="text/css" href="/static/css/public.css">
    <link rel="stylesheet" type="text/css" href="/static/css/shouye.css">
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
<div class="wrap dingwe clearfix">
    <!--左侧菜单-->
    <div class="yx_left_menu fl">
        <!--用记信息-->
        <div class="yx_exit">
            <p class="yx_tcdl"><a href="<?php echo  \yii\helpers\Url::to(['index/logout']);?>">退出登录</a></p>
            <p class="yx_photo"><img src="<?php echo Yii::$app->user->identity->avatar;?>  "></p>
            <p class="yx_name"><a href=""><?php echo Yii::$app->user->identity->name;?></a></p>
        </div>
        <!--菜单内容-->
        <ul class="yx_menu_nr">
            <li class="yx_gaoliang"><span><img src="/static/images/tfgl.png"></span><a href="">投放素材</a></li>
            <li><span><img src="/static/images/cqgl.png"></span><a href="<?php echo  \yii\helpers\Url::to(['property/index']);?>">产权管理</a></li>
            <li><span><img src="/static/images/lsgl.png"></span><a href="<?php echo  \yii\helpers\Url::to(['history/delivery']);?>">历史管理</a></li>
        </ul>
    </div>
    <!--右侧菜单-->
    <div class="yx_right_menu fr">
        <p class="yx_dqyrbt">当前任务</p>
        <!--###############  有任务时 ###############    -->
        <?php  if(!empty($orderData)):?>
            <div class="yx_rwlb" >
                <ul>
                    <?php  foreach($orderData as $k=>$v):?>

                            <a href="<?php echo  \yii\helpers\Url::to(['delivery/info','orderid'=>$v['id']]);?>">
                            <li>
                                <?php if($v['examine_status'] == 0):?>
                                <p class="yx_ga_img"><img src="/static/images/no_shiping.jpg" width="280" height="200"></p>          <?php else:?>
                                <?php if(strpos($v['resource_thumbnail'],',')===false):?>
                                        <p class="yx_ga_img"><img src="<?php echo $v['resource_thumbnail']."?imageView2/0/w/280/h/200";?>" width="280" height="200"></p>
                                        <?php else: $img_thumbnail=explode(",",$v['resource_thumbnail']);?>
                                        <p class="yx_ga_img"><img src="<?php echo $img_thumbnail[0]."?imageView2/0/w/280/h/98";?>" width="280" height="98"><img src="<?php echo $img_thumbnail[1]."?imageView2/0/w/280/h/98";?>" width="280" height="98"></p>
                                    <?php endif;?>


                                <?php endif;?>
                                <div class="guangao_xx">
                                    <span>广告类型：<?php echo $v['advert_name'];?></span>
                                    <span>投放时间：起<?php echo $v['date']['start_at'];?> 止<?php echo $v['date']['end_at'];?></span>
                                    <span>广告时长：<?php echo $v['advert_time'];?></span>
                                </div>
                            <?php if($v['examine_status'] == 0):?>
                                <p class="guangao_zt">
                                    <cite class="zt_black"></cite>
                                    <span>未设置</span>
                                </p>
                            <?php elseif($v['examine_status'] == 1) :?>
                                <p class="guangao_zt">
                                    <cite class="zt_yellow"></cite>
                                    <span>审核中</span>
                                </p>
                            <?php elseif($v['examine_status'] == 2):?>
                                <p class="guangao_zt">
                                    <cite class="zt_red"></cite>
                                    <span>被驳回</span>
                                </p>
                            <?php elseif($v['examine_status'] == 3):?>
                                <p class="guangao_zt">
                                    <cite class="zt_blue"></cite>
                                    <span>待投放</span>
                                </p>
                            <?php elseif($v['examine_status'] == 4):?>
                                <p class="guangao_zt">
                                    <cite class="zt_green"></cite>
                                    <span>投放中</span>
                                </p>
                            <?php endif;?>

                            </li>
                            </a>
                    <?php endforeach;?>
                </ul>
            </div>
        <?php else:?>
            <!--        ###############  无有任务时 ###############    -->
            <div class="yx_rwlb" style="display:block">
                <p class="yx_wgm">当前您还未购买任何广告，请前往玉龙传媒APP购买</p>
            </div>
        <?php endif;?>
    </div>

    <div class="msm" style="display: none">
       <?php echo $jsonMsm;?>
    </div>


    <div class="tipfloat" data-num="2">
        <p class="tipfloat_bt">
            <span class="fl">消息</span>
            <span class="fr close"><img src="/static/images/guanbi.png"></span>
        </p>
        <div class="ranklist">
            <div class="xx_nrong"></div>
        </div>
    </div>
</div>

<script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="/static/js/sc_js.js"></script>
<script>
//次数
    var tk_index=0;
    var xx_num;
    var msm = $(".msm").html();
    var arr = JSON.parse(msm);

    $(function(){
        xx_num = arr.length;
        if(xx_num > 0){
            tankuang()
        }
        $(".close").click(function(){
            $(".tipfloat").animate({height:"hide"},800);
            if(tk_index!=xx_num){
                setTimeout(tankuang,1000);
            }

        });
    })
    function tankuang(){
        if(tk_index!=xx_num){
            $(".tipfloat").animate({height:"show"},800);
            for(var i in arr){
                if(i == tk_index ){
                   add_div =  '<p>您购买的广告（<cite>订单号：'+arr[i]['order_code']+'</cite></p><span>'+arr[i]['examine_desc']+'</span>';
                }
            }
            $(".xx_nrong").html(add_div);
            tk_index++;
        }
    }


</script>
</html>