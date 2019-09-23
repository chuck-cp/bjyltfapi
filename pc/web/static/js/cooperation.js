$(function () {
    var fromurl = document.referrer;
    //console.log(fromurl);
    //console.log(window.location.href);
    var way = window.location.href.split('=',window.location.href.length)[1];
    console.log('way====='+way);
    if(way=='one'){
        $('.cooperation1').css('display','block');
        $('.cooperation2').css('display','none');
        $('.cooperation3').css('display','none');
        $('.toggle').find('a').eq(0).find('span').addClass('active');
        $('.toggle').find('a').eq(0).siblings().find('span').removeClass('active')
    }else if(way=='two'){
        console.log('two=====');
        $('.cooperation1').css('display','none');
        $('.cooperation2').css('display','block');
        $('.cooperation3').css('display','none');
        $('.toggle').find('a').eq(1).find('span').addClass('active');
        $('.toggle').find('a').eq(1).siblings().find('span').removeClass('active')
    }else if(way=='three'){
        $('.cooperation1').css('display','none');
        $('.cooperation2').css('display','none');
        $('.cooperation3').css('display','block');
        $('.toggle').find('a').eq(2).find('span').addClass('active');
        $('.toggle').find('a').eq(2).siblings().find('span').removeClass('active')
    }
    console.log('way-----'+way);
    $('.toggle').find('a').on('click',function () {
        $(this).find('span').addClass('active');
        $(this).siblings().find('span').removeClass('active');
        console.log($(this).index());
        var ind = $(this).index();
        if(ind==0){
            $('.cooperation1').css('display','block');
            $('.cooperation2').css('display','none');
            $('.cooperation3').css('display','none');
        }else if(ind==1){
            $('.cooperation2').css('display','block');
            $('.cooperation1').css('display','none');
            $('.cooperation3').css('display','none');
        }else if(ind==2){
            $('.cooperation3').css('display','block');
            $('.cooperation1').css('display','none');
            $('.cooperation2').css('display','none');
        }
        //$('.cooperation').find('div').eq(ind).css('display','block').siblings().css('display','none');

    })

    $(window).scroll(function () {
        var scrollLeft = $(window).scrollLeft();
        if(scrollLeft > 0){
            $('.top').css('left',-scrollLeft);
        }
    })
})