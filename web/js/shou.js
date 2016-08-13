/**
 * Created by Gavin on 2016/7/14.
 */
$(function () {
    $('.juan').click(function () {
        $('#left').removeClass('fadeInLeft').addClass('fadeOutLeft');
        $('#right').removeClass('fadeOutRight').addClass('fadeInRight');
    });
    $('#right li').click(function () {
        $('#left').removeClass('fadeOutLeft').addClass('fadeInLeft');
        $('#right').removeClass('fadeInRight').addClass('fadeOutRight');
    });

    touch.on('#zhifu','tap',function (ev) {
        $('#mima').fadeToggle(400)
    });
    touch.on('.quxiao','tap',function () {
        $('#mima').fadeOut(400);
    });
    // $.post('',{},function (data) {
    //
    // },'json');
    var url='../../wxpay/demo/js_api_call.php?uid='+window.localStorage.getItem('uid')+'&token='+window.localStorage.getItem('token')+'&order=65314696122829218&typeurl=2';
    $('#one').click(function () {
        window.location.href=url;
    })
})