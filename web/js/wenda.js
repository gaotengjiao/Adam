/**
 * Created by Chan on 2016/8/3.
 */

$(function(){
    /*var oDate = new Date();
    var timenum = oDate.getTime();
    console.log(timenum);
    var str = '';*/
    fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=ColorCircle&a=QueryDetails","&id="+localStorage.getItem('btext')+"&type=1",wdcallback);

    function wdcallback(wddata){
        if(wddata.resultnum == 0) {
            $('.ask_main').append('<div class="li_header clear"><img src="'+wddata.result.details.user_url+'"><div class="header_infor"><p>'+wddata.result.details.user_nickname+'</p><i>'+wddata.result.details.createtime+'</i></div><span data-znum="0"></span></div><p class="li_text">'+wddata.result.details.msg+'</p><section class="li_image"><ul class="li_list clear"></ul></section><div class="li_footer clear"><div class="li_tips"><p>#<span>双眼皮</span>#</p></div><div class="footer_read"><span></span><i>'+wddata.result.details.views+'</i></div></div>');
            if(wddata.result.details.isreply == 1){
                /*var years = math.floor((timenum - Number(wddata.result[0].details.replytime))/31536000);
                var month = math.floor(((timenum - Number(wddata.result[0].details.replytime))%31536000)/2592000);
                var day = math.floor((((timenum - Number(wddata.result[0].details.replytime))%31536000)%2592000)/86400);
                var minute = (timenum-years*31536000-month*2592000-day*86400);
                if(years == 0){

                }else{
                   //str = (math.floor(Number(wddata.result[0].details.replytime)/31536000)+1970)+'-'+;
                }*/
                $('.li_header').find('span[data-znum="0"]').data('znum','1').html('已回答');
                $('.doctor_title').find('span').html('1');
                $('.doctor_list').append('<li><div class="list_header clear"><img src="'+wddata.result.details.doctor_url+'"><p>'+wddata.result.details.doctor+'</p><span>'+wddata.result.details.replytime+'</span></div><div class="list_content">'+wddata.result.details.replycontent+'</div></li>');
            }
            else{
                $('.li_header').find('span[data-znum="0"]').html('未回答');
                $('.doctor_title').find('span').html('0');
            }
            $('.all_title').find('span').html(wddata.result.usercommnet.count);
            $.each(wddata.result.usercommnet,function(i,n){
                if(typeof n != 'object'){
                    return;
                }
                $('.all_list').append('<li><div class="list_header clear"><img src="'+n.user_img+'"><p>'+n.user_nickname+'</p ><span>'+n.replytime+'</span></div><div class="list_content">'+n.replycontent+'</div></li>');
            });
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