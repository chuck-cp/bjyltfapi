/**
 * Created by gaojianbo on 2019/7/2.
 * 获取图片链接
 * elem 选择器，只支持id class
 * isinput 是否是input,非Input一律按attr处理
 */
function getpics(elem,isinput){
    if(!elem){
        return '';
    }
    var first = elem.substr(0, 1);
    if(first == '#'){
        if(isinput){
            return $(elem).val();
        }
        return $(elem).attr('src');
    }else if(first == '.'){
        var pics = '';
        $(elem).each(function () {
            if(isinput){
                pics += $(this).val() ? $(this).val()+',' : '';
            }else{
                pics += $(this).attr('src') ? $(this).attr('src')+',' : '';
            }
        })
        return pics ? pics.substring(0, pics.length-1) : '';
    }
}