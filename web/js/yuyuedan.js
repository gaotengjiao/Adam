var arry= new Array();
var onoff = false;
var sonoff= true; //控制‘编辑’是否可点的开关
var ssonoff = false; //控制‘编辑’是否变成完成的开关
var oDate = new Date();
var arr1 = new Array();
var aArr = [];

function yueAjax(type,url,data,callback){
	$.ajax({
		type:type,
		url:url,
		data:data,
		success:callback
	});
};
$(function(){
	var oDate = new Date();
	var str = oDate.getFullYear()+'-'+toZero(oDate.getMonth()+1)+'-'+toZero(oDate.getDate()+1);
	function toZero(n){
		if(n<10){
			return '0'+n;
		}
		else{
			return ''+n;
		}
	}	
	$('.chooseRili input').val(str);
	$('.pro_title span').click(function(){ //点击编辑变为完成，互相切换
		if(ssonoff == false && sonoff == true){
			$('.wz_delete').css('display','block');
			$(this).html('完成');
			$('.chooseTime').css('display','none');
			$('#nowYuyue input').css('background','red');
			$('#nowYuyue input').val('立即删除');
			ssonoff = true;
		}
		else if(ssonoff == true&&sonoff == true){
			$('.wz_delete').css('display','none');
			$(this).html('编辑');
			$('.chooseTime').css('display','block');
			$('#nowYuyue input').css('background','#f97fa4');
			$('#nowYuyue input').val('立即预约');
			arr1.length = 0;
			ssonoff = false;
		}
	});
	$('#in').mdater({//时间选择器
		minDate : new Date(oDate.getFullYear(), oDate.getMonth(), oDate.getDate()+1)
	});
	
});

yueAjax("post","http://www.buruwo.com/index.php?g=Portal&m=shopping&a=ReservationList","uid="+localStorage.getItem('uid')+"&token="+localStorage.getItem('token')+"&type=select",chaxu);

function chaxu(danZi){
	//alert(danZi.resultnum);
	if(danZi.resultnum!=0){
		if(danZi.resultnum==404){
			//alert('您的预约单里没有任何项目呦，快去添加吧 ~~');
			$('.shoushu').append('<p class="tips33">您的预约单里没有任何项目呦，快去添加吧 ~~</p>');
			$('.tips33').css('width','100%').css('height','1rem').css('line-height','1rem');
			$('.tips33').css('text-align','center').css('font-size','0.6rem').css('color','#898989');
			if(danZi.result.length==0){
				$("#nowYuyue input").attr('disabled','true');
				$("#nowYuyue input").css('background','#CCCCCC');
				sonoff = false;
			}
		}
	}else{
		//alert(danZi.result.length);
		for(var  m=0; m<danZi.result.length; m++){
			$("#ss_wz").append("<section class='weizheng' id="+ danZi.result[m].id +" ><input class='lala' name='aid2' type='hidden' value="+danZi.result[m].aid+"><input  name='tpid' type='hidden' value="+danZi.result[m].typeid+"><div class='wz_main'><div class='wz_title' >"+ danZi.result[m].typename +"</div><div class='wz_content clear'><div class='wz_delete'></div><div class='wz_image'><img src="+ danZi.result[m].img +"><img src='../img/qianglituijian.png'><a href='javascript:;'></a></div><div class='wz_text'><h3>"+ danZi.result[m].gname +"</h3><p>"+ danZi.result[m].about +"</p><span>"+ danZi.result[m].keyword +"</span><a href='javascript:;'></a></div></div></div></section>");
			if( danZi.result[m].aid != 0 ){
				$("#ss_wz").append("<div class='wz_activity clear'><p>颜值立减活动</p><span><img src='../img/into.png'></span><a href="+danZi.result[m].activelink+"></a></div>");	
			}
			var idd=null;
			var typeidd=null;
			var aidd = null;
			idd = danZi.result[m].id;
			typeidd = danZi.result[m].typeid;
			aidd = danZi.result[m].aid;
			var jsonq={'id':idd,'typeid':typeidd,'aid':aidd};
			arry.push(JSON.stringify(jsonq));
			localStorage.removeItem("datavalue1");
            aArr.push(danZi.result[m].id);
		}
		$('.wz_content a').tap(function(){//查看项目详情
			setCookie('cok2',$(this).closest('.weizheng').attr('id'));
			window.location.href = 'xiangqingye.html';
		});
		
		onoff = true;
		$(".shoushu").append('<div class="tishi">已经到底啦~</div>');
		$('.wz_delete').tap(function(){
			if($(this).attr('class') != 'wz_delete wz_change'){
				//alert($(this).closest('.weizheng').attr('id'));
				$(this).addClass('wz_change');
				for(var g=0;g<danZi.result.length;g++){
					//alert(danZi.result[g].id);
					if(danZi.result[g].id == $(this).closest('.weizheng').attr('id')){
						if(arr1.length == 0){
							arr1.push(danZi.result[g].id);
						}
						else{
							for(var d=0;d<arr1.length;d++){
								if($(this).closest('.weizheng').attr('id')!=arr1[d]){
									arr1.push(danZi.result[g].id);
								}
							}
						}
					}
				}
				if(ssonoff == false){
					$('.pro_title span').tap(function(){
						$('.wz_delete').removeClass('wz_change');
						arr1.length = 0;
					});
				}
			}
			else{
				$(this).removeClass('wz_change');
				for(var d=0;d<arr1.length;d++){
					if(arr1[d] == $(this).closest('.weizheng').attr('id')){
						arr1.splice(d,1);
					}
				}
			}
		});
	};
};

