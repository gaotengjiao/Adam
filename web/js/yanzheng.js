
var clock = '';
var nums = 60;
var btn = '';
var zOnoff = true;
var cOnoff = true;
var sendNum = null;
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

function fengAjax(type,url,data,callback){
	$.ajax({
		type:type,
		url:url,
		data:data,
		success:callback
	})
};

function isPhone(numberI){//验证手机号码
	userNumb = numberI.val();
    var reg = /^[1][3578][0-9]{9}$/;
    return reg.test(userNumb);
}

function checkyanz(leftI){//验证验证码
	userYanz = leftI.val();
	if(getCookie('number') == userYanz){
		var reg = /^[0-9]{4}$/;
    	return reg.test(userYanz);
	}
    else{
    	//alert('验证码输入错误！');
    	return false;
    }
}

function checkpass(mimaI){//验证密码
	userPass = mimaI.val();
	if(userPass.length<6 || userPass.length >20){	
	    //alert("密码长度须在6-20之间!");
	    return false;
	}
	else{
		return true;
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
	  	cOnoff = true;
	}
}