/**
 * Created by Chan on 2016/7/20.
 */
$(function(){
    var btext = '';
    var oTimer=0;
    //var yistop = [];

    /*if(getCookie()){
        
    }*/

    common(0);//推荐加载

    //底部小图标
    $("#footer a").css("color","#989898");
    $("#footer span").css("color","#989898");
    $("#footer a").eq(3).css("color","#F97FA4");
    $("#footer span").eq(3).css("color","#F97FA4");

    //顶部导航条事件
    $('.circle_title li').click(function(){
        if($(this).css('color') != 'rgb(249, 127, 164)'){
            //console.log($(this).css('color'));
            clearInterval(oTimer);
            var i = $(this).index();
            common(i);
        }
    });

    function common(i){
        $('.circle_edit').css('display','none');
        $('.answer_ask').css('display','none');
        $('.circle_title li').css('color','#282828');
        $('.circle_title li').eq(i).css('color','#F97FA4');
        $('.title_change').css('left',1.6*i+'rem');
        $('.circle_content').css('width','0');
        $('.circle_content').eq(i).css('width','6.4rem');
        $('.circle_content').eq(i).css('top','0');
        if(i == 1){ //颜值圈页面
            $('.circle_edit').css('display','block');
            $('.text_content').html('');
            fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Levels&a=Index","",qcallback);
        }
        else if(i == 2){ //美问答页面重置
            $('.part_title li').css('color','#282828');
            $('.answer_artical').css('display','none');
            $('.answer_ask').css('display','none');
            $('.part_title li').eq(0).css('color','#F97FA4');
            $('.part_line').css('left',0);
            $('.answer_artical').eq(0).css('display','block');
            $('.answer_artical').eq(0).html('');
            fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=ColorCircle&a=BeautyEncyclopedia","",bcallback);
        }
        else if(i == 3){ //优惠页面
            $('.gift_list').html('');
            fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Favorable&a=FavorableIndex","",ycallback);
        }
        else if(i == 0){ //推荐页面
            $('.turn_big').html('');//轮播图接口
            $('.turn_small').html('');
            $('.recommend_ask').html('');//推荐 问答接口
            $('.recommend_contrast').html('');//推荐 术前术后对比
            $('.into_list').html('');//推荐 新增分享
            $('.recommend_ad').html('');//推荐 活动

            fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=ColorCircle&a=showPhoto","",tcallback);
            fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=ColorCircle&a=showProblem","",dcallback);
            fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=ColorCircle&a=contrast","",ccallback);
            fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=ColorCircle&a=newArticle","",fcallback);
            fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=ColorCircle&a=selDiscount","",acallback);
        }
    }


    //美问答  美容百科与有问必答 切换
    $('.part_title li').click(function(){
        var i=$(this).index();
        $('.part_title li').css('color','#282828');
        $('.answer_artical').css('display','none');
        $('.answer_ask').css('display','none');
        $(this).css('color','#F97FA4');
        $('.part_line').css('left',(1.34+1.44)*i+'rem');
        $('.answer_artical').eq(i).css('display','block').html('');
        if(i == 1){ //有问必答
            $('.answer_ask').css('display','block');
            fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=ColorCircle&a=BeautyAsk","",wcallback);
        }
        else{ //美容百科
            fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=ColorCircle&a=BeautyEncyclopedia","",bcallback);
        }
    })

    $('.header_search').click(function(){
        window.location.href = 'meisousuo.html';
    })

    //颜值圈精选 热门 全部 切换
    /* $('.text_title li').tap(function(){
     var i = $(this).index();
     $('.text_title li').find('a').attr('class','');
     $('.text_content').css('display','none');
     $(this).find('a').attr('class','text_change');
     $('.text_content').eq(i).css('display','block');
     })*/

    //推荐 进入颜值圈  模块
    $('.into_right').click(function(){
        common(1);
    })

    //颜值圈  编辑跳转发布页
    $('.circle_edit').click(function(){
        window.location.href = 'fabu.html';
    })

    //颜值圈  内容跳转内容详情
    $('.text_content li').click(function(){
        window.location.href = 'dashang.html';
    })

    //美问答  编辑跳转编辑页
    $('.answer_ask').click(function(){
        window.location.href = 'bianji.html';
    })


    // 推荐  轮播图回调函数
    function tcallback(tdata){
        if(tdata.resultnum == 0){
            for(var i=0;i<tdata.result.length;i++){
                $('.turn_big').append('<li id="'+tdata.result[i].slide_id+'"><a href="#"><img src="/'+tdata.result[i].slide_pic+'"></a></li>');
                $('.turn_small').append('<li></li>');
            }
            $('.turn_small li').eq(0).attr('class','small_change');
            $('.turn_big').css('left',0);
            turn();//推荐 的轮播图
            $('.turn_big li').click(function(){
               localStorage.setItem('liid',$(this).attr('id'));//存当前点击的轮播图的id
            });

        }
    }

    // 推荐  问答回调函数
    function dcallback(dtata) {
        if(dtata.resultnum == 0){
            $('.recommend_ask').append('<em>问答</em><div class="ask_left"><div class="ask_text"><h2>'+dtata.result.msg+'</h2></div><div class="see_time clear"><div class="see_text clear"><span></span><i>'+dtata.result.views+'</i></div><div class="time_text clear"><span></span><i>'+dtata.result.createtime+'</i></div></div></div><div class="ask_right"><div><img src="'+dtata.result.user.user_img+'"></div><p>'+dtata.result.user.nickname+'</p></div><a href="javascript:;"></a>');
            if(dtata.result.reply == '1'){
                $('.ask_right').append('<span class="yesAnswer">已回答</span>');
            }
            else{
                $('.ask_right').append('<span class="noAnswer">未回答</span>');
            }
            $('.recommend_ask').click(function(){
                common(2);
            });
        }
    }

    // 推荐  对比回调函数
    function ccallback(cdata) {
        if(cdata.resultnum == 0){
            $('.recommend_contrast').append('<ul class="contrast_left clear"><li class="clear"><div class="left_before"><img src="/'+eval(cdata.result[0].before_img)+'"><span>前</span></div><div class="left_after"><img src="/'+eval(cdata.result[0].after_img)+'"><span>后</span></div></li><li class="clear"><div class="left_before"><img src="/'+eval(cdata.result[1].before_img)+'"><span>前</span> </div><div class="left_after"><img src="/'+eval(cdata.result[1].after_img)+'"><span>后</span></div></li></ul><div class="contrast_right clear"><span></span><p>术前术后对比</p><i></i></div><a href="javascript:;"></a>');
            $('.recommend_contrast').click(function(){
                $('.answer_ask').css('display','none');
                $('.circle_title li').css('color','#282828');
                $('.circle_title li').eq(1).css('color','#F97FA4');
                $('.title_change').css('left','1.6rem');
                $('.circle_content').css('width','0');
                $('.circle_content').eq(1).css('width','6.4rem');
                $('.circle_content').eq(1).css('top','0');
                $('.circle_edit').css('display','block');
                $('.text_content').html('');
                fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Levels&a=Index","",qqcallback);
            });
        }
    }

    // 推荐  新增分享回调函数
    function fcallback(fdata){
        if(fdata.resultnum == 0){
            /*for(var attr in fdata.result){
                if(attr == fdata.result.length-1){
                    $('.into_left').find('p').html('新增'+fdata.result.count+'条用户分享');
                }else{
                    $('.into_list').append('<li><img src="'+fdata.result[attr].user_photo+'"></li>');
                }
            }*/
            var ooo = fdata.result;
            $.each(ooo,function(i,n){
                if(typeof n != 'object'){
                    $('.into_left').find('p').html('新增'+n+'条用户分享');
                    return;
                }
                $('.into_list').append('<li><img src="'+n.user_photo[0].user_img+'"></li>');
            })
        }
    }

    // 推荐  活动块回调函数
    function acallback(adata) {
        if(adata.resultnum == 0){
            $('.recommend_ad').append('<img id="'+adata.result[0].id+'" src="'+adata.result[0].img+'">')
        }
    }

    // 颜值圈  首页
    function qcallback(qdata){
        if(qdata.resultnum == 0){
            for(var i=0;i<qdata.result.length;i++){
                if(qdata.result[i].photo_status == 1){
                    $('.text_content').append('<li id="'+qdata.result[i].id+'" class="text_type1"><div class="li_header clear"><img src="'+qdata.result[i].user_mation.user_img+'"><div class="header_infor"><p>'+qdata.result[i].user_mation.nickname+'</p><i>'+qdata.result[i].level_date+'</i></div></div><ul class="li_image clear"><li><img src="/'+eval(qdata.result[i].user_photo[0].before_img[0])+'"><span>before</span></li><li><img src="/'+eval(qdata.result[i].user_photo[0].after_img[0])+'"><span>after</span></li></ul><p class="li_text">'+qdata.result[i].level_content+'</p><div class="li_tips"><span></span><p>'+qdata.result[i].level_keywords+'</p></div><div class="li_footer clear"><div class="footer_read"><span></span><i>'+qdata.result[i].level_hits+'</i></div><div class="footer_word"><span></span><i>'+qdata.result[i].level_like+'</i></div><div class="footer_good"><span></span><i>'+qdata.result[i].user_comment+'</i></div></div><a href="dashang.html"></a></li>');
                }else{
                    $('.text_content').append('<li id="'+qdata.result[i].id+'" class="text_type2"><div class="li_header clear"><img src="'+qdata.result[i].user_mation.user_img+'"><div class="header_infor"><p>'+qdata.result[i].user_mation.nickname+'</p><i>'+qdata.result[i].level_date+'</i></div></div><p class="li_text">'+qdata.result[i].level_content+'</p><div class="li_tips"><span></span><p>'+qdata.result[i].level_keywords+'</p></div><section class="li_image"><ul class="li_list clear"></ul></section><div class="li_footer clear"><div class="footer_read"><span></span><i>'+qdata.result[i].level_hits+'</i></div><div class="footer_word"><span></span><i>'+qdata.result[i].level_like+'</i></div><div class="footer_good"><span></span><i>'+qdata.result[i].user_comment+'</i></div></div><a href="dashang.html"></a></li>');
                    for(var j=0;j<qdata.result[i].user_photo[0].before_img.length;j++){
                        $('#'+qdata.result[i].id).find('.li_list').append('<li><img src="/'+eval(qdata.result[i].user_photo[0].before_img[j])+'"></li>');
                    }
                    if(qdata.result[i].user_photo[0].before_img.length > 2){
                        $('#'+qdata.result[i].id).find('.li_image').append('<span>共'+qdata.result[i].user_photo[0].before_img.length+'张</span>');
                    }
                }
            }
            $('.text_content li').click(function(){
                localStorage.setItem('articalId',$(this).attr('id'));
            });
        }
    }

    // 从 推荐 进入颜值圈  对比
    function qqcallback(qqdata){
        if(qqdata.resultnum == 0){
            for(var i=0;i<qqdata.result.length;i++){
                if(qqdata.result[i].photo_status == 1){
                    $('.text_content').append('<li id="'+qqdata.result[i].id+'" class="text_type1"><div class="li_header clear"><img src="'+qqdata.result[i].user_mation.user_img+'"><div class="header_infor"><p>'+qqdata.result[i].user_mation.nickname+'</p><i>'+qqdata.result[i].level_date+'</i></div></div><ul class="li_image clear"><li><img src="/'+eval(qqdata.result[i].user_photo[0].before_img[0])+'"><span>before</span></li><li><img src="/'+eval(qqdata.result[i].user_photo[0].after_img[0])+'"><span>after</span></li></ul><p class="li_text">'+qqdata.result[i].level_content+'</p><div class="li_tips"><span></span><p>'+qqdata.result[i].level_keywords+'</p></div><div class="li_footer clear"><div class="footer_read"><span></span><i>'+qqdata.result[i].level_hits+'</i></div><div class="footer_word"><span></span><i>'+qqdata.result[i].level_like+'</i></div><div class="footer_good"><span></span><i>'+qqdata.result[i].user_comment+'</i></div></div><a href="dashang.html"></a></li>');
                }
            }
            $('.text_content li').click(function(){
                localStorage.setItem('articalId',$(this).attr('id'));
            });
        }
    }




    //美问答  美容百科回调函数
    function bcallback (bdata){
        if(bdata.resultnum == 0){
            for(var a=0;a<bdata.result.length;a++){
                $('.answer_artical').eq(0).append('<li id="'+bdata.result[a].id+'" class="artical_one clear"><img src="/'+bdata.result[a].thumb+'"><div class="artical_text"><h3>'+bdata.result[a].post_title+'</h3><p>'+bdata.result[a].post_excerpt+'</p><div class="text_footer clear"><div class="text_read clear"><span></span><i>'+bdata.result[a].post_hits+'</i></div><p>'+bdata.result[a].post_modified+'</p></div></div><a href="baike.html"></a></li>');
            }
            $('.answer_artical').eq(0).find('li').click(function(){
                btext = $(this).attr('id');
                localStorage.setItem('btext',btext);
            })
        }
    }

    //美问答  有问必答回调函数
    function wcallback(wdata){
        if(wdata.resultnum == 0){
            for(var i=0;i<wdata.result.length;i++){
                $('.answer_artical').eq(1).append('<li class="artical_two" id="'+wdata.result[i].id+'"><div class="artical_header clear"><img src="'+wdata.result[i].user_img+'"><div class="header_mation"><p>'+wdata.result[i].user_nickname+'</p><i>'+wdata.result[i].createtime+'</i></div><span data-snum="1"></span></div><p class="artical_question">'+wdata.result[i].msg+'</p><div class="artical_footer clear"><div class="footer_say clear"><span></span><i>'+wdata.result[i].count+'</i></div><div class="footer_see clear"><span></span><i>'+wdata.result[i].views+'</i></div></div><a href="wenda.html"></a></li>');
                //console.log(wdata.result[i].isreply);
                if(wdata.result[i].isreply == 1){
                   //console.log($('#'+wdata.result[i].id).find('.artical_header').find('span').data('snum'));
                    $('.answer_artical').eq(1).find('li').eq(i).find('span[data-snum="1"]').data('snum','0');
                    $('.answer_artical').eq(1).find('li').eq(i).find('span[data-snum="0"]').html('已回答');
                    $('.answer_artical').eq(1).find('li').eq(i).append('<div class="answer_doctor"><img src="'+wdata.result[i].doctor_url+'"><p><span>'+wdata.result[i].doctor+'：</span>'+wdata.result[i].replycontent+'</p></div>');
                }
                else{
                    $('.answer_artical').eq(1).find('li').eq(i).find('span[data-snum="1"]').html('未回答');
                }
            }
            $('.answer_artical').eq(1).find('li').click(function(){
                btext = $(this).attr('id');
                localStorage.setItem('btext',btext);
            });
        }
    }

    //优惠  回调函数
    function ycallback(ydata){
        if(ydata.resultnum == 0){
            for(var a=0;a<ydata.result.length;a++){
                $('.gift_list').append('<li id="'+ydata.result[a].id+'"><img src="'+ydata.result[a].img+'"></li>');
            }
        }
        else{
            alert(ydata.result_mess);
        }
    }

})

