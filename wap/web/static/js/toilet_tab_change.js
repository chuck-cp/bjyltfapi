/* 表单验证 */
var verify_bool = false;//验证是否成功
var setup = 1 ;//表单tab值
var sel_tab = null;
function each_ver () {	
	//验证表单	
	$('#list_box .cur_show input').each(function(){	
		if ($(this).val() == '') {
			var cur_ver_placeholder = $(this).attr('placeholder');					
			if ($(this).attr('focus_bool') == 'false') {  //是否获取焦点
				$('.sy-installed-ts').text(cur_ver_placeholder);
				tippanel();
				$(this).blur();						
			}else if ($(this).attr('optional_bool') == 'true' ) {	//是否选填
				return false;
			}else{
				$('.sy-installed-ts').text(cur_ver_placeholder);
				tippanel();
				$(this).focus();
			}
			verify_bool = false;
			return false; 
			
		}else if ($(this).val()) {
			verify_bool = true;
		}
	});	
}
function tab_change (event) {
	//切换表单
	var cur_index = $(event.currentTarget).index();
		sel_tab = cur_index;			
	$(event.currentTarget).addClass('cur_def').siblings().removeClass('cur_def');
	$('#list_box .item_li').eq(cur_index).addClass('cur_show').siblings().removeClass('cur_show').addClass('cur_hide');
}
function new_tab () {
	//新建表单
	$(".cur_box").animate({scrollLeft:"0"});
	$('#list_box li').removeClass('cur_show');
	$('.cur_box span').removeClass('cur_def').first().addClass('cur_def');
	// $('.cur_box').append('<span >安装信息'+setup+'</span>');//顺序
	$('<span>安装信息'+setup+'</span>').insertAfter(".cur_def");//倒序
	setup++;
	var clone_con = $('#clone .item_li').clone(true);
	$('#list_box').prepend(clone_con);
}	
$('.cur_box span').live('click',function (event) {
	//tab切换按钮
	each_ver ();
	if (true == verify_bool) {
		// 验证通过，切换表单
		tab_change(event);
	}
});
$('.add_span').live('click',function (event) {
	//新建表单按钮
	each_ver ();
	if (true == verify_bool) {
		// 验证通过，新建表单
		new_tab ();
	}
});
/* 楼层选择 */
var cur_floor_index = null;//当前索引
var floor_sel_txt = "";
var floor_sel_setup = 0;
function floor_fun () {	
	//生成楼层数量
	for (floor_sel_setup; floor_sel_setup < 16; floor_sel_setup++) {
		if (0 == floor_sel_setup) {
			continue;
		}
		floor_sel_txt += "<input class='floor_btns' type='button' value="+floor_sel_setup+">";
	}
	$('.floor_con').append(floor_sel_txt);
	floor_sel_txt = "";
}
function floor_init () {
	//重置楼层所有状态
	$('.floor_btns[checked]').removeClass('cur_floor').attr('checked',false);
}
$('.floor_type').live('click',function () {
	floor_fun ();
	cur_floor_index = $(this).parents('.item_li').index();
	$('.mask_box').show();
	$('.floor_sel_box').show();
	$('.floor_con .floor_btns').toggle(function () {			
		$(this).addClass('cur_floor');
		$(this).attr('checked',true);
	},function () {
		$(this).removeClass('cur_floor');
		$(this).attr('checked',false);
	})
	
});
$('.floor_btn').live('click',function () {
	 var floor_count = [];
	$('.floor_btns[checked]').each(function(){
		floor_count.push($(this).val())
	});
	$('#list_box .item_li').eq(cur_floor_index).find('.floor_no').val(floor_count);//赋值
	$('.floor_sel_box,.mask_box').hide();
	floor_init ();
});
$('.floor_close').live('click',function () {
	$('.floor_sel_box,.mask_box').hide();
	floor_init ();
});
/* 选择设备类型 */

// var cur_list_index = null;//当前的.item_li 索引值
var self_me;//当前.equipment_type
$('#list_box .equipment_type').live('click',function () {//显示	
	cur_list_index = $(this).parents('.item_li').index();
	self_me = $(this);
	$('.mask_box').show();		
	$('body').addClass('modal-open');
	$('.equipment_box').show();
	$('.equipment_box').stop().animate({bottom:"0"});
	$('.equipment_list li').live('click',function () {
		var user_result = $(this).text();
		// $('#list_box .equipment_type').eq(cur_list_index).find('.user_result').val(user_result);	//取值
		self_me.find('.user_result').val(user_result);
		$('.equipment_box').stop().animate({bottom:"-240px"},function () {
			$('.equipment_box').hide();
			$('.mask_box').hide();
			$('body').removeClass('modal-open');
		});
		
	})
});
$('.cancel_btn').live('click',function () {//取消
	$('.equipment_box').stop().animate({bottom:"-240px"},function () {
		$('.equipment_box').hide();
		$('.mask_box').hide();
		$('body').removeClass('modal-open');
	});
	
});	
$('.mask_box').live('click',function () {		
	$('.equipment_box').stop().animate({bottom:"-240px"},function () {
		$('.equipment_box').hide();
		$('.mask_box').hide();
		$('.floor_sel_box').hide();
		$('body').removeClass('modal-open');
	});
	
})