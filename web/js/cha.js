// *////  * Created by Gavin on 2016/7/8.//$(function () {    var uid=window.localStorage.getItem('uid');    var token=window.localStorage.getItem('token');    var show;    var zong,qi;    $.post('http://www.buruwo.com/index.php?g=Portal&m=FaceScore&a=QuotaDetail&uid='+uid+'&token='+token+'&allnum=0&size=4&type=2',function (data) {        console.log(data);        if (data.result.money.allmoney){            zong=parseInt(data.result.money.allmoney).toFixed(2);        }else {            zong='0.00'        }        if (data.result.money.sevenmoney){            qi=parseInt(data.result.money.sevenmoney).toFixed(2);        }else {            qi='0.00'        }        $('.zongshu').text('￥'+zong);        $('.aaa').text('(全部待还总额)');        $.each(data.result.data,function (m,n) {            $('.context').append($('<li class="clear" data-num="'+n.status+'"><div class="left"><span class="zonge">'+n.allmoney+'</span><span class="xiangmu">'+n.billcontent+'</span></div><div class="right"><span class="qi_index">剩余'+n.daysremaining+'天</span><span class="qi_zong">'+n.paycount+'期 (共'+n.repaycount+'期)</span></div></li>'))        })    },'json');    touch.on('.xuan','tap',function (ev) {        $('.xuan').removeClass('xuanhot');        $(this).addClass('xuanhot');        show=$(this).index();        console.log(show);        if(show==1){            $('.zongshu').text('￥'+qi);            $('.aaa').text('(近七日待还总额)');            $('.context li').hide(function () {                setTimeout(function () {                    $('.context li[data-num="1"]').slideDown(600);                },500)            });        }else if (show==2){            $('.zongshu').text('');            $('.aaa').text('无待还金额');            $('.context li').hide(function () {                setTimeout(function () {                    $('.context li[data-num="2"]').slideDown(600);                },500)            });        }else if(show==0){            $('.zongshu').text('￥'+zong);            $('.aaa').text('(全部待还总额)');            $('.context li').slideDown(600);        }    })});