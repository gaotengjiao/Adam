<?php
/*
 *服务流程管理
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class ServiceController extends AdminbaseController {
    /*
    	服务流程
     */
    public function Service(){
    	$table = D('Service');
    	$result = $table->SelService();
    	$this->assign('list', $result[1]);
    	$this->assign('show', $result[2]);
        $this->display("index");
    }

    /*
    	添加服务流程名称
     */
    public function add(){
    	$this->display("add");
    }

    /*
    	添加操作
     */
    public function add_post(){
    	$content['sname'] = htmlspecialchars_decode(I('post.sname'));
    	$content['scontent'] = I('post.scontent');
    	$content['status'] = I('post.status');
    	$result = M('service')->add($content);
    	if($result){
    		$this->success();
    	}else{
    		$this->error();
    	}
    }
    /*
    	修改服务流程
     */
    public function edit(){
    	$id = I('get.id');
    	$table = D('Service');
    	$result = $table->edit($id);
    	$this->assign('find', $result);
    	$this->display("edit");
    }

    /*
		执行修改操作
	 */
	public function edit_post(){
		$data = I('post.');
		$table = D('service');
		$res = $table->edit_post($data);
		if($res){
			$this->success();
		}
	}

	/*
		执行启用或删除
	 */
	public function down(){
		$data = I('get.');
		$table = D('service');
		$res = $table->editDown($data);
		if($res){
			echo '1';
		}else{
			echo '2';
		}
	}

	/*
		全选 删除
	 */
	public function delete(){
		$id = I('get.id');
		$table = D('service');
		$result = $table->delete($id);
		if($result){
			echo '1';
		}else{
			echo '2';
		}
	}
}  