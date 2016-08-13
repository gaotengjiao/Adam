<?php
namespace Common\Model;
use Common\Model\CommonModel;
class MemberModel extends CommonModel {
	/*
		查询商品表
	 */
	public function seldata($post){
		$dat = "shopping";
		$id['iphone'] = $post['search'];
		$table = M('member');
		$res = $table->where($id)->field('uid')->find();
		
		$tables = M($dat);
		$red = $tables->where($res)->select();

		return $red;
	}
	/*
		查询订单表
	 */
	public function selector($post){
		$id['iphone'] = $post['search'];
		$table = M('member');
		$res = $table->where($id)->field('uid')->find();
		
		$tables = M('teacher');
		$red[1] = $tables->where($res)->select();

		$tabled = M('items');
		$red[2] = $tabled->where($res)->select();
		return $red;
	}

	/*
		修改商品也信息
	 */
	public function delOrder($id){

		$table = M('shopping');
		$state['state'] = 2;
		$res = $table->where("id = $id")->delete();
		return $res;
	}

	/*
		多条删除
	 */
	public function deletes($id){
		$len = count($id);
		for($i=0; $i<$len; $i++){
			$res = M('shopping')->where("id = $id[$i]")->delete();
		}
		return $res;
	}

	/*
		全部删除
	 */
	public function dele($id){
		$res = M('shopping')->where("uid = $id")->delete();
		return $res;
	}
	/*
	 * 医嘱提醒  状态更改
	 */
	public function doctor_status($uid, $doctor_status){
		$member = M('member');
		$where['uid'] = $uid;
		$data['doctor_status'] = $doctor_status;
		$return = $member->where($where)->save($data);
		if($return){
			return SONEOK($return);
			exit;
		}else{
			return SONEERROR();
			exit;
		}
	}
	/*
	 * 丽颜汇提醒
	 */
	public function smly_status($uid, $status){
		$member= M('member');
		$where['uid'] = $uid;
		$data['smly_status'] = $status;
		$return = $member->where($where)->save($data);
		if($return){
			return SONEOK($return);
			exit;
		}else{
			return SONEERROR();
			exit;
		}
	}
	/*
	 * 金钱变动提醒
	 */
	public function money_status($uid, $status){
		$member= M('member');
		$where['uid'] = $uid;
		$data['money_change'] = $status;
		$return = $member->where($where)->save($data);
		if($return){
			return SONEOK($return);
			exit;
		}else{
			return SONEERROR();
			exit;
		}
	}
}