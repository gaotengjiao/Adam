/**
 * Created by Chan on 2016/7/16.
 */
$(function(){
    var typeArr = [];//存储类型
    kindAjax("post","http://www.buruwo.com/index.php?g=Portal&m=ColorCircle&a=TypeHtml","",kinder);
    
    function kindAjax(type,url,data,callback){
        $.ajax({
            type:type,
            url:url,
            data:data,
            success:callback
        });
    };
    
    function kinder(data){
        if(data.resultnum == 0){
            for(var i=0;i<data.result[0].typeone.length;i++){
                $('.all_content').append('<div class="all_part"><p class="part_title">'+data.result[0].typeone[i].typename+'类</p><ul class="part_list clear"></ul>');
                for(var a=0;a<data.result[0].typeall.length;a++){
                    if(data.result[0].typeall[a].pid == data.result[0].typeone[i].id){
                        $('.part_list').eq(i).append('<li data-tnum="0">'+data.result[0].typeall[a].typename+'</li>');
                    }
                }
            }
            if(localStorage.getItem('typeArr') == null) {
                //console.log(localStorage.getItem('typeArr') == null);
            }else{
                typeArr = localStorage.getItem('typeArr').split(',');
                //console.log(typeArr);
                for(var i=0;i<typeArr.length;i++){
                    for(var j=0;j<$('li[data-tnum="0"]').length;j++){
                        if(typeArr[i] == $('li[data-tnum="0"]').eq(j).html()){
                            $('li[data-tnum="0"]').eq(j).data('tnum','1');
                        }
                    }
                }
            }
            $('li').tap(function(){
                if($(this).data('tnum') == 0){
                    $(this).data('tnum','1');
                    typeArr.push($(this).html());
                    localStorage.setItem('typeArr',typeArr.join());
                }else{
                    $(this).data('tnum','0');
                    for(var i=0;i<typeArr.length;i++){
                        if(typeArr[i] == $(this).html()){
                            typeArr.splice(i,1);
                            localStorage.setItem('typeArr',typeArr.join());
                        }
                    }
                }

            })
        }
        else{
            alert(data.result_mess);
        }
    }
})