function shanchu(sData){//项目删除  成功函数
	if(sData.resultnum == 0){
		//alert('删除成功');
		for(var a=0; a<$('.wz_change').length;a++){//循环标记删除的项目；
			var e = $('.wz_change').index();
			for(var b=0;b<arry.length;b++){//循环ajax数组arry;
				if(arry[b].id == $('.wz_change').eq(a).closest('.weizheng').attr('id')){
					arry.splice(b,1);//删除数组中对应的那条数据
				}
				else{
				}
			}
			for(var i=0;i<aArr.length;i++){//循环localStorage.getItem('ceshi')
				//if(JSON.parse(localStorage.getItem(i))){
					if(aArr[i]==$('.wz_change').eq(a).closest('.weizheng').attr('id')){//找到要删除的localstorage中的对应的那项
						//localStorage[i]= JSON.stringify({fuid:[{'ziid':'0'},{'xiaoid':'0'}]});//找到后  项目ID重置成0
						localStorage.setItem('sNum',Number(arry.length));
						if( $('.wz_change').eq(a).closest('.weizheng').find('.lala').val() != 0 ){//判断要删除的那项是否有颜值立减活动
							$('.wz_change').eq(a).closest('.weizheng').next().remove();
						}
						$('.wz_change').eq(a).closest('.weizheng').css('transition','0.6s');
						$('.wz_change').eq(a).closest('.weizheng').css('transform','scale(0.5) translateX(30rem)');//项目删除的效果
						setTimeout(function(){//效果执行完 删除项目
							$('.wz_change').eq(e).closest('.weizheng').remove();
							if($('.weizheng').length == 0){
								$(".tishi").html('还没有添加任何项目哟~');
                                $("#nowYuyue input").attr('disabled','true');
                                $("#nowYuyue input").css('background','#CCCCCC');
                                $('.pro_title span').html('编辑');
                                $('.chooseTime').css('display','block');
                                sonoff = false;
								localStorage.removeItem('arr');
                                localStorage.setItem('sNum',0);
                                window.location.reload();
							}
						},500);	
					}
				//}
			}
		}
	}
	else{
		alert(sData.result_mess);
	}
}

