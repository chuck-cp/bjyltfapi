//手机号中间四位变成*
$(function(){
	var pHo=$("#yx_yhphone").html();
	var pHoth=pHo.length-3;
	var str1=pHo.substr(0,4);
	var str2=pHo.substr(pHoth);
	$('#yx_yhphone').html(str1+"****"+str2)
})
//安装信息提交成功倒计时
var countdown=30; 
function settime(){	
	if (countdown == 0) { 
		countdown = 30; 
		$(".yx_djs_time").html(0);
		return false;
	} else { 
		$(".yx_djs_time").html(countdown);
		countdown--; 
	} 
	setTimeout(function() {settime()},1000)
}
settime()
