<?php
use yii\helpers\Html;
use yii\helpers\Url;
//echo '<pre>';
//print_r($shopList);die;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>已申请店铺</title>
    <meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" href="/static/css/sy_azywlb.css" />
</head>
<body>
<div class="sy_nbbox">
    <?if(!empty($shopList)):?>
    <ul>
        <?foreach ($shopList as $v):?>
            <?if(isset($v['name'])):?>
                <li class="sy_nblist">
                    <a href="<?=Html::encode(Url::to(['screen/underlinecheck','shopid'=>$v['id'],'token'=>$token,'dev'=>$dev]))?>">
                        <div class="sy_nblistbox">
                            <div class="img">
                                <img src="<?= Html::encode($v['shop_image'].'?imageView2/1/w/100/h/100')?>">
                            </div>
                            <div class="txt">
                                <p><?= Html::encode($v['name'])?></p>
                                <p><?= Html::encode($v['area_name'])?></p>
                                <p><?= Html::encode($v['address'])?></p>
                            </div>
                            <div class="status">
                                <?if($v['status'] == 5):?>
                                    已安装
                                <?elseif($v['status'] == 4):?>
                                    安装未通过
                                <?elseif($v['status'] == 3 && $v['screen_status'] > 0):?>
                                    安装待审核
                                <?elseif($v['status'] == 2):?>
                                    待安装
                                <?elseif ($v['status'] == 3 && ($v['screen_status'] == 0 || $v['screen_status'] == 2)):?>
                                    未激活
                                <?elseif($v['status'] == 1):?>
                                    申请未通过
                                <?elseif($v['status'] == 0):?>
                                    申请待审核
                                <?endif;?>
                            </div>
                        </div>
                    </a>
                </li>
            <? else:?>
                <li class="sy_nblist">
                    <a href="<?=Html::encode(Url::to(['screen/underlineheadoffice','headquarters_id'=>$v['id'],'token'=>$token,'dev'=>$dev]))?>">
                        <div class="sy_nblistbox">
                            <div class="img">
                                <img src="<?= Html::encode($v['business_licence'].'?imageView2/1/w/100/h/100')?>">
                            </div>
                            <div class="txt">
                                <p><?= Html::encode($v['company_name'])?>(总部)</p>
                                <p><?= Html::encode(str_replace(['&gt;',' '],'',$v['company_area_name']))?></p>
                                <p><?= Html::encode($v['company_address'])?></p>
                            </div>
                            <div class="status">
                                <?if($v['examine_status'] == 0):?>
                                    待审核
                                <?elseif($v['examine_status'] == 1):?>
                                    审核通过
                                <?elseif($v['examine_status'] == 2):?>
                                    已驳回
                                <?endif;?>
                            </div>
                        </div>
                    </a>
                </li>
            <?endif;?>
        <?endforeach;?>
    </ul>
    <?else:?>
        <p style="text-align: center; margin-top: 70px;color: #CCC;font-size: 16px;">对不起，您暂无安装业务</p>
    <?endif;?>
</div>



</body>
</html>