$("#nowYuyue").tap(function(ev){
	if(onoff == true){//ajax执行完  onoff=true;
		if(ssonoff == true){//编辑点击之后，ssonoff=false; 立即预约按钮改成立即删除；
			var sstring = arr1.join(',');
			//alert(sstring);
			yueAjax("post","http://www.buruwo.com/index.php?g=Portal&m=shopping&a=ReservationList","uid="+localStorage.getItem('uid')+"&project="+sstring+"&token="+localStorage.getItem('token')+"&type=delete",shanchu);
			//后端删除
		}
		else{
			if( !localStorage.getItem('datavalue1')){
				var yueDay = confirm("您没有选日期，默认预约为明天！！");
				if(yueDay == true){
					localStorage.setItem('datavalue1',oDate.getFullYear()+'-'+toZero(oDate.getMonth()+1)+'-'+toZero(oDate.getDate()+1)) ;
					function toZero(n){
						if(n<10){
							return '0'+n;
						}
						else{
							return ''+n;
						}
					};
					var allId = {
						"oneId":arry.toString(),
						"uid":localStorage.getItem('uid'),
						"time":localStorage.getItem('datavalue1')
					};
					localStorage['allId'] = JSON.stringify(allId);
					var pid=JSON.parse(localStorage.getItem('allId')).oneId.toString();
					//alert(localStorage.getItem('datavalue1'));
					var uuid=JSON.parse(localStorage.getItem('allId')).uid;
					var tie=JSON.parse(localStorage.getItem('allId')).time;
					yueAjax("post","http://www.buruwo.com/index.php?g=Portal&m=subscribe&a=SalesSupport","projectid="+pid+"&uid="+uuid+"&time="+tie,nowYue1);
					function nowYue1(proYue1){
						//alert('proYue1: '+proYue1.num);
						//proYue1=JSON.parse(proYue1);
						localStorage.setItem("bianHao",proYue1.num);
						setCookie('panDuan',100); //存cookie,跳转页面时，判断是从预约单还是从历史预约
						window.location.href="yuyuexiangqing.html";
					};
					/*for(var i=1;i<Number(localStorage.getItem('ceshi'))+1;i++){
						localStorage.removeItem(i.toString());
					};
					localStorage.removeItem("ceshi");*/
					localStorage.setItem("sNum",0);
				}
				else{
					//alert('您已取消');
				}
			}
			else{
				var allId = {
					"oneId":arry.toString(),
					"uid":localStorage.getItem('uid'),
					"time":localStorage.getItem('datavalue1')
				};
				localStorage['allId'] = JSON.stringify(allId);
				var pid=JSON.parse(localStorage.getItem('allId')).oneId.toString();
				//alert(localStorage.getItem('datavalue1'));
				var uuid=JSON.parse(localStorage.getItem('allId')).uid;
				var tie=JSON.parse(localStorage.getItem('allId')).time;
				yueAjax("post","http://www.buruwo.com/index.php?g=Portal&m=subscribe&a=SalesSupport","projectid="+pid+"&uid="+uuid+"&time="+tie,nowYue1);
				function nowYue1(proYue1){
					alert('proYue1: '+proYue1.num);
					//proYue1=JSON.parse(proYue1);
					localStorage.setItem("bianHao",proYue1.num);
					setCookie('panDuan',100); //存cookie,跳转页面时，判断是从预约单还是从历史预约
					window.location.href="yuyuexiangqing.html";
				};
				/*for(var i=1;i<Number(localStorage.getItem('ceshi'))+1;i++){
					localStorage.removeItem(i.toString());
				};
				localStorage.removeItem("ceshi");*/
				localStorage.setItem("sNum",0);
			}
		}
	}
});


