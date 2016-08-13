<?php
namespace Common\Model;
use Common\Model\CommonModel;
class UserModel extends CommonModel {

	/*
		查询本站用户的相关信息

	*/
	public function Userinfo()
	{ 
		$User = M('User'); // 实例化User对象
    		$count      = $User->count();// 查询满足要求的总记录数
    		$Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    		$show       = $Page->show();// 分页显示输出
    		// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
    		$list = $User->join("s_member on s_user.uid=s_member.uid")->order('starttime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
    		$userarr['1'] = $list;
    		$userarr['2'] = $show;
		return $userarr;
	}
}