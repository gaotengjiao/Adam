/**
 * Created by Chan on 2016/8/4.
 */
$(function(){
    var liHeight = $('.state_li').offset().height;
    //console.log(liHeight);
    $('.state_left').css('height',liHeight);//设置时间轴
    $('.state_line p').css('height',liHeight+10);

    //选项卡 切换
    $('.choose_list li').click(function(){
        var a = $(this).index();
        $('.choose_list li').css('color','#656565');
        $(this).css('color','#e169a3');
        $('.state_choose p').css('left',3.2*a+'rem');
        if(a==0){
            $('.state_text[data-mnum="0"]').css('left','0');
            $('.state_text[data-mnum="1"]').css('left','6.4rem');
        }
        else{
            $('.state_text[data-mnum="0"]').css('left','-6.4rem');
            $('.state_text[data-mnum="1"]').css('left','0');
        }
    })

})