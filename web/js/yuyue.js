$(function(){
	$("#footer a").css("color","#989898");
	$("#footer span").css("color","#989898");
	$("#footer a").eq(3).css("color","#F97FA4");
	$("#footer span").eq(3).css("color","#F97FA4");

	$(".makeYue1").tap(function(){  //点击项目预约，跳到项目页
		oA.css("color","#989898");
		oSpan.css("color","#989898");
		oA.eq(1).css("color","#F97FA4");
		oSpan.eq(1).css("color","#F97FA4");
	});
	
	$('.history').tap(function(){
		if(!getCookie('users')){
			setCookie('camea','5');
			window.location.href = 'denglu.html';
		}
		else{
			window.location.href = 'lishiyuyue.html';
		}
	});
});
function yueAjax(type,url, data ,callback){
	$.ajax({
		type:type,
		url:url,
		data:data,
		success:callback
	});
};

var proMu = null;  //预约类型
var proImg = null; //预约状态图片

if( !getCookie("users") ){  //上来就判断用户有没有登录，没有（去逛逛），有登录，没有预约（去逛逛），有登录有预约（显示预约）
	$("#everNone").append('<img src="../img/yuyue01.png" /><p>还没有项目预约~</p><a href="xiangmu.html">去逛逛</a>');
	$("#nowYue").get(0).style.display = "none";
}else{
	$("#everNone").get(0).style.display = "none";
	yueAjax("post","http://www.buruwo.com/index.php?g=Portal&m=subscribe&a=Historyrecord","uid="+localStorage.getItem('uid'),historyYue);
	function historyYue(three){
		if( three.resultnum == 0 ){
			for( var i = 0 ; i<three.result.length; i++ ){
				if( three.result[i].subtype == 2 ){
					proMu = "预约项目";
				}else if( three.result[i].subtype == 1 ){
					proMu = "预约医生";
				};
				if( three.result[i].status == 0 && three.result[i].subtype == 2 ){
					proImg = "../img/yueSuccess.png";
				}else if( three.result[i].status == 1 && three.result[i].subtype == 2 ){
					proImg = "../img/yueFail.png";
				}else if( three.result[i].status == 2 && three.result[i].subtype == 2 ){
					proImg = "../img/yueWaite.png";
				}else if( three.result[i].status == 0 && three.result[i].subtype == 1 ){
					proImg = "../img/yishi04.png";
				}else if( three.result[i].status == 1 && three.result[i].subtype == 1 ){
					proImg = "../img/yishi01.png";
				}else if( three.result[i].status == 2 && three.result[i].subtype == 1 ){
					proImg = "../img/yishi03.png";
				}else if( three.result[i].status == 5 && three.result[i].subtype == 2 ){
					proImg = "../img/icon1-quxiao.png";
				}else if( three.result[i].status == 5 && three.result[i].subtype == 1 ){
					proImg = "../img/icon-quxiao.png";
				};
				$("#nowYue").append('<p id='+ three.result[i].id +' stype='+ three.result[i].subtype +' num='+ three.result[i].num +'><a href="#"><time>'+ three.result[i].day +'</time><img src='+ proImg +'><span>'+ proMu +'</span></a></p>');
				$("#nowYue p").tap(function(){
					if( $(this).attr('stype') == 2 ){
						setCookie('hisNum',$(this).attr('num'));
						setCookie('hisStype',$(this).attr('stype'));
						window.location.href = "yuyuexiangqing.html";
					}else if( $(this).attr('stype') == 1 ){
						setCookie('cok3',1);
						setCookie('hisNum',$(this).attr('num'));
						setCookie('hisStype',$(this).attr('stype'));
						window.location.href = "yuyuedan.html";
					}
				});
			};
		}else{
			$("#everNone").get(0).style.display = "block";
			$("#everNone").append('<img src="../img/yuyue01.png" /><p>还没有历史预约~</p><a href="xiangmu.html">去逛逛</a>');
			$("#nowYue").get(0).style.display = "none";
		};
	};
};

















