
//《《《《《《《《《《《《《《《《图片上传
//初始化逻辑
//特别注意: JS-SDK使用之前请先到console.cloud.tencent.com/cos 对相应的Bucket进行跨域设置
//获取上传图片需要的大小
var uploadimgW=$('.upload').find('.imgspace').width();
var uploadimgH=$('.upload').find('.imgspace').height();


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
function uploadslt(url){
   // console.log(url)
//转换编码	
    function convertBase64UrlToBlob(urlData){

        var bytes=window.atob(urlData.split(',')[1]);        //去掉url的头，并转换为byte

        //处理异常,将ascii码小于0的转换为大于0
        var ab = new ArrayBuffer(bytes.length);
        var ia = new Uint8Array(ab);
        for (var i = 0; i < bytes.length; i++) {
            ia[i] = bytes.charCodeAt(i);
        }

        return new Blob( [ab] , {type : 'image/png'});
    }
    abc= convertBase64UrlToBlob(url);
    _this=this;
    var file = abc;
    //更改文件名称
    var imgname="a.jpg";
    //获取上传的时间戳
    timestamp = Date.parse(new Date());
    filename=timestamp+imgname.substring(imgname.lastIndexOf("."),imgname.length);
    //上传成功回调
    var successCallBack = function (result) {
       // console.log(result.data)
        //上传成功之后的追加
        var imgurl=result.data.source_url;
        //var imglocalname=imgname;
        $('#videoimg').val(imgurl);
        $('#crspslt').append('<li><img  src=' + imgurl + '></li>');
        //$('#crspslt').append(`<li>
        //<img  src=${imgurl}>
        //</li>`)
    };
    //进度条回调
    var hd = function(curr){
    };

    cos.uploadFile(successCallBack, errorCallBack, hd, bucket, myFolder+filename, file,0);
}



