/**
 * Created by Gavin on 2016/7/14.
 */
$(function () {
    $('#a').slideUp();
    touch.on('.qishu','tap',function (ev) {
        $('#xuan').css('display','block');
        $('#a').slideToggle(600);
    });
    touch.on('.close','tap',function (ev) {
        $('#a').slideToggle(600,function () {
            $('#xuan').css('display','none');
        });
    });

    touch.on('#a li','tap',function (ev) {
        $('.noxuan').removeClass('hot');
        if($(this)[0].localName=='li'){
            $(this).find('i').addClass('hot');
            var con=$(this).find('.tit').text();
        }else {
            $(this).parents('li').find('i').addClass('hot');
            var con=$(this).parents('li').find('.tit').text();
        }
        $('.qishu').text(con);
    });

    touch.on('#zhifu','tap',function (ev) {
        $('#mima').fadeToggle(400)
    });
    touch.on('.quxiao','tap',function () {
        $('#mima').fadeOut(400);
    })
})