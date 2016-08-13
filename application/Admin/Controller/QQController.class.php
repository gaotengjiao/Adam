<?php

namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class QQController extends AdminbaseController {
	// qq 跳转页
	public function QQ_login(){
		$appid = '101330520';
		$appkey = '8bb109f6cd5bec2a73bafafce898ec53';
		$callback = "http://www.buruwo.com/qqlogin.php";
		$scope = "get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo";
		$state = md5(uniqid(rand(), TRUE));
		$login_url = "https://graph.qq.com/oauth2.0/authorize?response_type=code&client_id=" 
        . $appid . "&redirect_uri=" . urlencode($callback)
        . "&state=" . $state
        . "&scope=".$scope;
    	header("Location:$login_url");
	}
}



