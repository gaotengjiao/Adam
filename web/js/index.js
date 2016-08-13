//alert(localStorage.getItem('uid'));

$(function(){  //首页轮播自动/手动滑动
	delCookie('comeon');
	var oBanner = document.getElementById("banner");
	var oPicList = document.getElementById("bannerList");
	var allLi = oPicList.getElementsByTagName("li");
	var aBtns=document.getElementById("btn").children;
	var downLeft = 0;
	var downX = 0;
	var iNow = 0;
	var downTime = 0;
	var oTimer=0;
	var bBtn = true;
	var oDate = new Date(); 
	var w = allLi[0].offsetWidth;
	var timestamp = '';//时间戳
	var nonceStr = '';//随机字符串
	var GetShaStr='';//签名所需字符串
	var surl='http://www.buruwo.com/web/project/index.html';
	oPicList.style.width = allLi.length * w + 'px';
	oBanner.ontouchmove = function(ev){
		ev.preventDefault();
	}
	function autoPlay()
	{
		oTimer=setInterval(function(){
			iNow++;
			if( iNow > allLi.length-1 ){
				iNow = 0;
			};
			for( var i=0; i<aBtns.length; i++ ){
				aBtns[i].style.backgroundColor = "";
			};
			aBtns[iNow].style.background = "#F97FA4";
			startMove(oPicList,{left : -iNow*w},400,'easeOut');
		},2000);
		
	}
	autoPlay();
	oPicList.addEventListener("touchstart",fnStart,false);
	function fnStart(ev)
	{
		clearInterval(oTimer);
		clearInterval(oPicList.timer);
		var touchs = ev.changedTouches[0];
		downLeft = this.offsetLeft;
		downX = touchs.pageX;
		downTime = Date.now();
	};
	oPicList.addEventListener("touchmove",fnMove,false);
	function fnMove(ev){
		var touchs = ev.changedTouches[0];
			if( this.offsetLeft >= 0 ){
				if(bBtn){
					bBtn = false;
					downX = touchs.pageX;		
				}
				this.style.left = (touchs.pageX - downX)/3 + 'px';
			}
			else if(this.offsetLeft <= oBanner.offsetWidth - this.offsetWidth){
				if(bBtn){
					bBtn = false;
					downX = touchs.pageX;		
				}
				this.style.left = (touchs.pageX - downX)/3 + (oBanner.offsetWidth - this.offsetWidth) + 'px';
			}
			else{
				this.style.left = touchs.pageX - downX + downLeft + 'px';
			}
	};
	oPicList.addEventListener("touchend",fnEnd,false);
	function fnEnd(ev)
	{
		var touchs = ev.changedTouches[0];
			
			this.ontouchmove = null;
			this.ontouchend = null;
			if( downX < touchs.pageX ){  //→
				if( iNow != 0){
					if(touchs.pageX - downX > oBanner.offsetWidth/2 || Date.now() - downTime < 300 &&  touchs.pageX - downX > 30){
						iNow--;
					}
				}
				startMove(oPicList,{left : -iNow*w},400,'easeOut');
			
			}
			else{  //←
				if( iNow != allLi.length-1){
					if(downX - touchs.pageX > oBanner.offsetWidth/2 || Date.now() - downTime < 300 && downX - touchs.pageX > 30){
						iNow++;
					}
				}
				startMove(oPicList,{left : -iNow*w},400,'easeOut');
			}
			for( var i=0; i<aBtns.length; i++ ){
				aBtns[i].style.backgroundColor = "";
			};
			aBtns[iNow].style.background = "#F97FA4";
		autoPlay();
	};
	
	$('#nav').tap(function(){
		haoAjax("get","http://www.buruwo.com/index.php?g=Portal&m=Vxin&a=GetToken","",callback2);
	})
	
	function callback2(data){
		console.log(data.result.token);
		if(data.resultnum == 0){
			timestamp = data.result.time;
			nonceStr = data.result.rand;
			haoAjax("get","https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token="+data.result.token+"&type=jsapi","",callback);
		}
	}
	
	/*function callback(tdata){
		alert(tdata.ticket);
		GetShaStr=tdata.ticket+'&noncestr='+nonceStr+'&timestamp='+timestamp+'&url='+surl;
		var sha = hex_sha1(GetShaStr);
   	 	alert(sha); 
		wx.config({
		    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
		    appId: 'wxaf6078a557ceeeec', // 必填，公众号的唯一标识
		    timestamp: timestamp, // 必填，生成签名的时间戳
		    nonceStr: nonceStr, // 必填，生成签名的随机串
		    signature: sha,// 必填，签名，见附录1
		    jsApiList: [
			    onMenuShareTimeline,
				onMenuShareAppMessage,
				onMenuShareQQ,
				onMenuShareWeibo,
				onMenuShareQZone,
				startRecord,
				stopRecord,
				onVoiceRecordEnd,
				playVoice,
				pauseVoice,
				stopVoice,
				onVoicePlayEnd,
				uploadVoice,
				downloadVoice,
				chooseImage,
				previewImage,
				uploadImage,
				downloadImage,
				translateVoice,
				getNetworkType,
				openLocation,
				getLocation,
				hideOptionMenu,
				showOptionMenu,
				hideMenuItems,
				showMenuItems,
				hideAllNonBaseMenuItem,
				showAllNonBaseMenuItem,
				closeWindow,
				scanQRCode,
				chooseWXPay,
				openProductSpecificView,
				addCard,
				chooseCard,
				openCard
		    ] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
		});
	}*/
	
	function haoAjax(type,url, data ,callback){//回调方法
		$.ajax({
			type:type,
			url:url,
			data:data,
			success:callback
		})
	};
})
