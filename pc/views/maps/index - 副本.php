<!DOCTYPE HTML>
<html>
<head>
    <title>店铺详情</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
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
            height:40px;
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
    </style>
    <script type="text/javascript" src="//api.map.baidu.com/api?v=2.0&ak=uX28OgIzOvbBvfcCFMqxzORy6AGBvEHO"></script>
    <script type="text/javascript" src="//lbsyun.baidu.com/jsdemo/data/points-sample-data.js"></script>
</head>
<body>
<div id="map"></div>
<script type="text/javascript">
    var markerArr  = <?php echo $markerArr['0']?>;
    var markerArr2 = <?php echo $markerArr['1']?>;
    var map = new BMap.Map("map", {enableMapClick:false});                        // 创建Map实例
    map.centerAndZoom(new BMap.Point(105.000, 38.000), 5);     // 初始化地图,设置中心点坐标和地图级别
    map.enableScrollWheelZoom();                        //启用滚轮放大缩小
    if (document.createElement('canvas').getContext) {  // 判断当前浏览器是否支持绘制海量点
        //安装店铺总量
        var points = [];  // 添加海量点数据
        for (var i = 0; i < markerArr.length; i++) {
            var json = markerArr[i];
            points.push(new BMap.Point(json.j, json.w));
        }
        var options = {
            size: 5,
            shape: 1,
            /*size: BMAP_POINT_SIZE_SMALL,
            shape: BMAP_POINT_SHAPE_STAR,*/
            color: '#E56308'
        }
        var pointCollection = new BMap.PointCollection(points, options);  // 初始化PointCollection
        pointCollection.addEventListener('click', function (e) {
            var content ="";
            for (var i = 0; i < markerArr.length; i++) {
                var json = markerArr[i];
                points.push(new BMap.Point(json.j, json.w));
                if (json.j == e.point.lng && json.w == e.point.lat) {//经度==点击的,维度
                    content = json.content
                    break;
                }
            }
            var point = new BMap.Point(e.point.lng, e.point.lat);
            var opts = {
                width: 300, // 信息窗口宽度
                //height: 70, // 信息窗口高度
                title:"", // 信息窗口标题
                enableMessage: false,//设置允许信息窗发送短息
            }
            var infowindow = new BMap.InfoWindow(content, opts);
            map.openInfoWindow(infowindow, point);


        });
        map.addOverlay(pointCollection);  // 添加Overlay

        //未安装店铺数量
        var points2 = [];  // 添加海量点数据
        for (var i = 0; i < markerArr2.length; i++) {
            var json2 = markerArr2[i];
            points2.push(new BMap.Point(json2.j, json2.w));
        }
        var options2 = {
            size: 5,
            shape: 1,
            /*size: BMAP_POINT_SIZE_SMALL,
            shape: BMAP_POINT_SHAPE_STAR,*/
            color: '#18A614'
        }
        var pointCollection2 = new BMap.PointCollection(points2, options2);  // 初始化PointCollection
        pointCollection2.addEventListener('click', function (e) {
            var content2 ="";
            for (var i = 0; i < markerArr2.length; i++) {
                var json2 = markerArr2[i];
                points2.push(new BMap.Point(json2.j, json2.w));
                if (json2.j == e.point.lng && json2.w == e.point.lat) {//经度==点击的,维度
                    content2 = json2.content
                    break;
                }
            }
            var point2 = new BMap.Point(e.point.lng, e.point.lat);
            var opts2 = {
                width: 300, // 信息窗口宽度
                //height: 70, // 信息窗口高度
                title:"", // 信息窗口标题
                enableMessage: false,//设置允许信息窗发送短息
            }
            var infowindow2 = new BMap.InfoWindow(content2, opts2);
            map.openInfoWindow(infowindow2, point2);
        });
        map.addOverlay(pointCollection2);  // 添加Overlay
    } else {
        alert('请在chrome、safari、IE8+以上浏览器查看本示例');
    }
</script>
</body>
</html>
