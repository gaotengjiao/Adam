<?php
namespace Common\Model;
use Common\Model\CommonModel;
class PayOrderModel extends CommonModel {
	public function Sel($search){
		$search 	= $search['search'];
		$where = "m.uid = o.uid";
		if($search){
			$where = "m.uid = o.uid and o.num = $search OR m.iphone = $search";
			$res[3] = $search;
		}

		$table = M('pay_order');
		$len = $table->table('__MEMBER__ m, __PAY_ORDER__ o')->field('o.*, m.iphone')->where($where)->count();
		$page = new \Think\Page($len, 10);
		$res[2] = $page->show();
		$res[1] = $table->table('__MEMBER__ m, __PAY_ORDER__ o')->field('o.*, m.iphone')->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		return $res;
	}

	/*
		待付款订单
	 */
	public function Selobligation($search){
		$search 	= $search['search'];
		$where = "m.uid = o.uid and o.pay_status = 0";
		if($search){
			$where = "m.uid = o.uid and o.num = $search OR m.iphone = $search and o.pay_status = 0";
			$res[3] = $search;
		}
		$table = M('pay_order');
		$len = $table->table('__MEMBER__ m, __PAY_ORDER__ o')->field('o.*, m.iphone')->where($where)->count();
		$page = new \Think\Page($len, 10);
		$res[2] = $page->show();
		$res[1] = $table->table('__MEMBER__ m, __PAY_ORDER__ o')->field('o.*, m.iphone')->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		return $res;
	}

	/*
		已付款订单
	 */
	public function Selspend($search){
		$search 	= $search['search'];
		$where = "m.uid = o.uid and o.pay_status = 1";
		if($search){
			$where = "m.uid = o.uid and o.num = $search OR m.iphone = $search and o.pay_status = 1";
			$res[3] = $search;
		}
		$table = M('pay_order');
		$len = $table->table('__MEMBER__ m, __PAY_ORDER__ o')->field('o.*, m.iphone')->where($where)->count();
		$page = new \Think\Page($len, 10);
		$res[2] = $page->show();
		$res[1] = $table->table('__MEMBER__ m, __PAY_ORDER__ o')->field('o.*, m.iphone')->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		return $res;
	}

	/*
		退款订单表
	 */
	public function Selrefund($search){
		$search 	= $search['search'];
		$where = "m.uid = o.uid and o.order_status in(3,4)";
		if($search){
			$where = "m.uid = o.uid and o.order_status in(3,4) and o.num = $search OR m.iphone = $search";
			$res[3] = $search;
		}

		$table = M('pay_order');
		$len = $table->table('__MEMBER__ m, __PAY_ORDER__ o')->field('o.*, m.iphone')->where($where)->count();
		$page = new \Think\Page($len, 10);
		$res[2] = $page->show();
		$res[1] = $table->table('__MEMBER__ m, __PAY_ORDER__ o')->field('o.*, m.iphone')->where($where)->limit($page->firstRow.','.$page->listRows)->select();
		return $res;
	}

	/*
		修改状态
	 */
	public function order_status($get){
		$table = M('pay_order');
		$id = $get['id'];
		$status['order_status'] = $get['v'];
		$res = $table->where("oid = $id")->save($status);
		return $res;
	}
}