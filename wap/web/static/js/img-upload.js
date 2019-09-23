//《《《《《《《《《《《《《《《《图片上传
//初始化逻辑
//特别注意: JS-SDK使用之前请先到console.cloud.tencent.com/cos 对相应的Bucket进行跨域设置
//和安卓交互的上传接口
//点击上传

function upload_start(id,staticurl){
	var elemeapp=$('#'+id);
	elemeapp.find('.sy_bgzsytp').remove();
	//判断是否有小括号
	if(staticurl.substr(0,1)=='('){
		staticurl=staticurl.substr(1,staticurl.length)
	}
	var staticurl='data:image/jpeg;base64,'+staticurl;	
    elemeapp.find('.idcardzfti').hide();
    elemeapp.find('.progress-bar').show();
    //elemeapp.find('.addimg').attr("src",staticurl);
    elemeapp.find('.sc_fail_ts').remove(); 
	elemeapp.find('.addimg').after('<div class="sy_bgzsytp"></div>');
	elemeapp.append("<p></p>")
	elemeapp.find('.sy_bgzsytp').css({
     "background-color":"#fff",
	 "background-image":"url("+staticurl+")",
	 "background-repeat":"no-repeat",
	 "background-position":"center center",
	 "background-size":"100% auto"
	})        
}
//上传失败
function upload_fail(id){
	var elemeapp=$('#'+id);
	elemeapp.find('.sy_bgzsytp').remove();
    elemeapp.find('.progress-bar').hide();
    elemeapp.find('.progress-bar').css('width',0);
    elemeapp.find('.progress-bar').css('background','#ea9061');
    elemeapp.find('.addimg').attr("src","/static/image/uploadimg-add.png");
    elemeapp.find('.idcardzfti').show();
    elemeapp.append('<p class="sc_fail_ts">上传失败请重新上传</p>');
}
//上传完成
function upload_end(id,url){
	var tagid="#"+id;
    $(tagid).find('.update_input').val(replaceCosUrl(url));
    $(tagid).find('.progress-bar').css('background','#ea9061');
    setTimeout(function(){
        $(tagid).find('.progress-bar').hide();
        $(tagid).find('.progress-bar').css('width',0);
    }
    ,500);    
}

var ad_uploadimgW=$('.upload').width();
function upimg_progress(id,pro){
    /*获取进度条总宽度*/
    pro = parseInt(pro) / 100;
    var progress=ad_uploadimgW*pro;
    $('#'+id).find('.progress-bar').width(progress);    
}
function replaceCosUrl(url){
	if (url == '') {
		return false
	}
    var uploadimgW=$('.upload').find('.imgspace').width();
    var uploadimgH=$('.upload').find('.imgspace').height();
    var orignSrc=url;
    var changSrc='https://i1.bjyltf.com';
    //var addimgWH='?imageView2/1/w/'+uploadimgW+'/h/'+uploadimgH;
    if(orignSrc.indexOf(".com/")>0){
        orignSrc=orignSrc.substring(orignSrc.lastIndexOf(".com/")+4,orignSrc.length);
    }
    //替换的url
    var newUrl=changSrc+orignSrc;
    return newUrl;
}
function randomString(len) {
    len = len || 32;
    var $chars = '_-+*#@{}[]^ABCDEFGHJKMNPQRSTWXYZabcdefhiojkmnprstwxyz1234567890';
    var maxPos = $chars.length;
    var pwd = '';
    for (i = 0; i < len; i++) {
        pwd += $chars.charAt(Math.floor(Math.random() * maxPos));
    }
    return pwd;
}

function getRandom(n,m){
    var c = m-n+1;
    return Math.floor(Math.random() * c + n);
}

