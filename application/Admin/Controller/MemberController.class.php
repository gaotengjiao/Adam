<?php

namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class MemberController extends AdminbaseController {
	public function index(){
		$this->display();
	}
	/*
		根据手机号查询购物单和订单
	 */
	public function seliphone(){ 
		$post = I('post.');

		$res['type']   = $post['type'];
		$res['iphone'] = $post['search'];
		$table = D('Member');

		if($post['type'] == 'shopping'){
			$post['type'] = "shopping"; 
			$data = $table->seldata($post);
		}else{
			$post['type'] = "order";
			$date = $table->selector($post);
		}

		$this->assign('data', $data);	// 商品页
		$this->assign('date', $date[1]); 	// 老师页
		$this->assign('test', $date[2]); 	// 项目页
		$this->assign('res', $res);		// 查询条件
		$this->display("index");
	}
	/*
		删除购物记录表单（改状态）
	 */
	public function editOrder(){
		$id = I('get.value');

		$table = D('Member');

		$res = $table->delOrder($id);

		if($res){
			echo '1';
		}else{
			echo '2';
		}
	}

	// 多条删除
	public function deletes(){
		$id = I('get.check');
		
		$table = D('Member');

		$res = $table->deletes($id);

		if($res){
			echo '1';
		}else{
			echo '2';
		}
	}

	/*
		全部删除
	 */
	public function dele(){
		$id = I('get.uid');
		$table = D('Member');
		$res = $table->dele($id);
		if($res){
			echo '1';
		}else{
			echo '2';
		}
	}
}