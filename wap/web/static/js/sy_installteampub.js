/*=============================待安装店铺页面=============================*/
//input按钮切换
//$('.identifited input').live('click',function(){
//	var isselect=$(this).prop('checked');
//	if(isselect){
//		$(this).parent().removeClass('unselected').addClass('selected');
//	}
//	else{
//		$(this).parent().removeClass('selected').addClass('unselected');
//	}
//})

$('.sy_waitin_corpra').live('click',function(){
    var classname=$(this).find('span').attr('class');
    if(classname=='unselected'){
        $(this).find('span').removeClass('unselected').addClass('selected');
        $(this).find('input').prop('checked',true);
    }
    else{
        $(this).find('span').removeClass('selected').addClass('unselected');
        $(this).find('input').prop('checked',false);
    }
})


//$('.sy_waitins_tabcon input').live('click',function(){
//    var isselect=$(this).prop('checked');
//    if(isselect){
//        $(this).parent().removeClass('unselected').addClass('selected');
//        $(this).parents('tr').siblings().find('span').removeClass('selected').addClass('unselected');
//    }
//    else{
//        $(this).parent().removeClass('selected').addClass('unselected');
//        $(this).parents('tr').siblings().find('span').removeClass('unselected').addClass('selected');
//    }
//})

$('.sy_waitins_tabcon tr').live('click',function(){
    var classname=$(this).find('span').attr('class');
    if(classname=='unselected'){
        $(this).find('span').removeClass('unselected').addClass('selected');
        $(this).siblings('tr').find('span').removeClass('selected').addClass('unselected');
        $(this).find('input').prop('checked',true);
    }
})


$('.sy_waitins_close,.sy_waitins_doing .qx,.sy_installset_doing .qx').click(function(){
	closetankuan('sy_waitins_tabpanel');
	closetankuan('sy_waitins_choicetwo');   ////不可同时选择两种状态弹框关闭
	closetankuan('sy_waitins_doing');    //取消	
	closetankuan('sy_installset_choicetwo');
	closetankuan('sy_installset_doing');
})
/*弹框*/
function tankuang(classmame){
	$('.'+classmame).show();
	$(".mask").css('opacity','0.3').show(); 
	var ymheight=$(document).height()+ "px";
	$(".mask").css("height",ymheight);
}
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

/*弹框1*/
function tankuangparama(classmame,param){
	$('.'+classmame).show();
	$('.'+classmame).find('p').text(param)
	$(".sy_dzmask").css('opacity','0.3').show(); 
	var ymheight=$(document).height()+ "px";
	$(".sy_dzmask").css("height",ymheight);
}
/*关闭弹框1*/
function closetankuana(classmam){
	$('.'+classmam).hide();
	$(".sy_dzmask").hide(); 
}