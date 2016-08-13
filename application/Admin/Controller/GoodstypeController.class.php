<?php

/*
 * 产品分类管理
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class GoodstypeController extends AdminbaseController {
	/*
		产品分类页面
	*/
	public function index()
	{
		$table = D('GoodsType');

		$aa = $table->MoveType();
		$this->assign('posts', $aa);

		$this->display('index');
	}
 
	/* 
		查询添加页面的分类
	*/
	public function addtype(){
		$table = D('GoodsType');

		$made = $table->MoveType();

		$this->assign('posts', $made);

		$this->display('add');
	}

	/* 
		禁用分类
	*/
	public function edits(){
		$data = I('get.id');
		$table = D('GoodsType');

		$aa = $table->edits($data);

		// dump($aa);
	}

	// ajax传值修改启用关闭
	public function state(){
		$post['id'] = I('get.id');
		$post['state'] = I('get.state');
		
		$table = D('GoodsType');

		$res = $table->editState($post);

		if($res){
			echo '1';
		}else{
			echo '2';
		}
	}

	// 验证type名字是否存在
	public function editname(){
		$post = I('post.');

		$table = D('GoodsType');

		$res = $table->editname($post);

		if($res == 0){
			echo '1';
		}else{
			echo '2';
		}
	}

	// 执行添加操作分类
	public function addpost(){
		$post = I('post.value');
		foreach($post as $k =>$v){
			$a[] = $v['name']; $b[] = $v['value'];
			$arr = array_combine($a, $b);
		}
		
		$table = D('GoodsType');

		$res = $table->addpost($arr);

		if($res){
			echo '1';
		}else{
			echo '2';
		}
	}

	// 跳转到修改分类页
	public function edittype(){
		$table = D('GoodsType');

		$made = $table->MoveType();

		$this->assign('posts', $made);

		$posts = I('get.');

		$this->assign('data', $posts);

		$this->display("edit");
	}

	// 修改分类
	public function typeedit(){
		$post = I('post.');

		$table = D('GoodsType');

		$res = $table->edit_type($post);

		if($res){
			$this->success('成功', U('Goodstype/index'));
		}else{
			$this->error('失败');
		}
	}

	/*
		修改分类名字
	 */
	public function edittypename(){
		$post['id'] 	  = I('post.id');
		$post['typename'] = I('post.v');
		$table = D('GoodsType');
		$res = $table->editnametype($post);
		if($res){
			echo '1';
		}else{
			echo '2';
		}
	}

	/*
		全选删除
	 */
	public function deldates(){
		$id = I('get.id');
		$table = D('GoodsType');
		$result = $table->DelDates($id);
	}


	/*
		批量移动页面
	 */
	public function move(){
		$id = I('get.ids');
		$table = D('GoodsType');
		$data = $table->MoveType();
		$this->assign('id', $id);
		$this->assign('posts', $data);
		$this->display();
	}

	/*
		批量移动操作
	 */
	public function typemove(){
		$get = I('get.');
		$table = D('GoodsType');
		$result = $table->typemove($get);
		print_r($result); 
	}
}