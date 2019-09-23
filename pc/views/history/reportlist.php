<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>播放报告</title>
    <link rel="icon" type="image/x-icon" href="/static/images/icon.ico" />
    <script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
    <style type="text/css">
        #tableDiv{width:80%;overflow-x:auto; margin:0 auto; border:1px #ccc solid}
        #tableDiv table{color:#000;text-align:center;border-collapse:collapse;font-size:12px;}
        #tableDiv table td{border:1px solid #ddd;border-top:0px;border-left:0px;height:30px; padding:0 10px; min-width:80px;}
        #tableDiv table td.dizhi{min-width:200px;}
        /*火狐兼容描边*/
        .firefoxbm{position:relative;border-right:1px solid #ddd;}
        .firefoxbm:after{ height:1px; content:""; width:100%; border-bottom:1px solid #ddd;position:absolute; bottom:-1px; right:0px; z-index:10;}
        .firefoxbm:before{height:100%;content: '';width:1px;border-right: 1px solid #ddd;position: absolute;top:0px;right:-1px;z-index:5}

        /*导航到本地*/
        .yx_daochu_bg{width:80%;margin:20px auto 0;}
        .yx_daochu{ float:right;-moz-border-radius:4px; -webkit-border-radius:4px;border-radius:4px; background:#838fca; border:0px; width:10%; height:40px; line-height:40px; color:#fff; cursor:pointer}

        /*分页样式  */
        .fenye{ width: 85%;float: left;height:40px; }
        .fenye li a{ color:#999; text-decoration: none }
        .pagination {display: inline-block;border-radius: 4px; margin: 0px; padding:0px;}
        .pagination > li { display: inline;}
        .pagination > li > a,.pagination > li > span {float: left; padding: 6px 12px; line-height: 1.428571429;background-color: #fff; border: 1px solid #dddddd; margin-left: -1px; }
        .pagination > li:first-child > a,.pagination > li:first-child > span { border-bottom-left-radius: 4px; border-top-left-radius: 4px; }
        .pagination > li:last-child > a,.pagination > li:last-child > span { border-bottom-right-radius: 4px; border-top-right-radius: 4px; }
        .pagination > li > a:hover,.pagination > li > span:hover, .pagination > li > a:focus,.pagination > li > span:focus {background-color: #eeeeee;}
        .pagination > .active > a,.pagination > .active > span,.pagination > .active > a:hover,pagination > .active > span:hover,.pagination > .active > a:focus,.pagination > .active > span:focus {z-index: 2;color: #fff; background-color: #5e87b0;border-color: #5e87b0; cursor: default;}
        .pagination > .disabled > span,.pagination > .disabled > span:hover,.pagination > .disabled > span:focus,.pagination > .disabled > a,.pagination > .disabled > a:hover,.pagination > .disabled > a:focus { color: #999; background-color: #fff; border-color: #ddd; cursor: not-allowed; }
        .pagination-lg > li > a,.pagination-lg > li > span { padding: 10px 16px;font-size: 18px; }
        .pagination-lg > li:first-child > a,.pagination-lg > li:first-child > span { border-bottom-left-radius: 6px; border-top-left-radius: 6px; }
        .pagination-lg > li:last-child > a,.pagination-lg > li:last-child > span { border-bottom-right-radius: 6px; border-top-right-radius: 6px; }
        .pagination-sm > li > a, .pagination-sm > li > span { padding: 5px 10px; font-size: 12px;}
        .pagination-sm > li:first-child > a,.pagination-sm > li:first-child > span { border-bottom-left-radius: 3px;border-top-left-radius: 3px; }
        .pagination-sm > li:last-child > a,.pagination-sm > li:last-child > span { border-bottom-right-radius: 3px; border-top-right-radius: 3px; }
    </style>
</head>
<body>
<div class="yx_daochu_bg">
    <span class="yx_tfdq_title">投放报告:</span>
</div>
<div id="tableDiv">
    <table >
        <tr>
            <td class="dizhi">投放地区/播放量</td>
            <?php foreach($datelist as $kdate=>$vdate):?>
                <td><?php echo $vdate;?></td>
            <?php endforeach;?>
            <td>播放总量</td>
            <td>应播总量</td>
            <td>播放率</td>
        </tr>
        <?php foreach($srr as $key=>$value):?>
            <tr>
                <td class="dizhi">
                    <?php echo \pc\models\SystemAddress::getAreaNameById($value["area_id"]);?>
                </td>
                <?php foreach(explode(',',$value['data_list']) as $kl=>$vl):?>
                    <td>
                        <?php echo $vl;?>
                    </td>
                <?php endforeach;?>
                <td style="min-width: 81px;">
                    <?php echo $value['play_total'];?>
                </td>
                <td style="min-width: 81px;">
                    <?php echo $value['should_total'];?>
                </td>
                <td style="min-width: 81px;">
                    <?php echo $value['percentage'];?>
                </td>
            </tr>
        <?php endforeach;?>
        <tr>
            <td style="min-width:199px;">合计<?php echo count($srr);?>街道</td>
            <?php foreach(explode(',',$newtotal['data_list']) as $kt=>$vt):?>
                <td>
                    <?php echo $vt;?>
                </td>
            <?php endforeach;?>
            <td>
                <?php echo $newtotal['play_total'];?>
            </td>
            <td>
                <?php echo $newtotal['should_total'];?>
            </td>
            <td>
                <?php echo $newtotal['percentage'];?>
            </td>
        </tr>
    </table>

</div>
<div class="yx_daochu_bg">
    <div class="fenye">
        <?= \yii\widgets\LinkPager::widget([
            'pagination' => $pages,
            'nextPageLabel' => '下一页',
            'prevPageLabel' => '上一页',
            'firstPageLabel' => '首页',
            'lastPageLabel' => '尾页',
        ]); ?>
    </div>
    <button class="yx_daochu">导出至本地</button>
</div>
<script>
    //初始化第一列颜色
    $(function (){
        $("#tableDiv").find("table tr").each(function() {
            $(this).find("td").eq(0).css({"background-color":"#e9f8ff"});
        });
    })
    $("#tableDiv").scroll(function(){//给table外面的div滚动事件绑定一个函数
        var left=$("#tableDiv").scrollLeft();//获取滚动的距离
        var trs=$("#tableDiv table tr");//获取表格的所有tr
        trs.each(function(i){//对每一个tr（每一行）进行处理
            $(this).children().eq(0).css({"position":"relative","top":"0px","left":left,"background-color":"#e9f8ff"});
            $(this).children().eq(0).addClass("firefoxbm");
        });
    });
</script>

</body>
</html>
