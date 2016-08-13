<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController;
class MemberController extends HomebaseController {
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
	/*
	 * 推送设置
	 */
	public function updSetup(){
		$uid = I('request.uid', '');
		$doctor_status = I('request.doctor_status', '');
		$smly_status = I('request.smly_status', '');
		$money_status = I('request.money_change', '');
		if(empty($uid)){
			$error = SONEERROR();
			$this->ajaxReturn($error);
			exit;
		}
		$table = D('Member');
		if(!empty($doctor_status)){
			$result = $table -> doctor_status($uid,$doctor_status);
		}
		if(!empty($smly_status)){
			$result = $table -> smly_status($uid,$smly_status);
		}
		if(!empty($money_status)){
			$result = $table -> money_status($uid,$money_status);
		}
		$this->ajaxReturn($result);
	}
}