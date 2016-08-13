
var ua = navigator.userAgent.toLowerCase();	
if (/iphone|ipad|ipod/.test(ua)) {
	$('.zc_right').css('height','1.8rem');
	$('.zc_right').css('line-height','1.8rem');
} else if (/android/.test(ua)) {
	$('.zc_right').css('height','2rem');
	$('.zc_right').css('line-height','2rem');
	$('.zc_right').css('margin-right','0.2rem');
	$('.zc_left input').css('margin-top','0.2rem');
}
//alert(getCookie('users'));
danji();

function danji(){//点击事件
	
	$('.zc_number i').tap(function(){//删除事件
		$('.zc_number input').val('');
	});
	
	$('.zc_number input').blur(function(){//失去光标事件
		sendNum = $('.zc_number input').val();
		if(!sendNum){
			$('.zc_number').css('border-color','red');
			alert("手机号码不能为空");
		}
		else{
			if(!isPhone($('.zc_number input'))){
				$('.zc_number').css('border-color','red');
			}
			else{
				//alert(789);
				fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Login&a=SoleUname","phone="+sendNum,weiyi);
			}
		}
	});
	
	$('.zc_right').tap(function(ev){//获取验证码事件
		ev.stopPropagation();
		if(cOnoff == true){
			sendNum = $('.zc_number input').val();
			if(!sendNum){
				$('.zc_number').css('border-color','red')
				//alert("手机号码不能为空");
				$('.zc_tips1').html('手机号码不能为空！');
  				$('.zc_tips1').css('display','block');
			}
			else{
				if(!isPhone($('.zc_number input'))){
					$('.zc_number').css('border-color','red');
					$('.zc_tips1').html('您的号码有误！');
  					$('.zc_tips1').css('display','block');
				}
				else{
					//alert(123);
					$('.zc_number').css('border-color','#d6d7dc');
					$('.zc_tips1').css('display','none');
					$('.zc_tips2').css('display','none');
					$('.zc_tips3').css('display','none');
					fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Login&a=Register","phone="+sendNum,callback);
				}
			}	
		} 
	});
	
	$('.xieyi span').eq(0).tap(function(){//是否同意协议事件
		if(zOnoff == true){
			zOnoff = false;
			$(this).css('background-image','url(../img/zcicon11.png)');
		}
		else{
			zOnoff = true;
			$(this).css('background-image','url(../img/zcicon14.png)');
		}
	});
}


function weiyi(weiyiData){
	if(weiyiData.resultnum == 0){
		$('.zc_number').css('border-color','#d6d7dc');
		$('.zc_tips1').css('display','none');
	}
	else if(weiyiData.resultnum == 109){
		var conf = confirm('您好，您已经是我们的会员了哦,请登录。');
		if(conf == true){
			window.location.href = 'denglu.html';
		}
	}
}

function callback(phone){//向后端发送请求  返回成功函数
	if(phone.resultnum == 0){
		alert(phone.code);
		cOnoff = false;
		setCookie('number',phone.code);
		sendCode($('.zc_right'));
		
		$('.zc_btn').tap(function(){//注册按钮点击事件
			
			if(!zhuce($('.zc_number input'),$('.zc_left input'),$('.zc_mima input'))){
				return false;
			}
			else if(!isPhone($('.zc_number input'))){
				$('.zc_number').css('border-color','red');
				$('.zc_tips1').html('您的号码有误！');
  				$('.zc_tips1').css('display','block');
			    return false;
			}
			else if(!checkyanz($('.zc_left input'))){
				$('.zc_left').css('border-color','red');
				$('.zc_tips2').html('您的验证码有误！');
  				$('.zc_tips2').css('display','block');
			    return false;
			} 
			else if(!checkpass($('.zc_mima input'))){
				$('.zc_tips3').html('您的密码有误！');
  				$('.zc_tips3').css('display','block');
				$('.zc_mima').css('border-color','red');
			    return false;
			} 
			else if(!zOnoff){
				alert('是否同意思妍隐私政策？');
			}
			else{
				sendPass = $('.zc_mima input').val();
				$('.zc_number').css('border-color','#d6d7dc');
				$('.zc_left').css('border-color','#d6d7dc');
				$('.zc_mima').css('border-color','#d6d7dc');
				$('.zc_tips1').css('display','none');
				$('.zc_tips2').css('display','none');
				$('.zc_tips3').css('display','none');
				fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Login&a=RegisterPost","phone="+sendNum+'&code='+getCookie('number')+'&type=1&pass='+sendPass,youCan);
			}
			
		});
	}
}

function zhuce(numberI,leftI,mimaI){//验证是否为空
	userNumb = numberI.val();
	userYanz = leftI.val();
	userPass = mimaI.val();
  	if (userNumb == "" || userNumb == null){
  		$('.zc_number').css('border-color','red');
  		$('.zc_tips1').html('电话号码不能为空！');
  		$('.zc_tips1').css('display','block');
    	//alert("号码不能为空");
    	return false;
  	}
  	else if(userYanz == "" || userYanz == null){
  		$('.zc_left').css('border-color','red');
  		$('.tie_tips2').html('验证码不能为空!');
  		$('.tie_tips2').css('display','block');
  		//alert("验证码不能为空");
    	return false;
  	}
  	else if(userPass == "" || userPass == null){
  		$('.zc_mima').css('border-color','red');
  		$('.tie_tips3').html('密码不能为空');
  		$('.tie_tips3').css('display','block');
    	//alert("密码不能为空");
    	return false;
  	}
  	else{
    	return true;
  	}
}

function youCan(lala){
	//alert(lala.resultnum);
	if(lala.resultnum == 0){
		setCookie('users',lala.result.iphone);
		localStorage.setItem('uid',lala.result.uid);
		localStorage.setItem('iName',lala.result.name);
		localStorage.setItem('iNumber',lala.result.number);
		localStorage.setItem('iPhone',lala.result.iphone);
		localStorage.setItem('iPoints',lala.result.points);
		localStorage.setItem('iLevel',lala.result.level);
		localStorage.setItem('influence',lala.result.influence);
		localStorage.setItem('iOpenid',lala.result.openid);
		localStorage.setItem('iStarttime',lala.result.starttime);
		localStorage.setItem('iNickname',lala.result.nickname);
		localStorage.setItem('iSex',lala.result.sex);
		localStorage.setItem('iUser_img',lala.result.user_img);
		localStorage.setItem('token',lala.result.token);
		localStorage.setItem('isface',lala.result.isface);
		jiance(getCookie('gogo'));
		jiance(getCookie('comeon'));
		jiance(getCookie('lalala'));
		jiance(getCookie('mingJ'));
		jiance(getCookie('yiYue'));
		jiance(getCookie('camea'));
	}
	else{
		alert(lala.result_mess);
	}
}

