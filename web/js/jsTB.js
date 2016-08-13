
var bianlianF=null;
var bianlianL=null;
var lailai = 0;
var ququ = 0;
var gunonoff = true;
var sArr = [];
var aOnoff = true;
var rId = null;

if(localStorage.getItem('uid')){
	haoAjax("post","http://www.buruwo.com/index.php?g=Portal&m=shopping&a=ReservationList","uid="+localStorage.getItem('uid')+"&token="+localStorage.getItem('token')+"&type=select",cxun);
}
else{
    $('#yueShu').html(0);
	$('#yueShu').css('display','none');
    $('.danNumber').html(0);
    localStorage.setItem('sNum',0);
}

function cxun(xData){
	if(xData.resultnum == 404){
		$('#yueShu').html(0);
		$('.danNumber').html(0);
        $('#yueShu').css('display','none');
		localStorage.setItem('sNum',0);
	}
	else if(xData.resultnum == 0){
        $('#yueShu').css('display','block');
		$('#yueShu').html(xData.result.length);
		$('.danNumber').html(xData.result.length);
		localStorage.setItem('sNum',xData.result.length);
        for(var i=0;i<xData.result.length;i++){
            sArr.push(xData.result[i].id);
            if(sArr[i] == getCookie('cok2')){
                addOnoff = false;
                //alert(123);
                $("#add").css({
                    background:"#989898"
                });
            }
        }
	}
};

function contlocalstorage(){//项目页内容storage存储事件及效果
	$(".right_img").tap(function(){//项目头像点击事件
        rId = $(this).closest('li').data('dnum');
        turn(rId);
	})
    $(".right_text").tap(function(){//项目内容点击事件
        rId = $(this).closest('li').data('dnum');
        turn(rId);
    })
    function turn(rId){
        if(window.localStorage){
            localStorage.setItem('fuid',bianlianF);//fuid存储
            localStorage.setItem('ziid',bianlianL);//ziid存储
            localStorage.setItem('xiaoid',rId);//xiaoid存储
        }
        delCookie("cok2");
        setCookie("cok2",rId);
        window.location.href="xiangqingye.html";
    }
}

function addlocalstorage(){//项目页内容后加号storage存储事件及效果
	$(".tianJia").tap(function(){//项目页内容添加点击事件
        if($(this).closest('li').data('flag') == '0'){
            setCookie('gogo','0');
            if(!getCookie('users')){
                window.location.href = 'denglu.html';
            }
            else{
                lailai = $(this).closest('li').data('dnum');//存储当前项目的id
                sArr.push(lailai);
                haoAjax("post","http://www.buruwo.com/index.php?g=Portal&m=shopping&a=ReservationList","uid="+localStorage.getItem('uid')+"&project="+lailai+"&token="+localStorage.getItem('token')+"&type=add",hunshu);
                //添加接口
                $(this).closest('li').data('flag','1') ;
                $(this).closest('li').find(".tianJia i").html('取消');
            }
        }
	else{
            ququ = $(this).closest('li').data('dnum');//存储当前项目的id
            haoAjax("post","http://www.buruwo.com/index.php?g=Portal&m=shopping&a=ReservationList","uid="+localStorage.getItem('uid')+"&project="+ququ+"&token="+localStorage.getItem('token')+"&type=delete",shanchu);//删除接口
            for(var i=0;i<sArr.length;i++){
                if(sArr[i] == ququ){
                    sArr.splice(i,1);
                }
            }
            $(this).closest('li').data('flag','0') ;
            $(this).closest('li').find(".tianJia i").html('预约');
        }
	});

    /*$('.less').tap(function(){//项目页内容 取消 点击事件

    })*/
}

function hunshu(adddd){
	if(adddd.resultnum == 0){//成功将项目添加到用户id中
        $('#yueShu').css('display','block');
        if( window.localStorage ){//改变预约单里面项目的个数
            $('#yueShu').html(Number(localStorage.getItem('sNum'))+1);
            localStorage.setItem('sNum',Number(localStorage.getItem('sNum'))+1);
        }
	}
	else if(adddd.resultnum == '119'){
		window.location.href = 'denglu.html';
	}
	else{
		alert(adddd.result_mess);
	}
}

function shanchu(deletee){//项目页中取消预约
    if(deletee.resultnum == 0){
        if( window.localStorage ){//改变预约单里面项目的个数
            $('#yueShu').html(Number(localStorage.getItem('sNum'))-1);
            localStorage.setItem('sNum',Number(localStorage.getItem('sNum'))-1);
            if($('#yueShu').html() == 0){
                $('#yueShu').css('display','none');
            }
            else{
                $('#yueShu').css('display','block');
            }
        }
    }
    else{
        alert(deletee.result_mess+': '+deletee.resultnum);
    }
}

function haoAjax(type,url, data ,callback){//回调方法
	$.ajax({
		type:type,
		url:url,
		data:data,
		success:callback
	})
};


if(getCookie("cok1")){
	menuclick(getCookie("cok1"));
};
delCookie("cok1");
var oA = $("#footer a");
var oSpan = $("#footer span");


function menuclick(index_click){
	if(index_click != 4 ){
		delCookie("cok1");
		setCookie("cok1",index_click);
	}
	if(oA){
		oA.css("color","#989898");
		oSpan.css("color","#989898");
		oA.eq(index_click).css("color","#F97FA4");
		oSpan.eq(index_click).css("color","#F97FA4");
	}
	if(index_click==2){
		setCookie('yanzhi','6');
		if( !getCookie("users") ){
			window.location.href = "denglu.html";
		}else{
            if(window.localStorage.getItem('isface')==0){
                window.location.href='yanzhi-yindao.html';
            }else {
                window.location.href='yanzhi.html';
            }
        }
    }
    if(index_click == 3){
        if( !getCookie("users") ){
            window.location.href = "yanzhiquan.html";
        }
        else{
            window.location.href = "geren.html";
        }
    }
	if( index_click == 4 ){
		setCookie('comeon','1');
		if( !getCookie("users") ){
			window.location.href = "denglu.html";
		}
		else{
			window.location.href = "geren.html";
		}
	}
};

//---------------以上是存取cookie和底部菜单点击效果----------------------------------------------------------
window.onload = function(){

	if(document.getElementById("action1")){
		var shuru = document.getElementById("shuru");
		var local = document.getElementById("local");
		var proIcon = document.getElementById("proIcon");
		var action1 = document.getElementById("action1");
		var action2 = document.getElementById("action2");
		action1.style.borderRight = "1px solid #d6d7dc";
		action2.style.borderBottom = "1px solid #d6d7dc";
		var ua = navigator.userAgent.toLowerCase();	
		if (/iphone|ipad|ipod/.test(ua)) {
			shuru.style.marginTop = "0.25rem";
			local.style.marginRight = "0.4rem";
			$('#sao').css('margin-top','0.65rem');
		} else if (/android/.test(ua)) {
			shuru.style.marginTop = "0.22rem";
			local.style.marginRight = "0.4rem";
			$('#sao').css('margin-top','0.65rem');
		};
	}
}
//-------------以上是搜索栏手机适配(结束)-----------------------------------------------------------

