/**
 * Created by Chan on 2016/7/16.
 */

$(function(){
    var keyArr = [];//存储关键词
    var oDate = new Date();

    leadAjax("post","http://www.buruwo.com/index.php?g=Portal&m=ColorCircle&a=KeyWord","",leader);//加载热门标签

    if(!getCookie('users')){
        var lData = oDate.getTime();
        localStorage.setItem('linshi',lData);
    }
    $('.lead_hot span').tap(function(){//点击跳过
        window.location.href = 'yanzhiquan.html';
        localStorage.setItem('keyArr',keyArr.join());
        localStorage.setItem('only','1');
    });

    $('.lead_in').click(function(){//点击 进入丽妍汇
        localStorage.setItem('keyArr',keyArr.join());
        localStorage.setItem('only','1');
        window.location.href = 'yanzhiquan.html';
    });

    function leadAjax(type,url,data,callback){//ajax函数
        $.ajax({
            type:type,
            url:url,
            data:data,
            success:callback
        });
    };


    function leader(data) {
        if (data.resultnum == 0) {
            for(var i=0;i<data.result.length;i++){
                $('.lead_text').append('<li data-knum="0">#<span>'+data.result[i].keyword+'</span>#</li>');
                if(i%5 == 1){
                    $('.lead_text li').eq(i).css('margin','0 0.5rem');
                    $('.lead_text li').eq(i).css('margin-top','0.3rem');
                }
            }
            $('.lead_text li').tap(function(){
                if($(this).data('knum') == 0){
                    $(this).data('knum','1');
                    keyArr.push($(this).find('span').html());
                    localStorage.setItem('keyArr',keyArr.join());
                }
                else{
                    $(this).data('knum','0');
                    for(var i=0;i<keyArr.length;i++){
                        if(keyArr[i] == $(this).find('span').html()){
                            keyArr.splice(i,1);
                            localStorage.setItem('keyArr',keyArr.join());
                        }
                    }
                }
            })
        }
    }



})




/*function arrFuc(one,arr){
    if(one.length != 0){
        for(var i=0;i<one.length;i++){
            arr.push(one[i].len);
        }
    }
}*/








