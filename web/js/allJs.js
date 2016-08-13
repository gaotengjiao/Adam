function jiance(cook){
	switch(cook){
		case '0'://点击项目中的预约单/预约  进入登录页面的cookie
			delCookie('gogo');
			window.location.href = 'xiangmu.html';
		break;
		case '1'://点击个人 进入登录页面的cookie
			delCookie('comeon');
			window.location.href = 'geren.html';
		break;
		case '2'://点击详情页中预约/预约单  进入登录页面的cookie
			delCookie('lalala');
			window.location.href = 'xiangqingye.html';
		break;
		case '3'://点击名师预约中预约/预约单  进入登录页面的cookie
			delCookie('mingJ');
			window.location.href = 'mingshiyuyue.html';
		break;
		case '4'://点击立即预约中预约按钮  进入登录页面的cookie
			delCookie('yiYue');
			window.location.href = 'lijiyuyue.html';
		break;
		case '5'://点击预约中历史预约  进入登录页面的cookie
			delCookie('camea');
			window.location.href = 'lishiyuyue.html';
		break;
        case '6'://点击颜值图标  进入登录页面的cookie
            delCookie('dianzan');
            window.location.href = 'dashang.html';
            break;
		case '7'://点击颜值圈文章中 赞  进入登录页面的cookie
			delCookie('dianzan');
			window.location.href = 'dashang.html';
		break;
	}
}
	

