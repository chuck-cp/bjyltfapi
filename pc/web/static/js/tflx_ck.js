$(function (){
var xztf_id=[];
	$(".yx_choice_dqdd").click(function(){	
		var i=$(this).parents("tr").siblings().length;
		var m=$(".yx_dqdd_lb").find(".yx_choice_dqdd:checked").length;
		if(i==m){
			$(this).parents(".yx_dqdd_lb").find(".yx_choice_dqdd_qx").attr("checked",true);
		}else{
			$(this).parents(".yx_dqdd_lb").find(".yx_choice_dqdd_qx").attr("checked",false);
		}
	});
	// //删除
	// var img_id=[];
	// $(".cqslt").each(function(){
	// 	//console.log($(this).children().eq(0).children().eq(1));
	// 	$(this).children().eq(0).children().eq(1).click(function(){
	// 		$(this).parent().attr("style","display:none");
	// 		add = $(this).attr("data");
	// 		img_id.push(add);
	// 	});
	// 	alert(img_id);
	// })
	//全选
	$(".yx_choice_dqdd_qx").click(function (){

		$(".yx_dqdd_lb table input:checkbox").prop("checked",$(this).prop('checked'));

	})
	//取消
	$(".yx_choice_qxcq").click(function (){
		guanbi()
	})
	$("#gb_tfls_lb").click(function (){
		guanbi()
	})
	//将此广告资料复制至新订单中
	$(".yx_copy_zl").click(function (){
		$(".yx_tfls_ddzs").show();
		$(".yx_dqdd_lb").find("input").attr("checked",false);
	})
	//确定按钮  此处调取代码
	$(".yx_choice_tjcq").click(function (){
		$('.yx_dqdd_lb').find(':checkbox').each(function(){
		  if ($(this).is(":checked")) {
			  if($(this).attr("class")=="yx_choice_dqdd"){
					ddhao=$(this).parent().siblings(".tf_dd_id").html()
					xztf_id.push(ddhao);
			  }
		  }
		  guanbi();
		});
		var info_id = $('#info_id').html();
		var paths = '/history/ordernew';
		$.ajax({
			type: "get",
            url: paths,
            data: {order_code:xztf_id,info:info_id},
            success: function(data) {
            	shtcts(data)
            	//guanbi();
            }
		});
		//alert(222222);
		xztf_id=[];
		//alert(resource);
			
	})
	
	
})
function guanbi(){
	$(".yx_tfls_ddzs").hide();	
	xztf_id=[];	
};
function shtcts(data){
	if(data=="成功"){
		$(".sc_ture").show();
	}else{
		$(".sc_false").show();
	}
	$(".yx_sccg_ts").show();
};
$(function(){
	$("#scdb_qding").click(function (){
		$(".yx_sccg_ts").hide();
	});
	$(".scdb_qding").click(function (){
		$(".yx_sccg_ts").hide();
	});
})