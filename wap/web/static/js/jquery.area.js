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
var url = self.location.href;
var startW = url.lastIndexOf('/');
var endW = url.indexOf('?');
var action = url.slice(startW+1,endW);
var actionArr = ['head-office-create','branch-install','branch-install-modify','modify-shop','head-office-modify','inner-create'];
var isHk = $.inArray(action,actionArr);
var hkout = 0;
if(isHk > -1){
    hkout = 1;
}



var expressArea="", areaCont, areaList = $("#areaList"),areaBox=$('#areaBox');
var djcs=0;
var csid=[0];
var zhdjcs="";
/*初始化省份*/
function intProvince(id,name) {
	areaCont = "";
    $.ajax({
        type: "GET",
        url: baseApiUrl+"/area?parent_id="+id+"&hkout="+hkout,
        success:function(data){
            $.each(data.data,function(i,item){
                areaCont += '<li onClick="selectP(' + item.id + ',\''+item.name+'\');">' + item.name + '</li>';
            })
            areaList.html(areaCont);
            areaBox.prop('scrollTop',0);
        },
        error:function(data){
            $('.sy-installed-ts').text('地区获取失败');
            tippanel();
        }
    });    
    return name;    
}
intProvince(101,'中国');

/*选择城市*/
function selectP(id,name) {
    //for(key in csid){
    //    if(csid[key].toString().length == id.toString().length){
    //        return false;
    //    }
    //}
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
            $("#"+thwz).find(".yx_srnr_dqu").html(expressArea);
            $("."+thwz).find(".yx_srnr_dqu").html(expressArea);
            $(".yx_srnr_dqu").css({"line-height":"16px","height":"35px","color":"#333"});
            $(".yx_anzdz").css("height","90px");
            expressArea="";
            if (thwz == 'activity_install_area') {
                $("#area_id").val(id);
            } else {
                $.ajax({
                    type: "GET",
                    url: baseApiUrl+"/system/brokerage?area_id="+id+'&screen_number=1',
                    success:function(data){
                        var price = data.data.price;
                        var token = data.data.token;
                        var month_price = data.data.month_price;
                        $("#area").val(id);
                        // $("#apply_brokerage_price").val(price/100);
                        // $("#apply_brokerage_price").html(price/100);
                        // $("#month_price").val(month_price/100);
                        // $("#month_price").html(month_price/100);
                        // $("#apply_brokerage_token").val(token);
                        // $("#apply_brokerage").val(price);
                    },
                    error:function(data){
                        $("#"+thwz).find("textarea").val('');
                        $("."+thwz).find("textarea").val('');
                        $('.sy-installed-ts').text('独家买断费用获取失败');
                        tippanel();
                    }
                });
            }
        }else{
            expressArea += name + " > ";
            intProvince(id,name)
        }

    }
}
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
    //scrollTop = document.scrollingElement.scrollTop;

    $('body').addClass('modal-open');
    document.body.style.top = -scrollTop + 'px';

}

/***取消滑动限制***/
function move(){
    $('body').removeClass('modal-open');
    //console.log(scrollTop)
    scrollTop=($(window).scrollTop() || $("body").scrollTop());
}

/***提交时禁止点击***/
function tjjzdj(){
    zztj()
    setTimeout(function(){
        tjgb()
    },20000);

}

function zztj(){
    $('#zztj').show();
    $(".zztj_zz").show();
    var ymheight=$(document).height()+ "px";
    $(".zztj_zz").css("height",ymheight);
    stop();

}
function tjgb(){
    $('#zztj').hide();
    $(".zztj_zz").hide();
    $(".zztj_zz").css("height","0px");
    move()
}

