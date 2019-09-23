
    $(document).ready(function(){
    var mobileBodyW= $(document).width();   //机身的实际宽度
    document.documentElement.style.fontSize=100*document.documentElement.clientWidth/750+'px';
    $('body').css('visibility','visible');
    $('body').append('<div id="appculW"></div>');
    $('#appculW').css('display','none');
    var culW=$('#appculW').width();
    var scale=mobileBodyW/culW;
    if(scale!=1){
        document.documentElement.style.fontSize=100*document.documentElement.clientWidth/750*scale+'px';
        }
})
$(window).resize(function(){
    document.documentElement.style.fontSize=100*document.documentElement.clientWidth/750+'px';
})
