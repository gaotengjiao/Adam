<?php
namespace Common\Model;
use Common\Model\CommonModel;
class ActivityModel extends CommonModel {
	/*
		查询预约活动的用户预约信息
		$id    活动ID
	*/
	public function Selinfo($id){
		$Activity = M("Activity");
		$res = $Activity->where("aid = $id")->select();
		$len = count($res);
		for($i=0; $i<$len; $i++){
			$uid = $res[$i]['uid'];
			$res[$i]['name'] 	= implode(',', M('member')->where("uid = $uid")->field('name')->find());
			$res[$i]['iphone'] 	= implode(',', M('member')->where("uid = $uid")->field('iphone')->find());
			$res[$i]['price'] 	= implode(',', M('product')->where("id = $id")->field('price')->find());
			$res[$i]['p_name'] 	= implode(',', M('product')->where("id = $id")->field('p_name')->find());
		}
		return $res;
	}
}