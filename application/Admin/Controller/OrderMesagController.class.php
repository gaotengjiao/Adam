<?php
/*
	账单控制器
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class OrderMesagController extends AdminbaseController {
	public function OrderManagement(){
		$search = I('get.');
		$table = D('PayOrder');
		$result = $table->Sel($search);
		$this->assign('data', $result[1]);
		$this->assign('show', $result[2]);
		$this->assign('num', $result[3]);
		$this->display("index");
	}

	/*
		代付款订单页
	 */
	public function obligation(){
		$search = I('get.');
		$table = D('PayOrder');
		$result = $table->Selobligation($search);
		$this->assign('data', $result[1]);
		$this->assign('show', $result[2]);
		$this->assign('num', $result[3]);
		$this->display("obligation");
	}

	/*
		已付款订单页
	 */
	public function spend(){
		$search = I('get.');
		$table = D('PayOrder');
		$result = $table->Selspend($search);
		$this->assign('data', $result[1]);
		$this->assign('show', $result[2]);
		$this->assign('num', $result[3]);
		$this->display("spend");
	}

	/*
		退款订单表
	 */
	public function refund(){
		$search = I('get.');
		$table = D('PayOrder');
		$result = $table->Selrefund($search);
		$this->assign('data', $result[1]);
		$this->assign('show', $result[2]);
		$this->assign('num', $result[3]);
		$this->display("refund");
	}

	/*
		修改状态
	 */
	public function order_status(){
		$get = I('get.');
		$table = D('PayOrder');
		$result = $table->order_status($get);
		if($result){
			echo '1';
		}else{
			echo '2';
		}
	}
}