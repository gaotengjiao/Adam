/**
 * Created by Chan on 2016/7/19.
 */
$(function(){
    var arr = [];
    $('.search_history').css('display','none');//初始化
    if($('.search_another')){
        $('.search_another').removeClass('search_another');
    }
    souAjax('post','http://www.buruwo.com/index.php?g=Portal&m=index&a=search','',search);//获取热门项目
    $('.search_line input').focus(function(){//光标获取
        $('.search_head').addClass('search_another');
        $('.search_history').css('display','block');
        if(localStorage.getItem('history')){//获取历史搜索记录
            $('.history_list').html('');
            arr = localStorage.getItem('history').split(',');
            for(var i=0;i<arr.length;i++){
                $('.history_list').append('<li class="clear"><p>'+arr[i]+'</p><span></span></li>');
            }
        }
        $('.history_list span').tap(function(){//删除历史搜索内容
            var i = $(this).index();
            $(this).closest('li').remove();
            arr.splice('i',1);
            localStorage.setItem('history',arr.join());
            //console.log(localStorage.getItem('history'));
        });
    });

    $('.search_line span').tap(function(){//将搜索内容存上，作为历史搜索内容
        if(!localStorage.getItem('history')){
            localStorage.setItem('history',$('.search_line input').val());
        }else{
            localStorage.setItem('history',localStorage.getItem('history')+","+$('.search_line input').val());
        }
    })
    
})
function souAjax(type,url,data,callback){
    $.ajax({
        type:type,
        url:url,
        data:data,
        success:callback
    });
};

function search(sData){
    //console.log(sData.result[0].gname);
    if(sData.resultnum == 0){
        if(sData.result.result[0].one.length!=0){
            for(var i=0;i<sData.result.result[0].one.length;i++){
                $('.project_list').append('<li id="'+sData.result.result[0].one[i].id+'">'+sData.result.result[0].one[i].keyword+'</li>');
            }
        }
        if(sData.result.result[0].two.length!=0){
            for(var i=0;i<sData.result.result[0].two.length;i++){
                $('.project_list').append('<li id="'+sData.result.result[0].two[i].id+'">'+sData.result.result[0].two[i].keyword+'</li>');
            }
        }
        if(sData.result.result[0].three.length!=0){
            for(var i=0;i<sData.result.result[0].three.length;i++){
                $('.project_list').append('<li id="'+sData.result.result[0].three[i].id+'">'+sData.result.result[0].three[i].keyword+'</li>');
            }
        }
        if(sData.result.result[0].four.length!=0){
            for(var i=0;i<sData.result.result[0].four.length;i++){
                $('.project_list').append('<li id="'+sData.result.result[0].four[i].id+'">'+sData.result.result[0].four[i].keyword+'</li>');
            }
        }
        $('.project_list li').tap(function(){
            $(this).addClass('li_change');
            setCookie('cok2',$(this).attr('id'));
        });
    }
}