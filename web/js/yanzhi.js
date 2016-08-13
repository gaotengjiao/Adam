$(function(){
	delCookie('comeon');
	$("#footer a").css("color","#989898");
	$("#footer span").css("color","#989898");
	$("#footer a").eq(2).css("color","#F97FA4");
	$("#footer span").eq(2).css("color","#F97FA4");
	var uid=parseInt(window.localStorage.getItem('uid'));
	var token=parseInt(window.localStorage.getItem('token'));
	$.post('http://www.buruwo.com/index.php?g=Portal&m=FaceScore&a=index&uid='+uid+'&token='+token,function (data) {
		console.log(data);
		$('.zong').text('总额度 '+parseInt(data.result.limit).toFixed(2));
		if (data.result.ishavebill==0){
			$('.dai_num').text('('+parseInt(data.result.billamount).toFixed(2)+'元)');
			window.sessionStorage.setItem('billnum',data.result.billnum);
			window.sessionStorage.setItem('dai',data.result.billamount);
		}else{
			$('.dai_num').text('(0.00元)');
			$('.huan').attr('href','javascript:;');
		}
		$('.pie_progress').attr('data-goal',parseInt(data.result.ramining));
		//进度条
		$('.pie_progress').asPieProgress({
			namespace: 'pie_progress',
			max: parseInt(data.result.limit),   //最大值
			goal: parseInt(data.result.limit),  //总数
			delay: 300, //延迟
			easing: 'ease'//easing效果
		});
		$('.pie_progress').asPieProgress('start');
	},'json');
	$.post('http://www.buruwo.com/wx/wx.php',{url:window.location.href},function (data) {
		var img={
			uid:window.localStorage.getItem('uid'),
			token:window.localStorage.getItem('token'),
			type:0,
			accessToken:data.accessToken,
			serverId:{}
		};
		var images = {
			localId: [],
			serverId: []
		};
		console.log(data);
		wx.config({
			debug: false, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
			appId: data.appId,
			timestamp: data.timestamp,
			nonceStr: data.nonceStr,
			signature: data.signature,
			jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone', 'startRecord', 'stopRecord', 'onVoiceRecordEnd', 'playVoice', 'pauseVoice', 'stopVoice', 'onVoicePlayEnd', 'uploadVoice', 'downloadVoice', 'chooseImage', 'previewImage', 'uploadImage', 'downloadImage', 'translateVoice', 'getNetworkType', 'openLocation', 'getLocation', 'hideOptionMenu', 'showOptionMenu', 'hideMenuItems', 'showMenuItems', 'hideAllNonBaseMenuItem', 'showAllNonBaseMenuItem', 'closeWindow', 'scanQRCode', 'chooseWXPay', 'openProductSpecificView', 'addCard', 'chooseCard', 'openCard'] // 必填，需要使用的JS接口列表，所有JS接口列表见附录2
		});
		wx.ready(function () {
			document.querySelector('#saoyisao').onclick=function () {
				wx.scanQRCode({
					needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
					scanType: ["qrCode","barCode"], // 可以指定扫二维码还是一维码，默认二者都有
					success: function (res) {
						var result = res.resultStr; // 当needResult 为 1 时，扫码返回的结果
						window.sessionStorage.setItem('num',result);
						window.location.href='shouyintai.html';
					}
				});
			}

		})
	},'json');
});

