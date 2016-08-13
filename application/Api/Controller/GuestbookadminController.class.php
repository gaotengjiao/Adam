<?php
namespace Api\Controller;
use Common\Controller\AdminbaseController;
class GuestbookadminController extends AdminbaseController{
	
	protected $guestbook_model;
	
	function _initialize() {
		parent::_initialize();
		$this->guestbook_model=D("Common/Guestbook");
	}
	
	public function index(){
		$where = "status = 1";
		if(IS_POST){
			$post = I('post.');
			$red = $this->search($post);
			//print_R($red);die;
			$where = $red[1];
			if(!empty($where)){
				$where .= " and status = 1";
			}else{
				$where = "status = 1";
			}
		}
		$count=$this->guestbook_model->where($where)->count();
		$page = $this->page($count, 20);
		$guestmsgs = $this->guestbook_model->where($where)->order(array("createtime"=>"DESC"))->limit($page->firstRow. ',' .$page->listRows)->select();
		for($i=0; $i<$count; $i++){
			$id = $guestmsgs[$i]['id'];
			$uid = $guestmsgs[$i]['uid'];
			$recommend = $guestmsgs[$i]['recommend'];
			if($id){
				$a = M('guestbook_reply')->where("guestbookid = $id and status = 1 and isadmin = 1")->count();
				if($a >= 1){
					$guestmsgs[$i]['zhstatus'] = "<a href='#' class='replied' data=".$id.">已回复</a>";
				}else{
					$guestmsgs[$i]['zhstatus'] = "<a href='#' class='replied' data=".$id.">未回复</a>";
				}

				$guestmsgs[$i]['count'] = M('comments')->where("post_table = 'guestbook' and post_id = $id")->count();
				$audit_status = $guestmsgs[$i]['audit_status'];
				if($audit_status == 0){
					$guestmsgs[$i]['audit_status'] = "未审核";
				}elseif($audit_status == 1){
					$guestmsgs[$i]['audit_status'] = "已审核";
				}
			}
			if($uid){
				$member = M('member')->where("uid = $uid")->field('iphone, nickname')->find();
				$guestmsgs[$i]['nickname'] = $member['nickname'];
				$guestmsgs[$i]['iphone'] = $member['iphone'];
			}
			$guestmsgs[$i]['recommend'] = $recommend == 0 ? '不推荐' : '推荐';
		}
		$guest_reply = M('guestbook_reply');
		foreach($guestmsgs as $k=>$v){
			$guestmsgs[$k]['comment_count'] = $guest_reply->where("guestbookid = ".$v['id']." AND isadmin = 0 AND status = 1")->count();
		}
		$this->assign('iphone', $red[2]);
		$this->assign('name', $red[3]);
		$this->assign('time1', $red[4]);
		$this->assign('time2', $red[5]);
		$this->assign("Page", $page->show('Admin'));
		$this->assign("guestmsgs",$guestmsgs);
		$this->display();
	}

	function delete(){
		$id=intval(I("get.id"));
		$result=$this->guestbook_model->where(array("id"=>$id))->delete();
		if($result!==false){
			$this->success("删除成功！", U("Guestbookadmin/index"));
		}else{
			$this->error('删除失败！');
		}
	}
	/*
		查看回复内容
	 */
	public function Sell(){
		$id = I('post.id');
		if(!empty($id)){
			$res = M('guestbook_reply')->where("guestbookid = $id and status = 1 and isadmin = 1")->order('replytime ASC')->select();
			if($res){
				$str = "";
				$str .= "<table border='1'>";
				$str .= "<tr>";
				$str .= "<th>昵称</th>";
				$str .= "<th>回复时间</th>";
				$str .= "<th>回复内容</th>";
				$str .= "</tr>";
				foreach ($res as $k => $v) {
					$admin = $v['uid'];
					$user_id = implode(',', M('users')->where("id = ".$admin)->field('user_login')->find());
					$str .= "<tr>";
					$str .= "<td> ".$user_id." </td>";
					$str .= "<td> ".$v['replytime']." </td>";
					$str .= "<td> ".$v['replycontent']." </td>";
					$str .= "</tr>";
				}
				$str .= "</table>";
				print_r($str);
			}
		}
	}

	/*
		回复
	 */
	public function Edits(){
		$post = I('post.');
		if($post){
			$data['guestbookid'] = I('post.id');
			$data['replycontent'] = I('post.value');
			$data['replytime'] = date('Y-m-d H:i:s',time());
			$data['isadmin'] = 1;
			$data['uid'] = session('ADMIN_ID');
	 		$res = M('guestbook_reply')->add($data);
	 		if($res){
	 			echo '1';
	 		}
		}
	}

	/*
		审核
	 */
	public function check(){
		$check = I('get.check');
		$id = explode(',',I('get.id'));
		$len = count($id);
		// 审核
		for($i = 0; $i < $len; $i++){
			$uid = $id[$i];
			if($check == 1){
				$data['audit_status'] = 1;
				$res = M('guestbook')->where("id = $uid")->save($data);
			}elseif($check == 2){
				$data['audit_status'] = 0;
				$red = M('guestbook')->where("id = $uid")->save($data);
			}elseif($check == 3){
				$data['status'] = 0;
				$red = M('guestbook')->where("id = $uid")->save($data);
			}elseif($check == 4){
				$data['recommend'] = 1;
				$red = M('guestbook')->where("id = $uid")->save($data);
			}elseif($check == 5){
				$data['recommend'] = 0;
				$red = M('guestbook')->where("id = $uid")->save($data);
			}
		}
		Header("Location:".U('Api/Guestbookadmin/index'));
	}

