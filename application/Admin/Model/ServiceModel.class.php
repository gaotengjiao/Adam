<?php

/*
	服务流程
*/
namespace Common\Model;
use Common\Model\CommonModel;
class ServiceModel extends CommonModel {
	public function SelService(){
		$ServiceTable = M("service");
		$len = count($ServiceTable->where("status = 1 || status = 0")->select());
		$Page = new \Think\Page($len, 10);
		$show = $Page->show();

		$result[1] = $ServiceTable->where("status = 1 || status = 0")->limit($Page->firstRow.','.$Page->listRows)->select();
		$result[2] = $show;
		return $result;
	}

	/*
		查询要修改的数据
	 */
	public function edit($id){
		$table = M('service');
		$result = $table->where("sid = $id")->find();
		return $result;
	}

	/*
		执行修改操作
	 */
	public function edit_post($data){
		$id = $data['id'];
		$content['sname'] = htmlspecialchars_decode($data['sname']);
		$content['scontent'] = $data['scontent'];
		$content['status'] = $data['status'];
		$result = M("service")->where("sid = $id")->save($content);
		return $result;
	}

	/*
		执行修改启用删除
	 */
	public function editDown($data){
		$id = $data['id'];
		$status = $data['status'];
		if($status == 1){
			$sta['status'] = 0;
		}
		if($status == 0){
			$sta['status'] = 1;
		}
		$res = M('service')->where("sid = $id")->save($sta);
		return $res;
	}

	/*
		执行全选删除
	 */
	public function delete($id){
		$len = count($id);
		$status['status'] = 99;
		for($i = 0; $i < $len; $i++){
			$result = M('service')->where("sid = $id[$i]")->save($status);
		}
		return $result;
	}
}