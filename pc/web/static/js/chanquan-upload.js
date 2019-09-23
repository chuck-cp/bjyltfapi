
//《《《《《《《《《《《《《《《《图片上传
//初始化逻辑
//特别注意: JS-SDK使用之前请先到console.cloud.tencent.com/cos 对相应的Bucket进行跨域设置
//获取上传图片需要的大小
var uploadimgW=$('.upload').find('.imgspace').width();
var uploadimgH=$('.upload').find('.imgspace').height();
function replaceCosUrl(url){
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
        //上传必须是图片格式
        if(file.type.indexOf("image") < 0){
            $('.sy_tctskuan').show();
            return false;
        }
	    //更改文件名称
	    var imgname=file.name;
	    //获取上传的时间戳
	    timestamp = Date.parse(new Date());
	    filename=timestamp+imgname.substring(imgname.lastIndexOf("."),imgname.length);
		//上传成功回调
		var successCallBack = function (result) {
			//上传成功之后的追加
			var imgurl=replaceCosUrl(result.data.source_url);
			var imglocalname=imgname;
            $.ajax({
                url:"/property/copyright",
                type:"get",
                dataType:"json",
                data:{imgurl:imgurl,imglocalname:imglocalname},
                success:function(data) {
                    console.log(data);
                    if(data.status==200){
                        $('.yx_upimg_list').append('<div class="yx_cq_lb_xq" data-cqid="' + data.id + ('"><p class="yx_cq_img"><img src=' + imgurl + '></p><p class="yx_cq_name">' + imglocalname + '</p><p class="yx_cq_name_xg"><input type="text" value="' + imglocalname + '" name="name" class="YX_bjcqbt" /></p><p class="yx_bjcq_zt"><span class="yx_del_cqimg fl"><img src="">删除</span><span class="yx_cq_bc fl"><img src="/static/images/cqbj.png">保存</span></p><!--点击编辑--> <p class="yx_xz_bjcq"><input type="checkbox" name="" class="yx_xz_bjcq_input"><span class="yx_xz_bjcq_false"></span></p></li>'));
             //           $('.yx_upimg_list').append(`
             //<div class="yx_cq_lb_xq" data-cqid="`+data.id+`">
             //   <p class="yx_cq_img"><img src=${imgurl}></p>
             //   <p class="yx_cq_name">${imglocalname}</p>
             //   <p class="yx_cq_name_xg"><input type="text" value="${imglocalname}" name="name" class="YX_bjcqbt" /></p>
             //
             //  <p class="yx_bjcq_zt">
             //      <span class="yx_del_cqimg fl"><img src="">删除</span>
             //      <span class="yx_cq_bc fl"><img src="/static/images/cqbj.png">保存</span>
             //  </p>
             //  <!--点击编辑-->
             //  <p class="yx_xz_bjcq">
             //  	<input type="checkbox" name="" class="yx_xz_bjcq_input">
             //  	<span class="yx_xz_bjcq_false"></span>
             //  </p>
             //</li> `)
                    }
                }
            });



            $("#cq_edit").css("display","block");
		};
		//进度条回调
		var hd = function(curr){
		};
	    cos.uploadFile(successCallBack, errorCallBack, hd, bucket, myFolder+filename, file,0);
    });
}



