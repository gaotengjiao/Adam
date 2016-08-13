$(function(){
	if(!localStorage.getItem('sNum')){
		$('#yueShu2').html(0);
	}
	else{
		$('#yueShu2').html(localStorage.getItem('sNum'));
	}
	
	var ua = navigator.userAgent.toLowerCase();	
	if (/iphone|ipad|ipod/.test(ua)) {
		$('.mingYi2').css('top','0.21rem');
	} else if (/android/.test(ua)) {
		$('.mingYi2').css('top','0.3rem');
	}
	
	haoAjax("post","http://www.buruwo.com/index.php?g=Portal&m=subscribe&a=Doctor","typeid=2",shifu);
})
function shifu(tuer){
	tuer = JSON.parse(tuer);
	for(var i = 0; i<tuer.length; i++){
		$('.shiList').append("<li id="+ tuer[i].id +"><img src="+ tuer[i].user_url +"><h2>"+ tuer[i].user_login +"<span>"+ tuer[i].user_nicename +"</span></h2><h3>北京思妍美容整形医院</h3><a href='#'>"+ tuer[i].rold +"</a><div class='yiJia'><img src='../img/addB.png'/><span>预约</span></div></li>");
		if(tuer[i].recommend == 1){
			$('.benZhou').append('<img src="'+tuer[i].user_url+'">');
			$('.benPeople').append('<span>'+tuer[i].user_login+'</span>');
		}
	}
	$('.shiList li').tap(function(){
		var i = $(this).index();
		setCookie('yishi',tuer[i].id);
		window.location.href = "lijiyuyue.html";
	});
	$('.yiJia').tap(function(even){
		var i = $(this).index();
		setCookie('yishi',tuer[i].id);
		window.location.href = "lijiyuyue.html";
		//even.stopPropagation();
	});
}


function haoAjax(type,url, data ,callback){
	$.ajax({
		type:type,
		url:url,
		data:data,
		success:callback
	});
};
$(".yueMenu").tap(function(){
	if(!getCookie("users")){
		setCookie('mingJ',3);
		window.location.href = "denglu.html"
	}else{
		//alert(123);
		window.location.href = "yuyuedan.html";
	};
});




























