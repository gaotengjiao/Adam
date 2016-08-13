/**
 * Created by Gavin on 2016/7/7.
 */
$(function () {
    var fs;
    touch.on('.xuan','tap',function (ev) {
        $('.xuan').removeClass('xuanhot');
        $(this).toggleClass('xuanhot');
        fs=$(this).parent().index();
    });
    $('.bianhao').text('编号:'+window.sessionStorage.getItem('billnum'));
    $('.num').text(window.sessionStorage.getItem('dai'))
});