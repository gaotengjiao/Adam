/**
 * Created by Chan on 2016/8/1.
 */
$(function(){
    $('.text_content').html('');
    $('.comment_list').html('');
    fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Levels&a=Index","&id="+localStorage.getItem('articalId'),dscallback);

    function dscallback(dsdata) {
        if(dsdata.resultnum == 0){
            if(dsdata.result.article[0].photo_status == 1){
                $('.text_content').append('<li id="'+dsdata.result.article[0].id+'" class="text_type1"><div class="li_header clear"><img src="'+dsdata.result.article[0].user_mation.user_img+'"><div class="header_infor"><p>'+dsdata.result.article[0].user_mation.nickname+'</p><i>'+dsdata.result.article[0].level_date+'</i></div></div><p class="li_text">'+dsdata.result.article[0].level_content+'</p><ul class="li_image clear"><li><img src="/'+eval(dsdata.result.article[0].user_photo[0].before_img[0])+'"><span>before</span></li><li><img src="/'+eval(dsdata.result.article[0].user_photo[0].after_img[0])+'"><span>after</span></li></ul><div class="li_tips"><span></span><p>'+dsdata.result.article[0].level_keywords+'</p></div><div class="li_footer clear"><div class="footer_read"><span></span><i>'+dsdata.result.article[0].level_hits+'</i></div><div class="footer_word"><span></span><i>'+dsdata.result.article[0].level_like+'</i></div><div class="footer_good"><span></span><i>'+dsdata.result.article[0].user_comment+'</i></div></div><a href="dashang.html"></a></li>');
            }else{
                $('.text_content').append('<li id="'+dsdata.result.article[0].id+'" class="text_type2"><div class="li_header clear"><img src="'+dsdata.result.article[0].user_mation.user_img+'"><div class="header_infor"><p>'+dsdata.result.article[0].user_mation.nickname+'</p><i>'+dsdata.result.article[0].level_date+'</i></div></div><p class="li_text">'+dsdata.result.article[0].level_content+'</p><div class="li_tips"><span></span><p>'+dsdata.result.article[0].level_keywords+'</p></div><section class="li_image"><ul class="li_list clear"></ul></section><div class="li_footer clear"><div class="footer_read"><span></span><i>'+dsdata.result.article[0].level_hits+'</i></div><div class="footer_word"><span></span><i>'+dsdata.result.article[0].level_like+'</i></div><div class="footer_good"><span></span><i>'+dsdata.result.article[0].user_comment+'</i></div></div></li>');
                for(var j=0;j<dsdata.result.article[0].user_photo[0].before_img.length;j++){
                    $('#'+dsdata.result.article[0].id).find('.li_list').append('<li><img src="/'+eval(dsdata.result.article[0].user_photo[0].before_img[j])+'"></li>');
                }
            }
            if(dsdata.result.comment.length > 0){
                for(var i=0;i<dsdata.result.comment.length;i++){
                    $('.comment_list').append('<li id="'+dsdata.result.comment[i].id+'"><div class="list_header clear"><img src="'+dsdata.result.comment[i].user_img+'"><p>'+dsdata.result.comment[i].user_name+'</p><span>'+dsdata.result.comment[i].createtime+'</span><div class="part_zan clear"><em data-num="0"></em><i>'+dsdata.result.comment[i].comment_count+'</i></div></div><div class="list_content">'+dsdata.result.comment[i].content+'</div></li>');
                    if(dsdata.result.comment[i].user_comment_status == 1){
                        $('.comment_list').find('li').eq('i').find('span[data-num="0"]').data('num','1');
                    }
                }
            }
            if(dsdata.result.article[0].user_click_status == 1){
                $('.reward_footer').find('i[data-num="0"]').data('num','1');
            }
            $('.reward_footer').find('i[data-num="0"]').click(function(){
                if(!getCookie('users')){
                    setCookie('dianzan','0');
                    window.location.href = 'denglu.html';
                }else{
                    fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Levels&a=clickGood","uid="+localStorage.getItem('uid')+'&aid'+localStorage.getItem('articalId'),dscallback);
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