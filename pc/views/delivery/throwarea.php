<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>投放地区详情</title>
    <link rel="icon" type="image/x-icon" href="/static/images/icon.ico" />
    <script type="text/javascript" src="/static/js/jquery-1.7.2.min.js"></script>
    <style type="text/css">
        #tableDiv{width:80%;overflow-x:auto; margin:0 auto; border:1px #ccc solid}
        #tableDiv table{color:#000;text-align:center;border-collapse:collapse;font-size:12px;}
        #tableDiv table td{border:1px solid #ddd;border-top:0px;border-left:0px;height:30px; padding:0 10px; min-width:80px;}
        /*导航到本地*/
        .yx_daochu_bg{width:80%;margin:20px auto 0;}
        .yx_daochu{ float:right;-moz-border-radius:4px; -webkit-border-radius:4px;border-radius:4px; background:#838fca; border:0px; width:100px; height:40px; line-height:40px; color:#fff; cursor:pointer;text-decoration: none;text-align: center;}
        #tableDiv table td.dizhi{min-width:200px;}
        .yx_tfdq_title{ line-height: 40px;}
        /*火狐兼容描边*/
        .firefoxbm{position:relative;border-right:1px solid #ddd;}
        .firefoxbm:after{ height:1px; content:""; width:100%; border-bottom:1px solid #ddd;position:absolute; bottom:-1px; right:0px; z-index:10;}
        .firefoxbm:before{height:100%;content: '';width:1px;border-right: 1px solid #ddd;position: absolute;top:0px;right:-1px;z-index:5}


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
   <span class="yx_tfdq_title">投放地区:</span>
</div>
<div id="tableDiv">
    <table >
        <tr>
            <td class="dizhi">投放地区/排期</td>
            <?php foreach($datelist as $kdate=>$vdate):?>
                <td><?php echo $vdate ?></td>
            <?php endforeach;?>
        </tr>
        <?php foreach($srr as $key=>$value):?>
            <tr>
                <td style="max-width: 201px;">
                    <?php echo \pc\models\SystemAddress::getAreaNameById($value);?>
                </td>
                <?php foreach($datelist as $kdate=>$vdate):?>
                    <td>
                        <?php  if(empty($newdate[$value][$vdate])){echo '无排期';}else{echo '播放';}?>
                    </td>
                <?php endforeach;?>
            </tr>
        <?php endforeach;?>
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
    <a href="/delivery/throwarea?orderid=<?php echo $order_id;?>&csv=1" class="yx_daochu">导出至本地</a>
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
//获得每一行下面的所有的td，然后选中下标为0的，即第一列，设置position为相对定位
//相对于父div左边的距离为滑动的距离，然后设置个背景颜色，覆盖住后面几列数据滑动到第一列下面的情况
//如果有必要也可以设置一个z-index属性
            $(this).children().eq(0).css({"position":"relative","top":"0px","left":left,"background-color":"#e9f8ff"});
            $(this).children().eq(0).addClass("firefoxbm");
        });
    });
</script>

</body>
</html>