function getFileName(){
    var date = new Date();
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();
    var hour = date.getHours() < 10 ? "0" + date.getHours() : date.getHours();
    var minute = date.getMinutes() < 10 ? "0" + date.getMinutes() : date.getMinutes();
    var second = date.getSeconds() < 10 ? "0" + date.getSeconds() : date.getSeconds();
    return year.toString() + month.toString() + day.toString() + hour.toString() + minute.toString() + second.toString() + getRandom(100000,999999);
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

var errorCallBack = function (result) {
    $('.sy-installed-ts').text("上传失败");
    tippanel();
};

//上传失败h5
function h5upload_fail(id){
	  var elem=$('#'+id);
	  elem.siblings('.sy_bgzsytp').remove();
	  elem.siblings('.progress-bar').hide();
      elem.siblings('.progress-bar').css('width',0);
      elem.siblings('.progress-bar').css('background','#ea9061');
      elem.siblings('.addimg').attr("src","/static/image/uploadimg-add.png");
      elem.siblings('.idcardzfti').show();
      elem.parent().append('<p class="sc_fail_ts">上传失败请重新上传</p>');      
}
//h5上传时显示本地图片
function h5localimg(id,staticurl){
	var eleme=$('#'+id);
	eleme.siblings('.sy_bgzsytp').remove();
	//eleme.siblings('.addimg').attr("src",staticurl);
	eleme.siblings('.idcardzfti').hide();
	eleme.siblings('.addimg').after('<div class="sy_bgzsytp"></div>');
	eleme.siblings('.sy_bgzsytp').css({
     "background-color":"#fff",
	 "background-image":"url("+staticurl+")",
	 "background-repeat":"no-repeat",
	 "background-position":"center center",
	 "background-size":"100% auto"
	});
}
//转换编码 canvas合成图转换编码
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
//二进制输出流字节转换
function binaryConversion(binstr){
	var binstr=binstr.substring(22);
	var equalIndex= binstr.indexOf("=");
	if(binstr.indexOf("=")>0)
	{
	    binstr=binstr.substring(0, equalIndex);	
	}
	var strLength=binstr.length;
	var fileLength=parseInt(strLength-(strLength/8)*2);
	return fileLength	
}
//图片上传宽度限制
widthlimit=1000;
function uploadimg(name){
	$('.'+name).off('change').on('change', function (e) {
	    var _this=this;
	    var upingid=$(_this).attr('id');
	    $('#'+upingid).parent().find('.sc_fail_ts').remove()
        var file = e.target.files[0];
        var img_watermsk_src='/static/image/watermarkpicture.png';  //水印图片地址
        //获取水印的高度和宽度
		var img_wm_w=511;
		var img_wm_h=75;
        windowURL = window.URL || window.webkitURL;
        var img_url = URL.createObjectURL(file);
        var imgwh = new Image();    
        var base64=[];
        var data=[img_url,img_watermsk_src];        
        var canvas = document.createElement("canvas"),
        ctx = canvas.getContext('2d');  
        EXIF.getData(file, function() { 
            Orientation = EXIF.getTag(this, 'Orientation');
        });
        imgwh.onload = function () {
            var imgWidth = this.width,
            imgHeight = this.height;           
            canvas.width = imgWidth;
            canvas.height = imgHeight; 
            if(Orientation && Orientation != 1){    //竖着有旋转情况  
                switch(Orientation){
                    case 6:     // 旋转90度                          
                        canvas.width = imgHeight;    
                        canvas.height = imgWidth;                        
                        ctx.rotate(Math.PI / 2); 
                        ctx.fillStyle="#fff";
                        ctx.fillRect(0, -imgHeight,imgWidth, imgHeight);                          
                        ctx.drawImage(this, 0, -imgHeight);	                        
                        break;
                    case 3:     // 旋转180度
                        ctx.rotate(Math.PI); 
                        ctx.fillStyle="#fff";
                        ctx.fillRect(-imgWidth, -imgHeight, imgWidth, imgHeight);
                        ctx.drawImage(this, -imgWidth, -imgHeight);
                        break;
                    case 8:     // 旋转-90度
                        canvas.width = imgHeight;    
                        canvas.height = imgWidth;    
                        ctx.rotate(3 * Math.PI / 2);                                                                        
                        ctx.fillStyle = "#fff";
                        ctx.fillRect(-imgWidth, 0, imgWidth, imgHeight);
                        ctx.drawImage(this, -imgWidth, 0, imgWidth, imgHeight);
                        break;                    
                }                              
                var img = new Image();
                img.src = canvas.toDataURL("image/jpeg", 0.8);  //转换为图片形式  打印二进制数据
			    rotateimg= convertBase64UrlToBlob(img.src);  //打印Bob对象
			    //转换编码                    
                var rotateSrc=URL.createObjectURL(rotateimg);
                var datar=[rotateSrc,img_watermsk_src];//新生成图片 与水印图片合成
                //对新旋转的图片进行添加水印
                var rotateimg=new Image()
                rotateimg.src = rotateSrc;
                rotateimg.onload=function(){
                	//获取新生成的图片的宽高
                    var rotimgW=rotateimg.width;
	                var rotimgH=rotateimg.height;
	                //如果宽度大于1000 则对新生图片大小进行缩放
	                if(rotimgW>1000){	            		            	
		            	var ratiolimit=widthlimit/rotimgW;
	            	    var rotimgH=ratiolimit*rotimgH;
	            	    var rotimgW=1000	            	
	                }
               	    var proport=rotimgW*0.8/img_wm_w;	//设置水印的宽度比例求水印高
		            var shuiyinW=rotimgW*0.8;	//设置水印的宽度
		            var shuiyinH=img_wm_h*proport;
		            //设置left和top值
		            var left=shuiyinW*0.1;
		            var top=(rotimgH-shuiyinH)/2;
		            function addwatermask(){//水印添加
		                draw(function(){
		                    var rotimg_url=base64[0];
		                    if (navigator.onLine) { //判断是否有网
		                    	h5localimg(upingid,rotimg_url);
		                    } 		                    		                    
		                    wmpicup(rotimg_url,_this);
		                })
		            }addwatermask()
		            function draw(fn){
		                var c=document.createElement('canvas'),
		                ctx1=c.getContext('2d');
		                len=datar.length;
		                c.width=rotimgW;
		                c.height=rotimgH;
		                ctx1.rect(0,0,rotimgW,rotimgH);
		                ctx1.fillStyle='#fff';
		                ctx1.fill();
		                function drawing(n){
		                    if(n<len){
		                        var img=new Image;
		                        img.crossOrigin = 'Anonymous';
		                        img.src=datar[n];
		                        img.onload=function(){
		                            if(n==1){
		                                ctx1.drawImage(img,left,top,shuiyinW,shuiyinH);
		
		                            }
		                            else{
		                                ctx1.drawImage(img,0,0,rotimgW,rotimgH);
		                            }
		                            drawing(n+1);//递归
		                        }
		                    }else{
		                       //保存生成作品图片
		                       var originalquality=parseInt(binaryConversion(c.toDataURL("image/jpeg",1)))/1024;
	                           var compressquality=c.toDataURL("image/jpeg",1)
		                       if(originalquality>500){
		                        	var compressquality=c.toDataURL("image/jpeg",0.8);
		                        	var originalquality=parseInt(binaryConversion(c.toDataURL("image/jpeg",0.8)))/1024;
		                       }
		                       base64.push(compressquality);
			                   fn();
		                    }
		                }
		                drawing(0);
		            }			                               	
               }		            	            	
              }else{
               	var timestamp = Date.parse(new Date());
	            var orgimgW=imgwh.width;
	            var orgimgH=imgwh.height;
	            //判断上传的宽度是不是大于1000 如果大于1000则等比例缩小
	            if(orgimgW>1000){  //widthlimit	            		            		
	            	ratiolimit=widthlimit/orgimgW;
	            	orgimgH=ratiolimit*orgimgH;
	            	orgimgW=1000	            	
	            }	            
	            //获取宽度比值
	            var proport=orgimgW*0.8/img_wm_w;	//设置水印的宽度比例求水印高
	            var shuiyinW=orgimgW*0.8;	//设置水印的宽度
	            var shuiyinH=img_wm_h*proport
	            //设置left和top值
	            var left=shuiyinW*0.1;
	            var top=(orgimgH-shuiyinH)/2;
	            function addwatermask(){
	                draw(function(){
	                    var img_url=base64[0];
	                    if (navigator.onLine) {
		                  h5localimg(upingid,img_url);
		                }else{
		                  h5upload_fail(upingid);
		                }
	                    wmpicup(img_url,_this);
	                })
	            }addwatermask()
	            function draw(fn){
	                var c=document.createElement('canvas'),
	                ctx=c.getContext('2d');
	                len=data.length;
	                c.width=orgimgW;
	                c.height=orgimgH;
	                ctx.rect(0,0,orgimgW,orgimgH);
	                ctx.fillStyle='#fff';
	                ctx.fill();
	                function drawing(n){
	                    if(n<len){
	                        var img=new Image;
	                        img.crossOrigin = 'Anonymous';
	                        img.src=data[n];
	                        img.onload=function(){
	                            if(n==1){
	                                ctx.drawImage(img,left,top,shuiyinW,shuiyinH);
	
	                            }
	                            else{
	                                ctx.drawImage(img,0,0,orgimgW,orgimgH);
	                            }
	                            drawing(n+1);//递归
	                        }
	                    }else{
	                        //保存生成作品图片 如图片质量大于500k那么进行压缩                      
	                        var originalquality=parseInt(binaryConversion(c.toDataURL("image/jpeg",1)))/1024;
	                        var compressquality=c.toDataURL("image/jpeg",1);	                        
	                        if(originalquality>500){
	                        	var compressquality=c.toDataURL("image/jpeg",0.8);
	                        }
	                        base64.push(compressquality);	                        	                        
	                        fn();
	                    }
	                }
	                drawing(0);
	            }		            		               	
             }        
       }       
	   imgwh.src = img_url;
	})	
}
//水印图片上传
uploadimgW=$('.upload').width();
function wmpicup(img_url,targt){	
    var shuiyin= convertBase64UrlToBlob(img_url);
    var _this=targt;
    var file = shuiyin;
    //更改文件名称
    var imgname="a.jpg";
    //获取上传的时间戳
    var class_id=$(_this).attr('id');
    var timestamp = Date.parse(new Date());
    var filename=getFileName()+imgname.substring(imgname.lastIndexOf("."),imgname.length);        
    $('#'+class_id).siblings('.progress-bar').show();
    $('#'+class_id).siblings('.progress-bar').attr('step',0);
    var successCallBack = function (result) {
    	//获取上传成功 上传图片的id值
        $('#'+class_id).siblings('.progress-bar').css('background','#ea9061');
        $('#'+class_id).siblings('.progress-bar').width(uploadimgW)
        setTimeout(function(){
                $('#'+class_id).siblings('.progress-bar').hide();
                $('#'+class_id).siblings('.progress-bar').css('width',0);
                $('#'+class_id).siblings('.progress-bar').css('background','#b3a6c4');
            }
        ,500);    	       	    	    	
        $('#'+class_id).siblings('.update_input').attr('value',replaceCosUrl(result.data.source_url));                        
    }
    var errorCallBack = function (result) {
        result = result || {};
        h5upload_fail(class_id);        
    }
    var progressCallBack = function(curr){
        var step = $('#'+class_id).siblings('.progress-bar').attr('step');        
        if(step == 0){   	    
            //$(_this).siblings('.addimg').attr("src","/static/image/load1ing.gif");
            $('#'+class_id).siblings('.progress-bar').attr('step',1);            
        }
        var progress=uploadimgW*curr;
        $('#'+class_id).siblings('.progress-bar').width(progress)
    };
    cos.uploadFile(successCallBack, errorCallBack, progressCallBack, bucket, myFolder+filename, file,0);
    return false;
}

