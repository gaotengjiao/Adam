<?php
/*
	医生管理
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class UsersController extends AdminbaseController{
	/*
		主页数据
	 */
	public function index(){
		$table = D('Users');
		$data = $table->selects();
		
		$this->assign('post', $data[1]);

		$this->assign('show', $data[2]);
		$this->display();
	}

	// 跳转 
	public function move(){
        $tid = I('get.id'); 
        $table = D('Users');
        // 修改日期。增加七天
        $table->edittime($tid);

        // 时间表
        $red = $table->selectdate($tid);
        $res = array_map('current', $red);
 
        $ref = $table->selecttime();	// 时间段	
         

        $this->assign('list', $res); // 时间表
        $this->assign('arr', $ref); 
        $this->assign('tid', $tid); // 老师id
		$this->display("move");
	}

	// 获取时间点
	public function falses(){
		$data = I("post.");
		$UsersModel = D("Users");
		$result = $UsersModel->SelFalseTime($data);
		echo $result;exit;
	}

	// 修改老师的休息表
	public function editFalse(){
		$post['id'] 	= I('post.id');
		$demo 			= I('post.false');
		$demos 			= implode(',', $demo);
		$post['false'] 	= $demos;
		$table = D('Users');
		$res = $table->editfalse($post);
		if($res){
			echo '1';
		}else{
			echo '2';
		}
	}

	/*
		全选删除
	 */
	public function editChecked(){
		$id = I('post.id');
		$table = D('Users');
		$res = $table->editChecked($id);
		if($res){
			echo '1';
		}else{
			echo '2';
		}
	}

	/*
		修改医师的状态
	 */
	public function editstatus(){
		$status['i'] = I('post.i');
		$status['e'] = I('post.e');
		$requirt = D('Users')->editstatus($status);
		if($requirt){
			echo '1';
		}else{
			echo '2';
		}
	}

	
}