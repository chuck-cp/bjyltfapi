<!DOCTYPE HTML>
<html>
<head>
    <title>加载海量点</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <style type="text/css">
        html,body{
            margin:0;
            width:100%;
            height:100%;
            background:#ffffff;
        }
        #map{
            width:100%;
            height:100%;
        }
        #panel {
            position: absolute;
            top:30px;
            left:10px;
            z-index: 999;
            color: #fff;
        }
        #login{
            position:absolute;
            width:300px;
            height:10px;
            left:50%;
            top:50%;
            margin:-40px 0 0 -150px;
        }
        #login input[type=password]{
            width:200px;
            height:30px;
            padding:3px;
            line-height:30px;
            border:1px solid #000;
        }
        #login input[type=submit]{
            width:80px;
            height:38px;
            display:inline-block;
            line-height:38px;
        }
        .BMap_bubble_content{
            font-size: 10px;
        }
        .anchorBL{
            display:none;
        }
        .BMap_bubble_title{
            color:black;
            font-size:13px;
            font-weight: bold;
            text-align:left;
        }
        .BMap_pop div:nth-child(1){
            border-radius:7px 0 0 0;
            height:200px;
        }
        .BMap_pop div:nth-child(3){
            border-radius:0 7px 0 0;background:#ABABAB;;
            /*background: #ABABAB;*/
            width:23px;
            width:0px;height;0px;
        }
        .BMap_pop div:nth-child(3) div{
            border-radius:7px;
        }
        .BMap_pop div:nth-child(5){
            border-radius:0 0 0 7px;
        }
        .BMap_pop div:nth-child(5) div{
            border-radius:7px;
        }
        .BMap_pop div:nth-child(7){
            border-radius:0 0 7px 0 ;
        }
        .BMap_pop div:nth-child div(7){
            border-radius:7px ;
        }
        ---------------------
        作者：qq_41827356
        来源：CSDN
        原文：https://blog.csdn.net/qq_41827356/article/details/80942362
        版权声明：本文为博主原创文章，转载请附上博文链接！
    </style>
    <script type="text/javascript" src="//api.map.baidu.com/api?v=2.0&ak=uX28OgIzOvbBvfcCFMqxzORy6AGBvEHO"></script>
    <script type="text/javascript" src="//lbsyun.baidu.com/jsdemo/data/points-sample-data.js"></script>
</head>
<body>
<div id="map"></div>
<script type="text/javascript">
    var markerArr=<?php echo $citys;?>;
    var map = new BMap.Map("map", {enableMapClick:false});                        // 创建Map实例
   // map.centerAndZoom(new BMap.Point(105.000, 38.000), 5);     // 初始化地图,设置中心点坐标和地图级别
    map.enableScrollWheelZoom();
    <?if($area_id==101):?>
        var pointss = new BMap.Point(105.000, 38.000);
        map.centerAndZoom(pointss,5);
    <?else:?>
        var myGeo = new BMap.Geocoder();
        // 灏嗗湴鍧€瑙ｆ瀽缁撴灉鏄剧ず鍦ㄥ湴鍥句笂锛屽苟璋冩暣鍦板浘瑙嗛噹
        myGeo.getPoint("<?php echo $areaname;?>", function(pointss){
                if (pointss) {
                    map.centerAndZoom(pointss, <?echo $level;?>);
                    // map.addOverlay(new BMap.Marker(point));
                }
            },
            "北京");
    <?endif;?>
    //启用滚轮放大缩小
    if (document.createElement('canvas').getContext) {  // 判断当前浏览器是否支持绘制海量点
        //瀹夎£呭簵閾烘€婚噺
        var points = [];  // 娣诲姞娴烽噺鐐规暟鎹®
        for (var i = 0; i < markerArr.length; i++) {
            var json = markerArr[i];
            points.push(new BMap.Point(json.j, json.w));
        }
        var options = {
            shape: 2,
        }
        var pointCollection = new BMap.PointCollection(points, options);
        pointCollection.addEventListener('click', function (e){
            var content ="";
            for (var i = 0; i < markerArr.length; i++) {
                var json = markerArr[i];
                points.push(new BMap.Point(json.j, json.w));
                if (json.j == e.point.lng && json.w == e.point.lat) {
                    content = json.content
                    break;
                }
            }
            var point = new BMap.Point(e.point.lng, e.point.lat);
            var opts = {
                width: 250,
                height: 120,
                title:"",
                enableMessage: false,
            }
            var infowindow = new BMap.InfoWindow(content, opts);
            map.openInfoWindow(infowindow, point);


        });
        map.addOverlay(pointCollection);  // 娣诲姞Overlay
    } else {
        alert('请在chrome、safari、IE8+以上浏览器查看本示例');
    }
</script>
</body>
</html>

