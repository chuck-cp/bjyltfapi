/**
 * Created by gaojianbo on 2019/7/2.
 */
//提示函数可跳转
function tippanel(href){
    $('.sy-installed-ts').show();
    href = href || '';
    if(href){
        setTimeout(function(){
            $('.sy-installed-ts').hide();
            window.location.href = href;
        },2000);
    }else {
        setTimeout(function(){
            $('.sy-installed-ts').hide();
        },2000);
    }
}
//数组操作函数
function pushArr(arr, value) {
    if(arr instanceof Array){
        if(value){
            return arr.push(value);
        }
        return false;
    }
    return false;
}
/*
*获取多个dom对象中的属性或值
* dom,dom对象
* val,是否是获取input的value
* data_type,返回值是数组或字符串(以逗号连接)
*/
function getAttr(dom, val, data_type, is_null, html) {
    var is_html = html || false;
    var re = data_type || '';
    var is_empty = is_null || false;
    var flag = false;
    if(re instanceof Array){
        flag = true;
    }
    return cyclicGeneration(dom, val, flag, is_empty, is_html);
}
//循环生成数组或字符串
function cyclicGeneration(dom, val, flag, is_empty, is_html) {
    var __thisval;
    var re = flag ? [] : '';
    if(flag){
        dom.each(function (i) {
            if(is_html){
                __thisval = $(this).html();
            }else{
                __thisval = getValByType(val, $(this));
            }
            if(is_empty){
                pushArr(re, __thisval);
            }else{
                if(__thisval){
                    pushArr(re, __thisval);
                }
            }
        })
    }else{
        dom.each(function (i) {
            if(is_html){
                __thisval = $(this).html();
            }else{
                __thisval = getValByType(val, $(this));
            }
            if(is_empty){
                re += __thisval+',';
            }else{
                if(__thisval){
                    re += __thisval+',';
                }
            }

        })
        re = re ? re.substring(0, re.length-1) : '';
    }
    return re;
}
//根据不同类型获取不同的值
function getValByType(val, domthis) {
    var __thisval;
    if(val == 'val'){
        __thisval = domthis.val() ? domthis.val() : ' ';
    }else {
        __thisval = domthis.attr(val);
    }
    return __thisval;
}
















