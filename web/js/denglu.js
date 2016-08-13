
var clock = '';
var nums = 60;
var btn = '';
var dOnoff = true;
var sendNum = null;
var userNumbT = null;
var sendPass = null;
var userYanz = null;
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
var oDate = new Date();

if(localStorage.getItem('timer')&&localStorage.getItem('timer')!=""){
	fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Vxin&a=UserInfo","state="+localStorage.getItem('timer'),passWX);//第二个接口   后端用来判断是否有此时间戳  判断用户是否登录
}


denglu();
function denglu(){
	$('.dl_fangshi div').tap(function(){//登录方式切换
		var n = $(this).index();
		$('.dl_fangshi div').attr('class','');
		$(this).attr('class','nowPage')
		if( n == 0){
			$('.dl_one').css('display','block');
			$('.dl_two').css('display','none');
		}
		else{
			$('.dl_one').css('display','none');
			$('.dl_two').css('display','block');
		}
	});
	
	$('.dl_number i').tap(function(){//删除事件
		$('.dl_number input').val('');
	});
	
	$('.dl_numberT i').tap(function(){//删除事件
		$('.dl_numberT  input').val('');
	});
	
	$('.dl_right').tap(function(){//获取验证码事件
		if($('.dl_fangshi div').eq(1).attr('class') =='nowPage'){
			if(dOnoff == true){
				sendNum = $('.dl_numberT input').val();
				//alert(sendNum);
				if(!sendNum){
					$('.dl_numberT').css('border-color','red');
					$('.dl_tips3').html('手机号码不能为空！');
  					$('.dl_tips3').css('display','block');
					//alert("手机号码不能为空");
				}
				else{
					if(!isPhone()){
						$('.dl_numberT').css('border-color','red');
						$('.dl_tips3').html('手机号码输入有误！');
  						$('.dl_tips3').css('display','block');
					}
					else{
						//alert(789);
						$('.dl_numberT').css('border-color','#d6d7dc');
						$('.dl_tips3').css('display','none');
						fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Login&a=Register","phone="+sendNum,callback);
					}
				}	
			}
		}
	});
	
	$('.dl_btn').tap(function(){//登录按钮点击事件
		if($('.dl_fangshi div').eq(0).attr('class') =='nowPage'){
			if(!blank()){
				return false;
			}
			else if(!isPhone()){
				$('.dl_number').css('border-color','red');
				$('.dl_tips1').html('手机号码输入有误！');
  				$('.dl_tips1').css('display','block');
				//alert('请输入正确的手机号码！');
			    return false;
			}
			else if(!checkpass()){
				$('.dl_mima').css('border-color','red');
				$('.dl_tips2').html('密码输入有误！');
  				$('.dl_tips2').css('display','block');
			    return false;
			}
			else{
				sendNum = $('.dl_number input').val();
				sendPass = $('.dl_mima input').val();
				$('.dl_number').css('border-color','#d6d7dc');
				$('.dl_mima').css('border-color','#d6d7dc');
				$('.dl_tips1').css('display','none');
				$('.dl_tips2').css('display','none');
				fengAjax("post"," http://www.buruwo.com/index.php?g=Portal&m=Login&a=Login","phone="+sendNum+"&type=2&pass="+sendPass,passOk);
			}
		}
	});
	
	$('.dl_icon a').eq('0').tap(function(){//微信登陆
		var aData = oDate.getTime();
		localStorage.setItem('timer',aData);
		window.location.href = 'http://www.buruwo.com/index.php?g=Portal&m=Vxin&a=OpenIdVxin&type=vx&state='+aData;
		//fengAjax("post","http://www.buruwo.com/index.php?g=Portal&m=Vxin&a=OpenIdVxin&type=vx","state="+aData,passWXT);//第一个接口    给后端传时间戳   用来微信登陆
	});
		
	$('.dl_icon a').eq('1').tap(function(){//QQ登陆
		toQzoneLogin();
	});
};

