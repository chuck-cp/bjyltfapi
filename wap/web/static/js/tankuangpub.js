/**
 * Created by Administrator on 2018/8/18 0018.
 */
//提示信息确认弹框
/*弹框*/
function conftankuangparam(classmame,param){
    $('.'+classmame).show();
    $('.'+classmame).find('.con').text(param)
    $(".mask").css('opacity','0.3').show();
    var ymheight=$(document).height()+ "px";
    $(".mask").css("height",ymheight);
}
/*关闭弹框*/
function closetankuan(classmam){
    $('.'+classmam).hide();
    $(".mask").hide();
}