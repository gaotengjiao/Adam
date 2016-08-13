function haoAjax(type,url, data ,callback){
	$.ajax({
		type:type,
		url:url,
		data:data,
		success:callback
	});
};
var doctorid = null;
var oDay = null
var oTime = null;
var oTime2 = null;
$(function(){
	doctorid = getCookie('yishi');
	var $nowUl = null;
	haoAjax("post","http://www.buruwo.com/index.php?g=Portal&m=subscribe&a=Subscribetime","doctorid="+doctorid,queen);
})
function queen(king){
	king = JSON.parse(king);
	$('.doctor').append('<div><img src="'+king.user.user_url+'" /></div><h2><span>'+king.user.user_login+'</span>'+king.user.user_nicename+'</h2><h3>北京思妍微整医院</h3>');
	$('.weekList').append('<li><div>'+king.day.day1+'</div></li><li><div>'+king.day.day2+'</div></li><li><div>'+king.day.day3+'</div></li><li><div>'+king.day.day4+'</div></li><li><div>'+king.day.day5+'</div></li><li><div>'+king.day.day6+'</div></li><li><div>'+king.day.day7+'</div></li>');
	
	$('.weekList li').find('div').css('font-size','0.4rem');
	$('.weekList li').eq(0).attr('class','wChange');
	oDay = $('.weekList li').eq(0).text();
	for(var i = 0; i<king.time.length; i++){
		$('.comTime').eq(0).find('ul').append('<li>'+king.time[i].t_time+'</li>');
		if(king.time[i].trueor){
			$('.comTime').eq(0).find('ul').find('li').eq(i).attr('class','unChecked');
		}
	}
	$('.weekList li').tap(function(){
		var indexe = $(this).index()+1;
		var doctorid = getCookie('yishi');
		$nowUl = $('.comTime').eq(indexe-1).find('ul');
		$nowUl.html('');
		//alert(indexe);
		$('.doctor_yuyue div').css('background','#d9d9d9');
		haoAjax("post","http://www.buruwo.com/index.php?g=Portal&m=subscribe&a=Subscribetimetwo","doctorid="+doctorid+"&dayid="+indexe,baober);
		
		
	});	
	function baober(ererer){
		ererer = JSON.parse(ererer);
		for(var i = 0; i<ererer.time.length; i++){
			$nowUl.append('<li>'+ererer.time[i].t_time+'</li>');
			if(ererer.time[i].trueor){
				$nowUl.find('li').eq(i).attr('class','unChecked');
			}
		}
		$nowUl.find('li').tap(function(){
			if(this.className != 'unChecked' ){
				$('.doctor_yuyue div').css('background','#f97fa4');
				$(this).siblings('li').removeClass('cChange');
				$(this).attr('class','cChange');
				oTime = $(this).index();
				oTime2 = $(this).text();
			}
		});
	};
	tTime(0);
	$('.weekList li').tap(function(){
		var i = $(this).index();
		$('.weekList li').attr('class','');
		$(this).attr('class','wChange');
		oDay = $(this).text();
		oTime = null;
		$('.doctor_yuyue div').css('background','#d9d9d9');
		$('.wBall span').eq(0).css('left',(0.8+2.2*i)+'rem');
		$('.yuyueTime').css('left',(-16*i)+'rem');
		tTime(i);
		
	});
	function tTime(num){
		$('.comTime').eq(num).find('li').tap(function(){
			if(this.className != 'unChecked' ){
				$('.doctor_yuyue div').css('background','#f97fa4');
				$(this).siblings('li').removeClass('cChange');
				$(this).attr('class','cChange');
				oTime = $(this).index();
				oTime2 = $(this).text();
				
			}
		});
	};
};
setCookie('cok3',1);//存固定值，指定跳转预约医生页面
$(".doctor_yuyue div").tap(function(){ //预约医生和修改预约时间
    if( !getCookie("users") ){
		setCookie('yiYue','4');
		window.location.href = "denglu.html";
	}
	else{
		if( oDay == null || oTime == null ){
			confirm("请选择时间");
		}else{
			if( getCookie('sok5') ){
				setCookie('cok8',oTime);
				haoAjax("post","http://www.buruwo.com/index.php?g=Portal&m=subscribe&a=EditSubDoctor","num="+getCookie('sok5')+"&uid="+localStorage.getItem('uid')+"&day="+getCookie('cok6').substr(2)+"&time="+getCookie("cok8"),xiuGai);
				function xiuGai(gaiTime){
					alert(2);
					if( gaiTime.resultnum == 0 ){
						setCookie('cok5',getCookie('sok5'));
						delCookie('sok5');
						setCookie('cok6',oDay);
						setCookie('cok7',oTime2);
						setCookie('panDuan2',101);
						window.location.href = "yuyuedan.html";
					}else{
						confirm("修改失败");
					}
				};
			}else{
				haoAjax("post","http://www.buruwo.com/index.php?g=Portal&m=teacher&a=SubDoctor","uid="+localStorage.getItem('uid')+"&tid="+doctorid+"&day="+oDay+"&time="+oTime,fucBack);
				function fucBack(docMes){
					alert(1);
					if( docMes.resultnum == 0 ){
						setCookie("cok5",docMes.t_num);
						setCookie('cok6',oDay);
						setCookie('cok7',oTime2);
						setCookie('panDuan2',101);
						window.location.href = "yuyuedan.html";
					}
				};
			};
		}
	}
});


















	