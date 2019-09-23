/**
 * Created by Administrator on 2019/3/28.
 */
/* 显示遮罩层 */
function showOverlay(id) {
    $("#"+id).height(bodyHeight());
    $("#"+id).width(pageWidth());
    // fadeTo第一个参数为速度，第二个为透明度
    // 多重方式控制透明度，保证兼容性，但也带来修改麻烦的问题
    $("#"+id).fadeTo(200, 0.5);
}
/* 隐藏覆盖层 */
function hideOverlay(id) {
    $("#"+id).fadeOut(200);
}
/* 当前页面高度 */
function bodyHeight() {
    return document.body.scrollHeight;
}
// function hh() {
//     return $(window).scrollTop();
// }
function hh(){
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop || document.body.scrollTop || 0;
    return scrollTop;
}
/* 当前页面宽度 */
function bodyWidth() {
    return document.body.scrollWidth;
}
/* 当前浏览器的高度*/
function pageHeight() {
    return window.innerHeight;
    //return window.screen.height;
    //return window.screen.availHeight;
}
/* 当前浏览器的宽度*/
function pageWidth() {
    return document.body.clientWidth
    //return window.screen.availWidth;
}
function unScroll() {
    var top = $(document).scrollTop();
    $(document).on('scroll.unable',function (e) {
        $(document).scrollTop(top);
    })
}
function removeUnScroll() {
    $(document).unbind("scroll.unable");
}
function GetWidthHeight(Img) {
    var image = new Image();
    image.src = Img;
    return [image.width,image.height];
}
//点击放大
function HitPic(){
    //点击放大
    $('.yx_dphoto_img').live('click',function () {
        if($('#overlay').css('display') == 'none'){
            var pic = $(this).attr('src');
            var arr = pic.split("?");
            $('#bigpic img').attr('src',arr[0]);
            var wh = pageWidth();
            var dv = wh;
            var wrr = GetWidthHeight(arr[0]);
            var imgHeight = wrr[1];
            if(imgHeight == 0){
                return;
            }
            showOverlay('overlay');
            var finalHeight = dv/wrr[0] * imgHeight;
            //var diffH = hh() + parseInt((pageHeight()-finalHeight)/2);
            var diffH = parseInt((pageHeight()-finalHeight)/2) - 20;
            var diffW = (wh - dv)/2;
            $('#bigpic').css({
                'visibility':'visible',
                'z-index':'101',
                'position':'fixed',
                'left':diffW,'top':diffH,

            });
            unScroll();
        }
    })
    $('#bigpic img').live('click',function () {
        $('#bigpic').css('visibility','hidden');
        removeUnScroll();
        hideOverlay('overlay');
    })
}