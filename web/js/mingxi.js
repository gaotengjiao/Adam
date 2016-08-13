/**
 * Created by Gavin on 2016/7/7.
 */
$(function () {
    var uid=window.localStorage.getItem('uid');
    var token=window.localStorage.getItem('token');
    var color=['#6a6a6a','#2c5cf6','#ff0101'];
    touch.on('.xuan','tap',function (ev) {
        $('.hot').removeClass('hot');
        $(this).addClass('hot');
        if($(this).index()==1){
            $('.yuefen').hide(function () {
                setTimeout(function () {
                    $('.yuefen[data-num="1"]').slideDown(600);
                },500)
            });
        }else if ($(this).index()==2){
            $('.yuefen').hide(function () {
                setTimeout(function () {
                    $('.yuefen[data-num="0"]').slideDown(600);
                },500)
            });
        }else{
            $('.yuefen').hide();
            setTimeout(function () {
                $('.yuefen').slideDown(600)
            },500)
        }
    });
    $.post('http://www.buruwo.com/index.php?g=Portal&m=FaceScore&a=QuotaDetail&uid='+uid+'&token='+token+'&allnum=0&size=4&type=1',function (data) {
        console.log(data);
        $('.ramining').text(data.result.money.ramining);
        $('.limit').text(data.result.money.limit);
        if (data.resultnum==404){
            return
        }else{
            $.each(data.result.data,function (m,n) {
                $('.tit').prepend('<li><span class="time padleft">'+m+'</span><ul></ul></li>');
                $.each(n,function (z,b) {
                    var payment=1;
                    var zt;
                    if (b.payment==0){
                        payment=0;
                    };
                    if (b.isoverdue=='未还清'){
                        zt='<span class="no_pri" style="color: '+color[0]+'">'+b.isoverdue+'</span>';
                    }else if(b.isoverdue=='已还清'){
                        zt='<span class="no_pri" style="color: '+color[1]+'">'+b.isoverdue+'</span>';
                    }else if(b.isoverdue=='已逾期'){
                        zt='<span class="no_pri" style="color: '+color[2]+'">'+b.isoverdue+'</span>';
                    };
                    $('.tit li:first-child ul').append('<li class="yuefen clear" data-num="'+payment+'"><div class="clear tittle padleft padright"><span class="chao bt">'+b.zname+'</span><span>总额:</span><span class="price_zong">'+b.allmoney+'</span></div><div class="zhuantai clear"><div class="zt_price"><span class="pri_yin pri">本期应还：<i>'+b.should+'</i>元</span><span class="pri_dai pri">待还金额：<i>'+b.wait+'</i>元</span><span class="pri_time">还款日期：<i>'+b.time+'</i></span></div><div class="zt_zt"><span class="qishu">已还'+b.paycount+'/'+b.repaycount+'期</span>'+zt+'</div></div></li>');
                })
            })
        }
    },'json')
});