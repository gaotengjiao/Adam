$(function(){
    $('.wait_list li').tap(function(){//选项卡切换
        var i = $(this).index();
        $('.wait_list li').attr('class','');
        $(this).attr('class','wait_now');
        if(i==0){
            $('.all_list li').css('display','block');
        }else{
            $('.all_list li').css('display','none');
            $('.all_list li[data-anum="0"]').css('display','block');
        }
    });
    
})