$(function(){
	var id = null;
	var iName = null;
	var iNumber = null;
	var iPhone = null;
	var iPoints = null;
	var iLevel = null;
	var influence = null;
	var iOpenid = null;
	var iStarttime = null;
	var iNickname = null;
	var iSex = null;
	var iUser_img = null;
	
	var ua = navigator.userAgent.toLowerCase();	
	if (/iphone|ipad|ipod/.test(ua)) {
		$('.tie_right').css('height','2.2rem');
		$('.tie_right').css('line-height','2.2rem');
	} else if (/android/.test(ua)) {
		$('.tie_right').css('height','2.2rem');
		$('.tie_right').css('line-height','2.2rem');
		$('.tie_right').css('margin-right','0.2rem');
		$('.tie_left input').css('margin-top','0.2rem');
	}
	
	$('.tie_right').tap(function(){//获取验证码事件
		if(cOnoff == true){
			sendNum = $('.tie_number input').val();
			if(!sendNum){
				alert("手机号码不能为空");
			}
			else{
				if(!isPhone($('.tie_number input'))){
					$('.tie_number').css('border-color','red');
				}
				else{
                    console.log(localStorage.getItem('uid')+':'+localStorage.getItem('token'));
					$('.tie_number').css('border-color','#d6d7dc');
					fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Login&a=Register","phone="+sendNum,callback);
				}
			}	
		} 
	});
	
	$('.tie_number input').blur(function(){//失去光标事件
		sendNum = $('.tie_number input').val();
		if(!sendNum){
			$('.tie_number').css('border-color','red');
			$('.tie_tips1').html('号码不能为空');
			$('.tie_tips1').css('display','block');
		}
		else{
			if(!isPhone($('.tie_number input'))){
				$('.tie_number').css('border-color','red');
				$('.tie_tips1').html('您输入的手机号码有误！');
				$('.tie_tips1').css('display','block');
			}
			else{
				$('.tie_number').css('border-color','#d6d7dc');
				$('.tie_tips1').css('display','none');
				fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Login&a=SoleUname","phone="+sendNum,weiyi);
			}
		}
	});
})

function callback(phone){//向后端发送请求  返回成功函数
	if(phone.resultnum == 0){
		alert(phone.code);
		cOnoff = false;
		setCookie('number',phone.code);
		sendCode($('.tie_right'));
		
		$('.tie_btn').tap(function(){//绑定按钮点击事件
			
			if(!bangding($('.tie_number input'),$('.tie_left input'),$('.tie_mima input'))){
				return false;
			}
			else if(!isPhone($('.tie_number input'))){
				$('.tie_number').css('border-color','red');
				$('.tie_tips1').html('您输入的手机号码有误！');
				$('.tie_tips1').css('display','block');
			    return false;
			}
			else if(!checkyanz($('.tie_left input'))){
				$('.tie_left').css('border-color','red');
				$('.tie_tips2').html('您输入的验证码有误！');
				$('.tie_tips2').css('display','block');
			    return false;
			}
			else if(!checkpass($('.tie_mima input'))){
				$('.tie_tips3').html('密码长度须在6-20之间!！');
				$('.tie_tips3').css('display','block');
				$('.tie_mima').css('border-color','red');
			    return false;
			} 
			else{
				sendNum = $('.tie_number input').val();
				sendPass = $('.tie_mima input').val();
				$('.tie_number').css('border-color','#d6d7dc');
				$('.tie_left').css('border-color','#d6d7dc');
				$('.tie_mima').css('border-color','#d6d7dc');
				$('.tie_tips1').css('display','none');
				$('.tie_tips2').css('display','none');
				$('.tie_tips3').css('display','none');
				fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Login&a=UserBound",'phone='+sendNum+'&uid='+localStorage.getItem('uid')+'&token='+localStorage.getItem('token')+'&code='+getCookie('number')+'&pass='+sendPass+'&type='+localStorage.getItem('weiyi'),tieFunc);
			}
			
		});
	}
}

function tieFunc(tData){
	console.log(tData.result.uid);
	if(tData.resultnum == 0){
		setCookie('users',tData.result.iphone);
		localStorage.setItem('uid',tData.result.uid);
		localStorage.setItem('iName',tData.result.name);
		localStorage.setItem('iNumber',tData.result.number);
		localStorage.setItem('iPhone',tData.result.iphone);
		localStorage.setItem('iPoints',tData.result.points);
		localStorage.setItem('iLevel',tData.result.level);
		localStorage.setItem('influence',tData.result.influence);
		localStorage.setItem('iOpenid',tData.result.openid);
		localStorage.setItem('iStarttime',tData.result.starttime);
		localStorage.setItem('iNickname',tData.result.nickname);
		localStorage.setItem('iSex',tData.result.sex);
		localStorage.setItem('iUser_img',tData.result.user_img);
		localStorage.setItem('token',tData.result.token);
		jiance(getCookie('gogo'));
		jiance(getCookie('comeon'));
		jiance(getCookie('lalala'));
		jiance(getCookie('mingJ'));
		jiance(getCookie('yiYue'));
		jiance(getCookie('camea'));
	}
}

function bangding(numberI,leftI,mimaI){//验证是否为空
	userNumb = numberI.val();
	userYanz = leftI.val();
	userPass = mimaI.val();
  	if (userNumb == "" || userNumb == null){
  		$('.tie_number').css('border-color','red');
  		$('.tie_tips1').html('号码不能为空');
  		$('.tie_tips1').css('display','block');
    	//alert("号码不能为空");
    	return false;
  	}
  	else if(userYanz == "" || userYanz == null){
  		$('.tie_left').css('border-color','red');
  		$('.tie_tips2').html('验证码不能为空');
  		$('.tie_tips2').css('display','block');
  		//alert("验证码不能为空");
    	return false;
  	}
  	else if(userPass == "" || userPass == null){
  		$('.tie_mima').css('border-color','red');
  		$('.tie_tips3').html('密码不能为空');
  		$('.tie_tips3').css('display','block');
    	//alert("密码不能为空");
    	return false;
  	}
  	else{
    	return true;
  	}
}

function weiyi(weiyiData){//验证唯一性
	if(weiyiData.resultnum == 0){
        localStorage.setItem('weiyi',1);
		$('.tie_number').css('border-color','#d6d7dc');
	}
	else if(weiyiData.resultnum == 109){
        localStorage.setItem('weiyi',2);
		$('.tie_tips1').html('号码已经注册过了哦。可继续绑定。');
        $('.tie_tips1').css('display','block');
	}
}
