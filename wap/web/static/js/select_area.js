/**
 * jquery.area.js
 * 移动端省市区三级联动选择插件
 *
 *areaCont 城市列表
 *expressArea 显示在ipnut内的地址
 *areaList 显示的列表内容]
 *djcs 点击个数
**/
/*定义三级省市区数据*/
//获取传入地址
$(function(){
    baseApiUrl= $("#chuaru_dz").html();
})

var expressArea="", areaCont, areaList = $("#areaList"),areaBox=$('#areaBox');
var djcs=0;
var csid=[0];
var zhdjcs=""
/*初始化省份*/
function intProvince(id,name) {
   	areaCont = "";
    $.ajax({
        type: "GET",
        url: baseApiUrl+"/area?parent_id="+id,
        success:function(data){
            $.each(data.data,function(i,item){
                areaCont += '<li onClick="selectP(' + item.id + ',\''+item.name+'\');">' + item.name + '</li>';
            })
            areaList.html(areaCont);
            areaBox.prop('scrollTop',0);
        },
        error:function(data){
           // $('.sy-installed-ts').text('地区获取失败');
            //tippanel();
        }
    });
    return name;

}

intProvince(101,'中国');

/*选择城市*/
function selectP(id,name) {
    zhdjcs=String(csid[csid.length-1])
    var bb=String(zhdjcs)
    var aa=String(id)
    if(aa.length!=bb.length){
        csid.push(id);
        djcs+=1;
        if (djcs==4){
            expressArea += name;
            clockArea();
            djcs=0;
            $("#"+thwz).find(".select_dizhi").html(expressArea);
            //公司id传参
            if(thwz=="xianzhu_area"){
                $("#xianzhu_id").val(id);
            }
            if(thwz=="gongsi_area"){
                $("#gongsi_id").val(id);
            }
            //公司id传参 end
            expressArea="";
            $.ajax({
                type: "GET",
                url: baseApiUrl+"/system/brokerage?area_id="+id,
                success:function(data){
                    var price = data.data.price;
                    var token = data.data.token;
                    var month_price = data.data.month_price;
                    $("#area").val(id);
                },
                error:function(data){
                   // $("#"+thwz).find("textarea").val('');
                    //$('.sy-installed-ts').text('独家买断费用获取失败');
                   // tippanel();
                }
            });
        }else{
            expressArea += name + " > ";

            intProvince(id,name)
        }

    }
}

//选择城市
function zxcs(djdiv){
    thwz=djdiv;
	$("#areaMask").fadeIn();
	$("#areaLayer").animate({"bottom": 0}).show();
    var ksfw=$(window).width();
    if(ksfw<700){
      stop();
    }else{
         $("body").css({overflow:"hidden"})
    }
}
/*关闭省市区选项*/
$(function() {
	$("#areaMask, #closeArea").click(function() {
        expressArea = "";
        djcs=0;
        csid=[0];
        zhdjcs=""
        clockArea();
	});
});

function clockArea() {
	$("#areaMask").fadeOut();
    $("#areaLayer").animate({"bottom": "-100%"}).hide();
     var ksfw=$(window).width();
    if(ksfw<700){
       move();
    }else{
         $("body").css({overflow:"visible"})
    }
	intProvince();
}

/***禁止滑动***/
function stop(){
    scrollTop=($(window).scrollTop() || $("body").scrollTop());
    $('body').addClass('modal-open');
    document.body.style.top = -scrollTop + 'px';

}

/***取消滑动限制***/
function move(){
    $('body').removeClass('modal-open');
    scrollTop=($(window).scrollTop() || $("body").scrollTop());
}


