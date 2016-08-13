/**
 * Created by Gavin on 2016/7/10.
 */
$(function () {
    touch.on('.xx','tap',function (ev) {
        $('.xx').removeClass('hotx');
        $(this).addClass('hotx');
        $('.shenqing').addClass('tj');
    });
    touch.on('body','tap','.tj',function (ev) {
        
    })
});