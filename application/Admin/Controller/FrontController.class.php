<?php

/*
 * 后台  前台会员管理
 显示前台注册用户相关信息
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class FrontController extends AdminbaseController {
	/*
		显示前台注册用户相关信息
	*/
		public function Index()
		{
			$user = D("User");//实例化Model
			$userinfo = $user->userinfo();//调用Model中的方法
    		$this->assign('lists',$userinfo['1']);// 赋值数据集
    		$this->assign('page',$userinfo['2']);// 赋值分页输出
    		$this->display("index"); // 输出模板
		}

		/*
			测试接口
		*/
		public function Getdate(){
			$date = I("post.");
			print_r($date);die;
			$user = M("user");
			$userinfo = $user->select();
			print_r(json_encode($userinfo));die;
		}
	}