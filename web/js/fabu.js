/**
 * Created by Chan on 2016/8/2.
 */

$(function () {
    var tipsValue = '';//存储标签内容
    var newtips = '';//存储新标签内容

    //头部切换选项卡
    $('.title_top li').tap(function(){
        var a = $(this).index();
        $('.title_top li').css('color','#282828');
        $(this).css('color','#e169a3');
        $('.title_bottom').css('left',a*3.2+'rem');
        if(a==0){
            $('.artical_img').css('display','block');
            $('.artical_compare').css('display','none');
        }
        else{
            $('.artical_compare').css('display','block');
            $('.artical_img').css('display','none');
        }
    })
    //标签选择
    $('.tips_list li').tap(function(){
        if($(this).index() !== $('.tips_list li').length-1){
            tipsValue = $(this).html();
            $('.tips_list li').css('color','#a9a9a9').css('border-color','#d6d7dc');
            $(this).css('color','#e169a3').css('border-color','#e169a3');
        }else{
            newtips = '';
            $('.send_black').css('display','flex');
        }
    });
    //添加标签  确认
    $('.window_sure').tap(function(){
        newtips = $('.window_write').val();
        if(newtips == '' || newtips == null){
            alert('标签内容不能为空');
        }else{
            if($('.window_write').val().length > 4){
                alert('标签内容不能超过4个字');
            }else{
                $('.send_black').css('display','none');
                $('.tips_list').prepend('<li>'+newtips+'</li>');
                $('.tips_list li').css('color','#a9a9a9').css('border-color','#d6d7dc');
                $('.tips_list li').eq(0).css('color','#e169a3').css('border-color','#e169a3');
                tipsValue = newtips;
            }
        }
    });
    //添加标签  取消
    $('.window_cancel').tap(function(){
        $('.send_black').css('display','none');
    });


})
