<?php

/**
 * 分期管理
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class InstallmentController extends AdminbaseController {
	/*
		分期页面
	 */
	public function index(){
		$post = I('post.'); 
		$table = D('Installment');
		$res = $table->Sel($post);
		$this->assign('list', $res[1]);
		$this->assign('show', $res[2]);
		$this->assign('num', $res[3]);
		$this->assign('time1', $res[4]);
		$this->assign('time2', $res[5]);
		$this->display();
	}
 
	/*
		新增期数
	 */
	public function add(){
		$this->display();
	}

	/*
		添加操作
	 */
	public function add_post(){
		$post = I('post.');
		$table = D('Installment');
		$res = $table->add_post($post);
		if($res){
			$this->success();
		}
	}

	/*
		查询利率
	 */
	public function selment(){
		$get = I('post.text');
		$table = D('Installment');
		$res = $table->selment($get);
		$data = $res[0]['period'];
		if($data == 1){
			$per = '月';
		}
		if($data == 2){
			$per = '日';
		}
		if($data == 3){
			$per = '年';
		}

		$str = " ";
		$str .= "<table border='1' cellspacing='0' width='200'>";
		$str .= "<tr>";
		$str .= "<th>期数</th><th>利率</th>";
		$str .= "</tr>";
		foreach ($res as $k => $v) {
			$num = $k+1;
			$str .= "<tr>";
			$str .= "<td>".$num."</td>";
			$str .= "<td>".$v['rate']."/".$per."</td>";
			$str .= "</tr>";
		}
		$str .= "</table>";
		print_r($str);die;
	}

	/*
		修改期数利率
	 */
	public function edit(){
		$name = I('get.type');
		$table = D('Installment');
		$res = $table->selfind($name);
		foreach ($res as $k => $v) {
			$date['periodstype'] = $v['periodstype'];
			$date['period'] = $v['period'];
			$date['num'] 	= $k+1;
			$test[$k]['qi'] = $v['periods'];
			$test[$k]['id'] = $v['id'];
			$test[$k]['lv'] = $v['rate'];
		}

		$this->assign('date', $date);
		$this->assign('first', $test);
		$this->display("edit");
	}
	/*
		修改禁用与启用
	 */
	public function editstatus(){
		$get = I('get.');
		$table = D('Installment');
		$res = $table->editstatus($get);
		if($res){
			echo '1';
		}else{
			echo '2';
		}
	}
 
	/*
		修改利率与信息
	 */
	public function edit_post(){
		$post = I('post.');
		$table = D('Installment');
		$res = $table->editrate($post);
		if($res){
			$this->success();
		}
	}

	/*
		分期项目
	 */
	public function cyclebilling(){
		$table = D('Installment');
		$res = $table->Seltype();
		$this->assign('list', $res[1]);
		$this->assign('table', $res[2]);
		$this->display("cyclebilling");
	}

	/*
		查询子类
	 */
	public function selgoodstype(){
		$get =  I('get.id');
		$table = D('Installment');
		$res = $table->selgoods($get);
		$str = '';
		$dag = 'hsd'.$get;
		foreach ($res as $k => $v) {
			if($v['types'] == ''){
				$v['types'] = '暂无分期';
			}


			$str .= "<tr class=".$dag.">";
			$str .= "<td>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp <a style='cursor:pointer' class='bagade' data=".$v['id'].">◆</a> &nbsp&nbsp".$v['id']."</td>";
			$str .= "<td>".$v['gname']."</td>";
			if($v['types'] == 1){
				$str .= "<td><select name='types' data=".$v['id']." class='selectead'><option value='1' selected>分期</option><option value='0'>不分期</option></select></td>";
			}elseif($v['types'] == 0){
				$str .= "<td><select name='types' data=".$v['id']." class='selectead'><option value='1'>分期</option><option value='0' selected>不分期</option></select></td>";
			}
			
			$str .= "<td>123</td>";
			$str .= "<td type='2' data=".$v['id'].">修改</td>";
			$str .= "</tr>";
			// $v['types']
		}
		print_r($str);
	}

	/*
		查询分期商品信息
	 */
	public function editstall(){
		$table = D('Installment');
		$res = $table->editstall();
		$str = "";
		$str .= "<select name='' class='selectedsar' style='margin:0px;padding:0px;height:22px'>";
		$str .= "<option value=' '>点击选择</option>";
		foreach ($res as $k => $v) {
			$str .= "<option value=".$v['periodstype'].">".$v['periodstype']."</option>";
		}
		$str .= "</select>";

		print_r($str);
	}

	/*
		修改分期信息
	 */
	public function setting(){
		$post = I('post.');
		$table = D('Installment');
		$res = $table->setting($post);
	}

	/*
		查询名称是否存在
	 */
	public function ajaxName(){
		$post = I('post.name');
		$table = D('Installment');
		$res = $table->ajaxName($post);
		if ($res) {
			echo '1';
		}else{
			echo '2';
		} 
	}

	/*
		修改是否分期
	 */
	public function isinstall(){
		$post = I('post.');
		$table = D('Installment');
		$res = $table->isinstall($post);
		if($res){
			echo '1';
		}
	}

	/*
		用户组分期
	 */
	public function userInstall(){
		$table = D('Installment');
		$res = $table->userInstall();
		$this->assign('list',$res[1]);
		$this->assign('type',$res[2]);
		$this->assign('show',$res[3]);
		$this->display();
	}

	/*
		修改是否分期
	 */
	public function editisinstall(){
		if(IS_POST){
			$post = I('post.');
			$table = D('Installment');
			$res = $table->editisinstall($post);
			print_r($res);
		};
	}

	/*
		修改是否用户组是否正在使用
	 */
	public function editisusess(){
		if(IS_POST){
			$post = I('post.');
			$table = D('Installment');
			$res = $table->editisusess($post);
			print_r($res);die;
		};
	}
}