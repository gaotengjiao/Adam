<?php
/*
	医生管理页
 */
namespace Common\Model;
use Common\Model\CommonModel;
class UsersModel extends CommonModel {
	/*
		查询所有医师信息
	 */
	public function selects(){
		$table  = M('users');
		
		$data = $table->where("is_doctor = 1")->order('id DESC')->limit($page->firstRow.','.$page->listRows)->select();
	
		$len = count($data); 	// 查询总条数
		$page = new \Think\Page($len, 15);
		$show = $page->show(); // 分页显示输出
		$user['1'] = $data;
		$user['2'] = $show; 
		return $user;
	} 

	/*
		处理医师时间
	 */
	public function edittime($tid){
		$table = M('date');  

		$id = $tid;
		for($i = 0; $i < 7; $i++){
    		$date['date'] = date('Y-m-d', strtotime('+'.$i.' day'));
    		$date['tid'] = $id;
    		$date['state'] = '1';
    		$red = $table->where($date)->select();
    		
    		if(count($red) == 0){
    			$res = $table->add($date);
    		}
		}

		return $res;
	}

	/*
		查询医师时间表
	 */
	public function selectdate($tid){
		$table = M('date');
		$id = $tid;
		for($i = 1; $i < 8; $i++){
			$date['date'] = date('Y-m-d', strtotime('+'.$i.' day'));
    		$date['tid'] = $id;
    		$date['state'] = '1';
    		$res[$i] = $table->where($date)->select();
		}
		
		return $res;
	}

	/*
		查询时间段
	 */
	public function selecttime(){
		$table = M('time');

		$res = $table->select();

		return $res;
	}

	public function SelFalseTime($data){
		$tid = $data['id'];
		$date = $data['value'];
		$DateTable = M('date');
		$TimeTable = M("time");
		$Datefalse = $DateTable->where("id = $tid")->field("false")->find();
		$Datefalse = $Datefalse['false'];
		$FalseTime = explode(",",$Datefalse);
		$FalseCount = count($FalseTime);
		$time      = $TimeTable->field("id,t_time")->select();
		$len = count($time);
		for($j=0;$j<$len;$j++){
			if($time[$j][id] != $FalseTime){
				$arr[] = $time[$j][id];
			}
		}
		 
		$str = "";
		$str.="<tr class='timetr'>";
		foreach($time as $k=>$v){
			// print_r($v);
			// 
			$str.="<td><input class='checks' name='false'"; 
			for ($i=0;$i<$FalseCount;$i++) {
				if($v['id'] == $FalseTime[$i])
					$str.="checked=checked";
			}
			$str .= " type='checkbox' value=".$v['id']." >".$v['t_time']."</td>";
		}
		$str.="</tr>";
		return $str;
	}

	/*
		修改老师的作息表
	 */
	public function editfalse($post){
		$id['id'] 		= $post['id'];
		$false['false'] = $post['false'];
		$table = M('date');	
		// $red = $table->where($id)->find();
		$res = $table->where($id)->save($false);
		return $res;
	}

	/*
		全选操作
	 */
	public function editChecked($id){
		$table = M('time')->field('id')->select();
		for($i=0; $i<count($table); $i++){
			$num[] = $table[$i]['id'];
		}
		$number['false'] = implode(',', $num);
		$deta = M('date');
		$res = $deta->where("id = $id")->save($number);
		return $res;
	}

	/*
		修改医师状态
	 */
	public function editstatus($status){
		$id = $status['i'];
		if($status['e'] == 1){
			$post['status'] = 0;
		}
		if($status['e'] == 0){
			$post['status'] = 1;
		}
		$data = M('users');
		$res = $data->where("id = $id")->save($post);
		return $res;
	}
}