function passWX(wData){
	//alert(wData.resultnum+' ，'+wData.result.iphone);
	if(wData.resultnum == 0){
		//alert(wData.result.iphone);
		if(wData.result.iphone != 0){
			setCookie('users',wData.result.iphone);
			localStorage.setItem('uid',wData.result.uid);
			localStorage.setItem('iName',wData.result.name);
			localStorage.setItem('iNumber',wData.result.number);
			localStorage.setItem('iPhone',wData.result.iphone);
			localStorage.setItem('iPoints',wData.result.points);
			localStorage.setItem('iLevel',wData.result.level);
			localStorage.setItem('influence',wData.result.influence);
			localStorage.setItem('iOpenid',wData.result.openid);
			localStorage.setItem('iStarttime',wData.result.starttime);
			localStorage.setItem('iNickname',wData.result.nickname);
			localStorage.setItem('iSex',wData.result.sex);
			localStorage.setItem('iUser_img',wData.result.user_img);
			localStorage.setItem('token',wData.result.token);
            localStorage.setItem('isface',wData.result.isface);
            jiance(getCookie('gogo'));
            jiance(getCookie('comeon'));
            jiance(getCookie('lalala'));
            jiance(getCookie('mingJ'));
            jiance(getCookie('yiYue'));
            jiance(getCookie('camea'));


			//if(!getCookie('gogo')&&!getCookie('comeon')&&!getCookie('lalala')&&!getCookie('mingJ')&&!getCookie('yiYue')&&!getCookie('camea')){
				//window.location.href = 'index.html';
			//}
		}
		else{
			//console.log(wData.result.uid +':'+ wData.result.token);
			localStorage.setItem('uid',wData.result.uid);
			localStorage.setItem('token',wData.result.token);
			window.location.href = 'tie.html';
		}
	}
	else if(wData.resultnum == 00110){
		window.location.href = 'denglu.html';
	}
}


function toQzoneLogin(){
	var A=window.open("www.buruwo.com/index.php?g=admin&m=user&a=qq_login","TencentLogin","width=450,height=320,menubar=0,scrollbars=1, resizable=1,status=1,titlebar=0,toolbar=0,location=1");
} 


function fengAjax(type,url,data,callback){
	$.ajax({
		type:type,
		url:url,
		data:data,
		success:callback
	})
};


function passOk(pdata){
	//pdata = JSON.parse(pdata);
	//console.log(pdata.result.iphone);
	if(pdata != ''||pdata != null){
		if(pdata.resultnum == 0){
			setCookie('users',pdata.result.iphone);
			localStorage.setItem('uid',pdata.result.uid);
			localStorage.setItem('iName',pdata.result.name);
			localStorage.setItem('iNumber',pdata.result.number);
			localStorage.setItem('iPhone',pdata.result.iphone);
			localStorage.setItem('iPoints',pdata.result.points);
			localStorage.setItem('iLevel',pdata.result.level);
			localStorage.setItem('influence',pdata.result.influence);
			localStorage.setItem('iOpenid',pdata.result.openid);
			localStorage.setItem('iStarttime',pdata.result.starttime);
			localStorage.setItem('iNickname',pdata.result.nickname);
			localStorage.setItem('iSex',pdata.result.sex);
			localStorage.setItem('iUser_img',pdata.result.user_img);
			localStorage.setItem('token',pdata.result.token);
            localStorage.setItem('isface',pdata.result.isface);
            jiance(getCookie('gogo'));
            jiance(getCookie('comeon'));
            jiance(getCookie('lalala'));
            jiance(getCookie('mingJ'));
            jiance(getCookie('yiYue'));
            jiance(getCookie('camea'));


			//alert(localStorage.getItem('uid'));
			//if(!getCookie('gogo') && !getCookie('comeon')&& !getCookie('lalala')&& !getCookie('mingJ')&& !getCookie('yiYue')&& !getCookie('camea')){
				//window.location.href = 'index.html';
			//}
		}
		else{
			alert(pdata.result_mess);
		}
	}
}

