$(function(){
    fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=ColorCircle&a=SearchBeautyAsk","",rbcallback);

    $('.beauty_close').tap(function(){//搜索条置空
        $('.beauty_line input').val('');
    });

    $('.beauty_title li').tap(function(){//搜索内容切换
        var i=$(this).index();
        if( i == 0 ){
            $('.beauty_bai').css('display','block');
            $('.beauty_you').css('display','none');
        }else{
            $('.beauty_bai').css('display','none');
            $('.beauty_you').css('display','block');
        }
        $('.beauty_title li').attr('class','');
        $(this).attr('class','beauty_change');
    });


    function rbcallback(rbdata){
        if(rbdata.resultnum == 0){
            for(var i=0;i<rbdata.result.type1.length;i++){
                $('.you_list').append('<li id="'+rbdata.result.type1[i].id+'"><span></span>'+rbdata.result.type1[i].content+'</li>');
            }
            for(var attr in rbdata.result.type2){
                $('.bai_list').append('<li>'+rbdata.result.type2[attr]+'</li>');
            }
            $('.bai_list li').click(function(){
                $('.beauty_line input').val($(this).html());
                localStorage.setItem('bwhere',$(this).html());
            });
            $('.you_list li').click(function(){
                $('.beauty_line input').val($(this).html());
                localStorage.setItem('wwhere',$(this).html());
            });
            $('.beauty_line span').click(function(){
                window.location.href = 'yanzhiquan.html';
                
            });
        }
    }

});

function fengAjax(type,url,data,callback){
    $.ajax({
        type:type,
        url:url,
        data:data,
        success:callback
    })
};
