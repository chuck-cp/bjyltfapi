<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no, width=device-width">
    <title>店铺坐标图</title>
    <link rel="stylesheet" href="https://a.amap.com/jsapi_demos/static/demo-center/css/demo-center.css"/>
    <style>
        html, body, #container {
            height: 100%;
            width: 100%;
        }

        .input-card .btn {
            margin-right: 1.2rem;
            width: 9rem;
        }

        .input-card .btn:last-child {
            margin-right: 0;
        }
        .guanbi{
            display: inline-block;
            float: right;
        }
    </style>
</head>
<body>
<div id="container" class="map" tabindex="0"></div>
<!-- <script type="text/javascript" src='https://a.amap.com/jsapi_demos/static/citys.js'></script> -->
<!-- <script type="text/javascript" src="https://webapi.amap.com/maps?v=1.4.10&key=5a157ca18a863919d096de8adc4e88e6&plugin=AMap.Geocoder"></script> -->
<script type="text/javascript"
        src="https://webapi.amap.com/maps?v=1.4.12&key=5a157ca18a863919d096de8adc4e88e6&plugin=AMap.Geocoder"></script>
<script type="text/javascript">
    var citys  = <?php echo $citys?>;
    /*var citys=[{"title":"","name":"<b>店铺编号:<\/b> 768 <div class='guanbi' onclick='feng()'>X<\/div><br\/><br\/><b>店名:<\/b> 诗呀阁理发店<br\/> <b>地区:<\/b> 北京市北京市东城区东华门街道 <br\/><b>详址:<\/b> 北京市北京市东城区东华门街道北京市东城区东华门街道诗呀阁理发店 <br\/><b>安装台数:<\/b> 5<br\/><b>镜面数量:<\/b> 5","lnglat":[116.40323991709,39.915204120254],"name2":"北京市北京市东城区东华门街道北京市东城区东华门街道诗呀阁理发店","city":"北京市","style":0}];*/
    var map = new AMap.Map('container', {
        zoom: 10,
        center: [116.397477,39.908692]
    });

    var style = [{
        url: '/static/images/gaode/1_1.png',
        anchor: new AMap.Pixel(6, 6),
        size: new AMap.Size(15, 23)
    }, {
        url: '/static/images/gaode/2_1.png',
        anchor: new AMap.Pixel(4, 4),
        size: new AMap.Size(15, 23)
    }, {
        url: '/static/images/gaode/3_1.png',
        anchor: new AMap.Pixel(3, 3),
        size: new AMap.Size(15, 23)
    }, {
        url: '/static/images/gaode/4_1.png',
        anchor: new AMap.Pixel(3, 3),
        size: new AMap.Size(15, 23)
    }
    ];

    var mass = new AMap.MassMarks(citys, {
        opacity: 0.8,
        zIndex: 111,
        cursor: 'pointer',
        style: style
    });

    var marker = new AMap.Marker({content: ' ', map: map});
    var clickNumber =1;
    mass.on('click', function (e) {
        marker.setPosition(e.data.lnglat);
        // if(clickNumber %2==1){
        marker.setLabel({content: e.data.name})
        // }else{
        //     marker.setLabel({content: ""})
        // }
        clickNumber ++;

    });

    mass.setMap(map);

    function setStyle(multiIcon) {
        if (multiIcon) {
            mass.setStyle(style);
        } else {
            mass.setStyle(style[2]);
        }
    }
    function geoCode(address,i,type) {
        var geocoder,marker;
        if(!geocoder){
            geocoder = new AMap.Geocoder({
                city: "010", //城市设为北京，默认：“全国”
            });
        }
        var icon = new AMap.Icon({
            size: new AMap.Size(40, 50),    // 图标尺寸
            image: '//webapi.amap.com/theme/v1.3/images/newpc/way_btn2.png',  // Icon的图像
            imageOffset: new AMap.Pixel(0, 0),  // 图像相对展示区域的偏移量，适于雪碧图等
            imageSize: new AMap.Size(40, 50)   // 根据所设置的大小拉伸或压缩图片
        });
        // var address  = "北京市朝阳区阜荣街10号";
        // console.log(address['name2'])
        // console.log(citys4[i]['name2'])
        geocoder.getLocation(address['name2'], function(status, result) {
            if (status === 'complete'&&result.geocodes.length) {
                var lnglat = result.geocodes[0].location
                // if (type==2) {
                //     citys4[i]['lnglat']=[lnglat['lng'],lnglat['lat']];

                // }else{
                //     citys2[i]['lnglat']=[lnglat['lng'],lnglat['lat']];
                // }
                if(!marker){
                    marker = new AMap.Marker();
                    map.add(marker);
                }
                marker.setPosition(lnglat);

            }
        });
    }
    function feng(){
        marker.setLabel({content: ""})
    }
    // for(var i=0;i<citys2.length;i++){
    //     geoCode(citys2[i],i,2)
    //    // console.log(lnglat)
    // }
    // for(var i=0;i<citys4.length;i++){
    //     geoCode(citys4[i],i,4)
    //    // console.log(lnglat)
    // }
    // var csz = citys2.concat(citys4)
    // cs = citys.concat(csz)

</script>
</body>
</html>