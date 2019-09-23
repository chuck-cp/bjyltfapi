/*申请安装选中*/
$('.sy_shoplist').live('click',function(){
	var selected=$(this).find('.unselected').hasClass('selected');
	if(!selected){
		$(this).find('.unselected').addClass('selected');
		$(this).find('.unselected input').prop('checked',true);
		$(this).siblings().find('.unselected').removeClass('selected');		
	}
})
/*申请安装分店信息*/
/*选中*/
$('.sy_shopaddress').live('click',function(){
	var selected=$(this).find('.unselected').hasClass('selected');
	if(!selected){
		$(this).find('.unselected').addClass('selected');
		$(this).find('.unselected input').prop('checked',true);
		$(this).siblings().find('.unselected').removeClass('selected');		
	}
})
/*弹框*/
function tankuang(classmame){
	$('.'+classmame).show();
	$(".mask").css('opacity','0.3').show(); 
	var ymheight=$(document).height()+ "px";
	$(".mask").css("height",ymheight);
}
/*带参数弹框*/
/*弹框*/
function tankuangparam(classmame,param){
	$('.'+classmame).show();
	$('.'+classmame).find('p').text(param)
	$(".mask").css('opacity','0.3').show(); 
	var ymheight=$(document).height()+ "px";
	$(".mask").css("height",ymheight);
}
/*关闭弹框*/
function closetankuan(classmam){
	$('.'+classmam).hide();
	$(".mask").hide(); 
}
$('.sy_waitins_close,.mask').click(function(){
	closetankuan('sy_waitins_choicetwo')
})