function turn(){ //推荐中的轮播图  开始
    var downLeft = 0;
    var downX = 0;
    var iNow = 0;
    var downTime = 0;
    var bBtn = true;
    var oTurn = document.getElementsByClassName('content_turn')[0];
    var oBig = document.getElementsByClassName('turn_big')[0];
    var oBigLi = oBig.getElementsByTagName('li');
    var oSmall = document.getElementsByClassName('turn_small')[0];
    var oSmallLi = oSmall.getElementsByTagName('li');
    var oneWidth = oBigLi[0].offsetWidth;
    var allW = oneWidth*oBigLi.length;
    oBig.style.width = allW+'px';
    oTurn.ontouchmove = function(ev){
        ev.preventDefault();
    }
    function autoPlay()
    {
        iNow++;
        if( iNow > oSmallLi.length-1 ){
            iNow = 0;
        };
        for( var i=0; i<oSmallLi.length; i++ ){
            oSmallLi[i].className = '';
        };
        oSmallLi[iNow].className = 'small_change';
        startMove(oBig,{left : -iNow*oneWidth},400,'easeOut');
    }
    oTimer=setInterval(autoPlay,2000);
    oBig.addEventListener("touchstart",fnStart,false);
    function fnStart(ev)
    {
        clearInterval(oTimer);
        var touchs = ev.changedTouches[0];
        downLeft = this.offsetLeft;
        downX = touchs.pageX;
        downTime = Date.now();
    };
    oBig.addEventListener("touchmove",fnMove,false);
    function fnMove(ev){
        var touchs = ev.changedTouches[0];
        if( this.offsetLeft >= 0 ){
            if(bBtn){
                bBtn = false;
                downX = touchs.pageX;
            }
            this.style.left = (touchs.pageX - downX)/3 + 'px';
        }
        else if(this.offsetLeft <= oTurn.offsetWidth - this.offsetWidth){
            if(bBtn){
                bBtn = false;
                downX = touchs.pageX;
            }
            this.style.left = (touchs.pageX - downX)/3 + (oTurn.offsetWidth - this.offsetWidth) + 'px';
        }
        else{
            this.style.left = touchs.pageX - downX + downLeft + 'px';
        }
    };
    oBig.addEventListener("touchend",fnEnd,false);
    function fnEnd(ev)
    {
        var touchs = ev.changedTouches[0];
        this.ontouchmove = null;
        this.ontouchend = null;
        if( downX < touchs.pageX ){  //→
            if( iNow != 0){
                if(touchs.pageX - downX > oTurn.offsetWidth/2 || Date.now() - downTime < 300 &&  touchs.pageX - downX > 30){
                    iNow--;
                }
            }
            startMove(oBig,{left : -iNow*oneWidth},400,'easeOut');
        }
        else{  //←
            if( iNow != oBigLi.length-1){
                if(downX - touchs.pageX > oTurn.offsetWidth/2 || Date.now() - downTime < 300 && downX - touchs.pageX > 30){
                    iNow++;
                }
            }
            startMove(oBig,{left : -iNow*oneWidth},400,'easeOut');
        }
        for( var i=0; i<oSmallLi.length; i++ ){
            oSmallLi[i].className = '';
        };
        oSmallLi[iNow].className = 'small_change';
        oTimer=setInterval(autoPlay,2000);
    };
    //推荐 轮播图  结束
}

function fengAjax(type,url,data,callback){
    $.ajax({
        type:type,
        url:url,
        data:data,
        success:callback
    })
};