	/*
		搜索
	 */
	public function search($post){
		$iphone = $post['iphone'];
		$name 	= $post['name'];
		$time1  = $post['start_time'];
		$time2	= $post['end_time'];

		if($iphone && $iphone != 0){
			$phone['iphone'] = array("like", "%".$iphone."%");
			$id = implode(',',M('member')->where($phone)->field('uid')->find());
			if(!empty($id)){
				$uid = $id;
				$where = "uid = $uid";	
			}
		}
		if($name){
			//$where['title'] = $name;
			$where = "title like '%".$name."%'";
		}
		if($time1){
			//$where['createtime'] = array("gt", $time1);
			$where = "createtime > '$time1'";
		}
		if($time2){
			//$where['createtime'] = array("lt", $time2);
			$where = "createtime < '$time2'";
		}
		if($iphone && $name){
//			$where['uid'] = $uid;
//			$where['title'] = $name;
			$where = "uid = '$uid' and title like '%".$name."%'";
		}
		if($iphone && $time1){
//			$where['uid'] = $uid;
//			$where['createtime'] = array("gt", $time1);
			$where = "uid = '$uid' and createtime > '$time1'";
		}
		if($iphone && $time2){
//			$where['uid'] = $uid;
//			$where['createtime'] = array("lt", $time2);
			$where = "uid = '$uid' and createtime < '$time2'";
		}
		if($name && $time1){
//			$where['title'] = $name;
//			$where['createtime'] = array("gt", $time1);
			$where = "title like '%".$name."%' and createtime > '$time1'";
		}
		if($name && $time2){
//			$where['title'] = $name;
//			$where['createtime'] = array("lt", $time2);
			$where = "title like '%".$name."%' and createtime < '$time2'";
		}
		if($time1 && $time2){
			//$where['createtime'] = array('between', '$time1,$time2');
			$where = "createtime between '$time1' and '$time2'";
		}
		if($iphone && $name && $time1){
//			$where['uid'] = $uid;
//			$where['title'] = $name;
//			$where['createtime'] = array("gt", $time1);
			$where = "uid = '$uid' and title like '%".$name."%' and createtime > '$time1'";
		}
		if($iphone && $time1 && $time2){
//			$where['uid'] = $uid;
//			$where['createtime'] = array('between', '$time1,$time2');
			$where = "uid = '$uid' and createtime between '$time1' and '$time2'";
		}
		if($name && $time1 && $time2){
//			$where['title'] = $name;
//			$where['createtime'] = array('between', '$time1,$time2');
			$where = "title like '%".$name."%' and createtime between '$time1' and '$time2'";
		}
		if($iphone && $name && $time1 && $time2){
//			$where['uid'] = $uid;
//			$where['title'] = $name;
//			$where['createtime'] = array('between', '$time1,$time2');
			$where = "uid = '$uid' and title like '%".$name."%' and createtime between '$time1' and '$time2'";
		}
		$red[1] = $where;
		$red[2] = $iphone;
		$red[3] = $name;
		$red[4] = $time1;
		$red[5] = $time2;
		return $red;
	}
	/*
	 * 查看评论内容
	 */
	public function showComment(){
		$id = I("post_id", '');
		if(empty($id)){
			$this->error("请正常操作");
			exit;
		}
		$comment = M('guestbook_reply');
		$member = M('member');
		$return = $comment ->where("isadmin = 0 AND status = 1 AND guestbookid = ".$id)->select();
		foreach($return as $k=>$v){
			$return[$k]['user'] = $member->where("uid = ".$v['uid']) ->field("uid,nickname,iphone")->find();
		}
		$this->assign('count',$return);
		$this->display();

	}
	/*
	 * 删除
	 */
	public function del(){
		$reply = M('guestbook_reply');
		$data['status'] = 0;
		if(isset($_GET['id'])){
			$id = intval(I("get.id"));
			if ($reply->where("id=$id")->save($data)!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
		if(isset($_POST['ids'])){
			$ids=join(",",$_POST['ids']);
			if ($reply->where("id in ($ids)")->save($data)!==false) {
				$this->success("删除成功！");
			} else {
				$this->error("删除失败！");
			}
		}
	}
	/*
	 * 查看原文
	 */
	public function showArticle(){
		$id=intval($_GET['id']);
		if(empty($id)){
			$this->error("请正常操作");
			exit;
		}
		$guestbook = M("guestbook");
		$reply = M('guestbook_reply');
		$user_table = M('users');
		$member = M("member");
		$return = $guestbook -> where("id = ".$id)->find();
		$doctor = $reply->where("status = 1 AND isadmin = 1 AND guestbookid = ".$return['id'])-> order("replytime DESC")->select();
		foreach($doctor as $k=>$v){
			$user = $user_table -> where("id = ".$v['uid'])->field("user_login,user_url")->select();
			foreach($user as $key=>$value){
				$doctor[$k]['nickname'] = $value['user_login'];
				$doctor[$k]['avatar'] = $value['user_url'];
			}
		}
		$users = $reply->where("status = 1 AND isadmin = 0 AND guestbookid = ".$return['id'])-> order("replytime DESC")->select();
		foreach($users as $kk =>$vv){
			$user_infomation = $member -> where("uid = ".$vv['uid']) ->field("nickname,user_img")->select();
			foreach($user_infomation as $ka=>$ve){
				$users[$kk]['nickname'] = $ve['nickname'];
				$users[$kk]['user_img'] = $ve['user_img'];
			}
		}
		//print_R($doctor);die;
		$this->assign('article', $return);
		$this->assign('doctor', $doctor);
		$this->assign('users', $users);
		$this->display();
	}
}