//if(document.getElementById("pYuyue")){ //医生预约和项目预约选项卡切换
//	var pYuyue = document.getElementById("pYuyue");
//	var haoH3 = pYuyue.getElementsByTagName("h3");
//	var twoArticle = document.getElementsByTagName("article");
//	for( var s = 0; s<twoArticle.length; s++ ){
//		haoH3[s].index = s;  //添加索引值,记录下标
//		haoH3[s].onclick = function (){
//			for( var s = 0; s<twoArticle.length; s++ ){
//				haoH3[s].style.color = "#f97fa4";
//				twoArticle[s].style.display = "none";
//				haoH3[s].style.background = "#fff"
//			};
//			this.style.color = "#fff";
//			this.style.background = "#f97fa4";
//			twoArticle[this.index].style.display = "block";
//			if( this.index == 1 ){
//				$("#bian").attr("class",'xiaoshi');
//			}else{
//				$("#bian").attr("class",'chuxian');
//			};
//		};
//	};
//};
if( getCookie('panDuan2') == 101 ){ //判断此处是从预约项目进入预约单的
	delCookie('panDuan2');
	var docId = getCookie('yishi');
	yueAjax("post","http://www.buruwo.com/index.php?g=Portal&m=subscribe&a=Subscribetime","doctorid="+docId,yiMess);
	function yiMess(helo){ 
		helo=JSON.parse(helo);
		$("#docYue").html("");
		if( getCookie('cok6') ){
			$("#docYue").append('<section class="dNumber">预约编号：'+ getCookie('cok5') +'</section><section class="dXiangqing">预约详情</section><section class="dDate">预约日期：2016-'+ getCookie('cok6').substr(2) +'<time>'+ getCookie('cok7') +'</time><span>修改</span></section><section class="dDoctor"><i>一键帮助</i><img src="'+helo.user.user_url+'" /><h2><span>'+helo.user.user_login+'</span>'+helo.user.user_nicename+'</h2><h3>北京思妍微整医院</h3></section><section class="dCancel"><a href="#"><input type="button" value="取消预约"></a></section>');
		}
		$(".dCancel").click(function(){ //点击取消医生预约
			var sure = confirm("您确定要取消医师预约吗？");
			if(sure == true){
				yueAjax("post","http://www.buruwo.com/index.php?g=Portal&m=subscribe&a=CancelSub","num="+getCookie('cok5')+"&uid="+localStorage.getItem('uid')+"&token="+localStorage.getItem('token')+"&type=doctorcancel",yiXiao);
				function yiXiao(yinone){
					if(yinone.resultnum == 0){
						alert("取消成功");
						$("#docYue").html("");
						delCookie("cok5");
						delCookie("cok6");
						delCookie("cok7");
						delCookie("cok8");
					};
				};
			};
		});
		$(".dDate span").tap(function(){  //点击修改预约时间
			setCookie("sok5",getCookie('cok5'));
			window.location.href = "lijiyuyue.html"
		});
	};
}else{  //此处是从历史预约进入预约单医生预约详情的
	yueAjax("post","http://www.buruwo.com/index.php?g=Portal&m=subscribe&a=BookingDetails","num="+getCookie('hisNum')+"&uid="+localStorage.getItem('uid')+"&type="+getCookie('hisStype')+"&token="+localStorage.getItem('token'),hisYi);
	function hisYi(yiSheng){
		if( yiSheng.resultnum == 0 ){
			$("#docYue").html("");
			for( var n = 0; n < yiSheng.result.length; n++ ){
				$("#docYue").append('<section class="dNumber">预约编号：'+ yiSheng.result[n].num +'</section><section class="dXiangqing">预约详情</section><section class="dDate">预约日期：'+ yiSheng.result[n].day +'<time>'+ yiSheng.result[n].time +'</time><span>修改</span></section><section class="dDoctor"><i>一键帮助</i><img src="'+yiSheng.result[n].img+'" /><h2><span>'+yiSheng.result[n].username+'</span>'+yiSheng.result[n].jobtitle+'</h2><h3>北京思妍微整医院</h3></section><section class="dCancel"><a href="#"><input type="button" value="取消预约"></a></section>');
			};
		};
		$(".dCancel").click(function(){ //点击取消医生预约
			var sure = confirm("您确定要取消医师预约吗？");
			if(sure == true){
				yueAjax("post","http://www.buruwo.com/index.php?g=Portal&m=subscribe&a=CancelSub","num="+getCookie('cok5')+"&uid="+localStorage.getItem('uid')+"&token="+localStorage.getItem('token')+"&type=doctorcancel",yiXiao);
				function yiXiao(yinone){
					if(yinone.resultnum == 0){
						alert("取消成功");
						$("#docYue").html("");
						delCookie("cok5");
						delCookie("cok6");
						delCookie("cok7");
						delCookie("cok8");
					};
				};
			};
		});
		$(".dDate span").tap(function(){  //点击修改预约时间
			setCookie("sok5",getCookie('cok5'));
			window.location.href = "lijiyuyue.html"
		});
	};
}