function callback(phone){//向后端发送请求  返回成功函数
	//alert(phone.resultnum);
	if(phone.resultnum == 0){
		alert(phone.code);
		setCookie('number',phone.code);
		setCookie('users',$('.dl_numberT input').val());
		dOnoff = false;
		sendCode($('.dl_right'));
		
		
		$('.dl_btn').tap(function(){//登录按钮点击事件
			if($('.dl_fangshi div').eq(1).attr('class') =='nowPage'){
				if(!blank()){
					return false;
				}
				else if(!isPhone()){
					$('.dl_numberT').css('border-color','red');
					$('.dl_tips3').html('手机号码输入有误！');
  					$('.dl_tips3').css('display','block');
				    return false;
				}
				else if(!checkyanz()){
					$('.dl_left').css('border-color','red');
					$('.dl_tips4').html('验证码输入有误！');
  					$('.dl_tips4').css('display','block');
				    return false;
				} 
				else{
					//alert(getCookie('users'));
					$('.dl_numberT').css('border-color','#d6d7dc');
					$('.dl_left').css('border-color','#d6d7dc');
					$('.dl_tips3').css('display','none');
					$('.dl_tips4').css('display','none');
					fengAjax("post"," http://www.buruwo.com/index.php?g=Portal&m=Login&a=Login","phone="+getCookie('users')+'&type=1&code='+getCookie('number'),passOk);
				}
			}
		});
	}
	else{
		alert(pdata.result_mess);
	}
}

function sendCode(thisBtn){//倒计时
	btn = thisBtn;
	btn.disabled = true; //将按钮置为不可点击
	btn.css('background-color','#F1F1F1');
	btn.css('color','#898989');
	btn.css('border-color','#898989');
	btn.val(nums+'秒后重新获取');
	clock = setInterval(doLoop, 1000); //一秒执行一次
}

function doLoop(){//倒计时定时器
	nums--;
	if(nums > 0){
		btn.val(nums+'秒后重新获取');
	}
	else{
	  	clearInterval(clock); //清除js定时器
	  	btn.disabled = false;
	  	btn.val('重新发送验证码');
	  	btn.css('color','#f97fa4');
	  	btn.css('border-color','#f97fa4');
	  	nums = 60; //重置时间
	  	dOnoff = true;
	}
}

function blank(){//验证是否为空
	
	if($('.dl_fangshi div').eq(0).attr('class') =='nowPage'){
		userNumb = $(".dl_number input").val();
		userPass = $(".dl_mima input").val();
		if (userNumb == "" || userNumb == null){
			$('.dl_number').css('border-color','red');
			$('.dl_tips1').html('手机号码不能为空！');
  			$('.dl_tips1').css('display','block');
	    	//alert("号码不能为空");
	    	return false;
	  }
	  	else if(userPass == "" || userPass == null){
	  		$('.dl_mima').css('border-color','red');
	  		$('.dl_tips2').html('密码不能为空！');
  			$('.dl_tips2').css('display','block');
	    	//alert("密码不能为空");
	    	return false;
	  	}
	  	else{
	    	return true;
	  	}
	}
	else{
		userNumb = $(".dl_numberT input").val();
		userYanz = $(".dl_left input").val();
		if (userNumb == "" || userNumb == null){
			$('.dl_numberT').css('border-color','red');
			$('.dl_tips3').html('电话号码不能为空！');
  			$('.dl_tips3').css('display','block');
	    	//alert("号码不能为空");
	    	return false;
	  	}
	  	else if(userYanz == "" || userYanz == null){
	  		$('.dl_left').css('border-color','red');
	  		$('.dl_tips4').html('验证码不能为空！');
  			$('.dl_tips4').css('display','block');
	  		//alert("验证码不能为空");
	    	return false;
	  	}
	  	else{
	    	return true;
	  	}
	}	
}

function isPhone(){//验证手机号码
	userNumb = $(".dl_number input").val();
	userNumbT = $(".dl_numberT input").val();
    var reg = /^[1][3578][0-9]{9}$/;
    if($('.dl_fangshi div').eq(0).attr('class') =='nowPage'){
    	return reg.test(userNumb);
    }
    else{
    	return reg.test(userNumbT);
    }
}

function checkyanz(){//验证验证码
	userYanz = $(".dl_left input").val();
	if(userYanz == getCookie('number')){
		var reg = /^[0-9]{4}$/;
    	return reg.test(userYanz);
	}
    else{
    	$('.dl_left').css('border-color','red');
    	$('.dl_tips4').html('验证码输入有误！');
  		$('.dl_tips4').css('display','block');
    	//alert('验证码输入错误！');
    	return false;
    }
}

function checkpass(){//验证密码
	userPass = $(".dl_mima input").val();
	if(userPass.length<6 || userPass.length >20){
		$('.dl_mima').css('border-color','red');
		$('.dl_tips2').html('密码长度须在6-20之间!');
  		$('.dl_tips2').css('display','block');
	    //alert("密码长度须在6-20之间!");
	    return false;
	}
	else{
		return true;
	}
}



