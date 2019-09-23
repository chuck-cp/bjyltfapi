
//《《《《《《《《《《《《《《《《图片上传
//初始化逻辑
//特别注意: JS-SDK使用之前请先到console.cloud.tencent.com/cos 对相应的Bucket进行跨域设置
//获取上传图片需要的大小
var uploadimgW=$('.upload').find('.imgspace').width();
var uploadimgH=$('.upload').find('.imgspace').height();
function replaceCosUrlB(url){
    if (url == '') {
        return false;
    }
    var orignSrc=url;
    var changSrc='http://i1.bjyltf.com';
    if(orignSrc.indexOf(".com/")>0){
        orignSrc=orignSrc.substring(orignSrc.lastIndexOf(".com/")+4,orignSrc.length);
    }
    //替换的url
    var newUrl=changSrc+orignSrc;
    return newUrl;
}

var szloading=false;

var myFolder = "/guanggao"+$('#myFolder').val();//'/member/advert';//需要操作的目录

var errorCallBack = function (result) {
    result = result || {};
    $("#result").val(result.responseText || 'error');
    console.log('上传失败');
};
function uploadimg(name){
	$('.'+name).val('');
	$('.'+name).off('change').on('change', function (e) {
		_this=this;
	    var file = e.target.files[0];
	    //获取图片名称
	    var imgname=file.name;
	    //console.log(file);
	    //限制360*1080    最大支持5M     格式JPG
        //上传必须是图片格式
        if(file.type.indexOf("image") < 0){
            $('.scttsk_nr').text('上传文件应是图片格式!');
            $('.yx_tctskuan').show();
            return false;
        }
        $('#videoSize').val(file.size);
        //sha1File(file);
        /*$('#videoSha1').val(sha1(file.toString()));*/
        var file_size=file.size/1024/1024;
        var img_url = URL.createObjectURL(file);
		// 创建对象
		var imgwh = new Image();
		// 改变图片的src
		imgwh.src = img_url;

		//更改文件名称
	    //获取上传的时间戳
	    timestamp = Date.parse(new Date());
	    filename=timestamp+imgname.substring(imgname.lastIndexOf("."),imgname.length);
		//获取不同的data-type值 成功之后分别作不同的操作
        var data_type=$(_this).attr('data-type');
        //console.log(data_type);
        if(data_type=='type1'){
            var cos = new CosCloud({
                appid: '1252719796',// APPID 必填参数
                bucket: 'yulong',//bucketName 必填参数
                region: 'bj',//地域信息 必填参数 华南地区填gz 华东填sh 华北填tj
                getAppSign: function (callback) {//获取签名 必填参数
                    $.ajax(baseApiUrl+'/qcloud-material').done(function (data) {
                        console.log(data)
                        var sig = data.token;
                        callback(sig);
                    });
                },
                getAppSignOnce: function (callback) {//单次签名，必填参数，参考上面的注释即可
                }

            });
            /*var bucket='yulong';*/
            var bucket='yulong';
        	//console.log(11111);
        	// 加载完成执行获取宽高并进行判断
		   imgwh.onload = function(){
               if(imgwh.width!=360||imgwh.height!=1080||file_size>5){
                   $('.scttsk_nr').text('B屏图片格式为JPG、JPEG、PNG   360*1080  5M以内');
                   $('.yx_tctskuan').show();
                   return false;
               }
		    //上传成功回调
			var successCallBack = function (result) {
				console.log(result);
				$('.yx_djsc').hide();
                $('.sy_upimg_position').show();
				//上传成功之后的追加
				   var imgurl=result.data.source_url;
                    $('#duration').val("30");//图片播放时长30;
                    $('#videoName').val(imgname);
                    $('#videoUrl').val(imgurl);
                    $('#videoimg').val(imgurl);
					$('.bdspingmc').html('<input id=\"videoNameB\" type=\"text\" value=\"'+imgname+'\">');
					//console.log(imgurl)
					$('.sy_upimg_position .img-posit').attr('src',imgurl);
					$('.sy_upimg_position').show();


			};
			var hd = function(curr){}
        	cos.uploadFile(successCallBack, errorCallBack, hd, bucket, myFolder+filename, file,0);
		    };
        }
        if(data_type=='type2'){//版权上传
            var cos = new CosCloud({
                appid: '1255626690',// APPID 必填参数
                bucket: 'yulongchuanmei',//bucketName 必填参数
                region: 'sh',//地域信息 必填参数 华南地区填gz 华东填sh 华北填tj
                getAppSign: function (callback) {//获取签名 必填参数
                    $.ajax(baseApiUrl+'/qcloud').done(function (data) {
                        var sig = data.token;
                        callback(sig);
                    });
                },
                getAppSignOnce: function (callback) {//单次签名，必填参数，参考上面的注释即可
                }

            });
            var bucket='yulongchuanmei';
        	//上传成功回调
		var successCallBack = function (result) {
            console.log(result);
			//上传成功之后的追加
			var imgurl=replaceCosUrlB(result.data.source_url);
			var imglocalname=imgname;
            $.ajax({
                url:"/property/copyright",
                type:"get",
                dataType:"json",
                data:{imgurl:imgurl,imglocalname:imglocalname},
                success:function(data) {
                    console.log(data);
                    if(data.status==200){

                    }
                }
            });
            $('.sy_upimg_list ul').append('<li><img src=' + imgurl + '><p>' + imglocalname + '</p>');
			//$('.sy_upimg_list ul').append(`<li>
     	  	//   	 	<img src=${imgurl}>
     	  	//   	 	<p>${imglocalname}</p>
     	  	//</li>`);
		};
		var hd = function(curr){}
	    cos.uploadFile(successCallBack, errorCallBack, hd, bucket, myFolder+filename, file,0);
      }
    });
}



