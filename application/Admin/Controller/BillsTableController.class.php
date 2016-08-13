<?php

/**
 * 用户账单表
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class BillsTableController extends AdminbaseController 
{
	/*
		所有账单
	 */
	public function index()
	{	
		$search = I('post.');
		$table = D('UserBill');
		$res = $table->Sel($search);
		$this->assign('list', $res[1]);
		$this->assign('show', $res[2]);
		$this->assign('num', $res[3]);
		$this->assign('time1', $res[4]);
		$this->assign('time2', $res[5]);
		$this->display("index");
	} 

	/*
		分期账单
	 */
	public function cyclebilling(){
		$search = I('post.');
		$table = D('UserBill');
		$res = $table->cyclebilling($search);
		$this->assign('list', $res[1]);
		$this->assign('show', $res[2]);
		$this->assign('num', $res[3]);
		$this->assign('time1', $res[4]);
		$this->assign('time2', $res[5]);
		$this->display();
	}

	/*
		逾期账单
	 */
	public function overduebill(){
		$search = I('post.');
		$table = D('UserBill');
		$res = $table->overduebill($search);
		$this->assign('list', $res[1]);
		$this->assign('show', $res[2]);
		$this->assign('num', $res[3]);
		$this->assign('time1', $res[4]);
		$this->assign('time2', $res[5]);
		$this->display('overduebill');
	}

	/*
		设置账单
	 */
	public function setbill(){
		$id = I('get.id');
		$table = D('UserBill');
		$res = $table->Selfind($id); 
		$this->assign('list', $res);
		$this->display("edit");
	}
	
	/*
		导出到Excel表格
	 */
	public function excel(){
		header("Content-Type: application/vnd.ms-excel;utf-8");
		header('Content-Disposition: attachment; filename=demo.xls');
		header('Pragma: no-cache');
		header('Expires: 0');
		$table = D('UserBill');
		$res = $table->Sel();

		$title = array('账单编号', '手机号', '账单内容', '是否分期', '交易时间', '还款时间','账单类型','账单状态','还款状态','账单金额');
		$data = array();
			foreach ($res[1] as $value) {
				$data[] = array($value['id'], $value['iphone'], $value['billcontent'], $value['isstallments'], $value['billDate'], $value['RepaymentDate'],$value['billtype'],$value['status'] , $value['billStatus'], $value['billAmount']);
			}
			echo iconv('utf-8', 'gbk', implode("\t", $title)), "\n";
		foreach ($data as $value) {
			echo iconv('utf-8', 'gbk', implode("\t", $value)), "\n";
		}die;
	}
}