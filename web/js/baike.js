/**
 * Created by Chan on 2016/8/4.
 */
$(function(){
    fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=ColorCircle&a=QueryDetails","&id="+localStorage.getItem('btext')+"&type=2",bxcallback);

    function bxcallback(bxdata){
        if(bxdata.resultnum == 0){
            $('.bk_main').append('<h1>'+bxdata.result.post_title+'</h1><div class="bk_time"><span>'+bxdata.result.post_modified+'</span><i>'+bxdata.result.post_source+'</i></div><div class="bk_content">'+bxdata.result.post_content+'</div><div class="bk_footer clear"><div class="bk_read clear"><span></span><i>'+bxdata.result.post_hits+'</i></div><div class="bk_zan clear"><span data-bnum="0"></span><i>'+bxdata.result.post_like+'</i></div></div>');
            $('.bk_zan').click(function(){
                if($(this).find('span').data('bnum') == 0){
                    $(this).find('span').data('bnum','1');
                    $(this).find('i').html(Number($(this).find('i').html())+1);
                }
                else{
                    $(this).find('span').data('bnum','0');
                    $(this).find('i').html(Number($(this).find('i').html())-1);
                }


            })
        }
    }
})

function fengAjax(type,url,data,callback){
    $.ajax({
        type:type,
        url:url,
        data:data,
        success:callback
    })
};





