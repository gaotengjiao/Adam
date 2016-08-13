/**
 * Created by Gary Lew on 2016/8/8.
 */
$(function () {
    var time;
    $('#xia').prev().bind('input propertychange', function() {
        if($(this).val().length==6){
            $('#xia').css({background:'#f3a9c9'}).click(function () {
                if(!$(this).prev().val()){
                    return
                }
                $('#p_right').animate({left:0},500);
            })
        }else {
            $('#xia').css({background:''}).unbind();
        };
    });
    touch.on('.phone','tap',function (ev) {
        $('#phone').animate({left:0},500)
    });
    touch.on('.tui','tap',function (ev) {
        $('#tui').animate({left:0},500)
    });
    touch.on('.about','tap',function (ev) {
        $('#guanyu').animate({left:0},500)
    });
    $('.qu').click(function (ev) {
        time=60;
        var that=$(this);
        $(this).css({background:'rgba(0,0,0,0.5)',color:'#fff',border:0}).text(time+'秒');
        var t=setInterval(function () {
            time--;
            if (time<=0){
                clearInterval(t);
                that.removeAttr('style').text('重新发送验证码');
                return
            }
            that.text(time+'秒');
        },1000)
    });

});