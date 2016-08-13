<?php
namespace Common\Model;
use Common\Model\CommonModel;
class OrderModel extends CommonModel {

	/*
		查看预约列表
	*/
	public function SelOrder(){
		$Table = M("order");
		$count      = $Table->count();// 查询满足要求的总记录数
    	$Page       = new \Think\Page($count,20);// 实例化分页类 传入总记录数和每页显示的记录数(25)
    	$show       = $Page->show();// 分页显示输出
    	// 进行分页数据查询 注意limit方法的参数要使用Page类的属性
    	$list = $Table->join("s_teacher on s_order.uid = s_teacher.uid")->join("s_pub on s_order.uid = s_pub.uid")->join("s_items on s_order.uid = s_items.uid")->limit($Page->firstRow.','.$Page->listRows)->select();
        $facearr = M("goods_type")->where("state = 1")->select();
		$arr = $this->sortOut($facearr);
    	$Order['1'] = $list;
    	$Order['2'] = $show; 
        $Order['3'] = $arr;
		return $Order;
	} 
	/*
		无限极分类查询分类内容
	*/
	static public function sortOut($cate,$pid=0,$level=0,$html='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'){
        $tree = array();
        foreach($cate as $v){
            if($v['pid'] == $pid){
            	$v['pid'] = $level + 1;
                $v['html'] = str_repeat($html, $level);
                $tree[] = $v;
                $tree = array_merge($tree, self::sortOut($cate,$v['id'],$level+1,$html));
               }
            }
        return $tree;
      }
        //审核
    public function Editsaudit($date){
    	$Table = M("order");
		$status = $date['audit'];
		$id 	= $date['id'];
		$Edata['audit'] = "$status";
		$Edit = $Table->where("id = $id")->save($Edata);
		return $Edit;
	}
	//审核医师
	public function EditsTeacher($date){
		$Table = M("teacher");
		$status = $date['audit'];
		$id 	= $date['id'];
		$Edata['audit'] = "$status";
		$Edit = $Table->where("id = $id")->save($Edata);
		return $Edit;
	}
	//审核项目
    public function EditsItems($date){
    	$Table = M("items");
		$status = $date['audit'];
		$id 	= $date['id'];
		$Edata['audit'] = "$status";
		$Edit = $Table->where("id = $id")->save($Edata);
		return $Edit;
	}
	//审核酒店
	public function EditsPub($date){
    	$Table = M("pub");
		$status = $date['audit'];
		$id 	= $date['id'];
		$Edata['audit'] = "$status";
		$Edit = $Table->where("id = $id")->save($Edata);
		return $Edit;
	}
        //预约老师列表
        public function Teachers(){
            //查询条件
            $iphone = I('post.iphone');
            $t_num = I('post.keyword');
            $time1 = strtotime(I('post.start_time'));   // 开始时间
            $time2 = strtotime(I('post.end_time'));     // 结束时间
            $where = "id";
            if($t_num){
                $where = "t_num = '$t_num'";
                $Order['4'] = $t_num;
            }

            if($time1){
                $where = "time > $time1";
            }
            if($time2){
                $where = "time < $time2";
            }

            if($time1 && $time2){
                $where = "time > $time1 and time < $time2";
            }

            if($time1 && $t_num){
                $where = "time > $time1 and t_num = '$t_num'";
            }
            if($time2 && $t_num){
                $where = "time < $time2 and t_num = '$t_num'";
            }
            if($time1 && $time2 && $t_num){
                $where = "time > $time1 and time < $time2 and t_num = '$t_num'";
            }
            $Table = M("teacher");
            $count      = $Table->where($where)->select();// 查询满足要求的总记录数
            $len = count($count);
            $Page       = new \Think\Page($len,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show       = $Page->show();// 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $Table->where($where)->order('time desc')->limit($Page->firstRow.','.$Page->listRows)->select();
            $le = count($list);
            // 查询医师名称   
            for($i=0; $i<$le; $i++){
                $id = $list[$i]['tid'];
                $list[$i]['tid'] = implode(',', M('users')->where("id = $id")->field('user_nicename')->find());
            }

            // 查询用户名称手机号
            for($j=0; $j<$le; $j++){
                $ids = $list[$j]['uid'];
                $list[$j]['uname'] = implode(',', M('user')->where("uid = $ids")->field('uname')->find());
                $list[$j]['iPhone'] = implode(',', M('member')->where("uid = $ids")->field('iphone')->find());
            }

            // 如果手机号存在
            if($iphone){
                for($c=0; $c<$len; $c++){
                    $iph = $list[$c]['iPhone'];
                    if($iph == $iphone){
                        $arr[] = $list[$c];    
                    }
                }
                $Order['1'] = $arr;
                $Order['3'] = $iphone;
            }else{
                $Order['1'] = $list;
            }

            $Order['2'] = $show;
            $Order['5'] = I('post.start_time');
            $Order['6'] = I('post.end_time');
            return $Order;
        }
        //预约项目列表
        public function items(){
            //查询条件
            $iphone = I('post.iphone');
            $t_num = I('post.keyword');
            $time1 = strtotime(I('post.start_time'));   // 开始时间
            $time2 = strtotime(I('post.end_time'));     // 结束时间
            $where = "id";
            if($t_num){
                $where = "i_num = '$t_num'";
                $Order['4'] = $t_num;
            }

            if($time1){
                $where = "time > $time1";
            }
            if($time2){
                $where = "time < $time2";
            }

            if($time1 && $time2){
                $where = "time > $time1 and time < $time2";
            }

            if($time1 && $t_num){
                $where = "time > $time1 and i_num = '$t_num'";
            }
            if($time2 && $t_num){
                $where = "time < $time2 and i_num = '$t_num'";
            }
            if($time1 && $time2 && $t_num){
                $where = "time > $time1 and time < $time2 and i_num = '$t_num'";
            }

            $Table = M("items");
            $count      = $Table->where($where)->select();// 查询满足要求的总记录数
            $len = count($count);
            $Page       = new \Think\Page($len,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show       = $Page->show();// 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $Table->order('time desc')->where($where)->limit($Page->firstRow.','.$Page->listRows)->select();
            $le = count($list);
            // 查询用户名称手机号
            for($j=0; $j<$le; $j++){
                $ids = $list[$j]['uid'];
                $list[$j]['uname'] = implode(',', M('user')->where("uid = $ids")->field('uname')->find());
                $list[$j]['iPhone'] = implode(',', M('member')->where("uid = $ids")->field('iphone')->find());
            }
            // 如果手机号存在
            if($iphone){
                for($c=0; $c<$len; $c++){
                    $iph = $list[$c]['iPhone'];
                    if($iph == $iphone){
                        $arr[] = $list[$c];    
                    }
                }
                $Order['1'] = $arr;
                $Order['3'] = $iphone;
            }else{
                $Order['1'] = $list;
            }

            $Order['2'] = $show;
            $Order['5'] = I('post.start_time');
            $Order['6'] = I('post.end_time');
            return $Order;
        }
        //预约酒店列表
        public function pub(){
            $Table = M("pub");
            $count      = $Table->count();// 查询满足要求的总记录数
            $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
            $show       = $Page->show();// 分页显示输出
            // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
            $list = $Table->join("s_goods on s_pub.gid = s_goods.id")->join("s_user on s_pub.uid = s_user.uid")->join("s_goods_type on s_pub.id = s_goods_type.id")->limit($Page->firstRow.','.$Page->listRows)->select();
            $facearr = M("goods_type")->where("state = 1")->select();
            $arr = $this->sortOut($facearr);
            $Order['1'] = $list;
            $Order['2'] = $show;
            $Order['3'] = $arr;
            return $Order;
        }
        //自动审核
        public function ManSelect($date){
            $Table = M("teacher");
            $status = $date['audit'];
            $id     = $date['id'];
            $Edata['audit'] = "$status";
            $Edit = $Table->where("id = $id")->save($Edata);
            return $Edit;
        }

        /*
            ajax审核医师订单
         */
        public function audits($post){
            $id = $post['id'];
            $audit['audit'] = $post['audit'];
            $table = M('teacher');
            $res = $table->where("id = $id")->save($audit);
            return $res;
        }

        /*
            ajax审核医师订单
         */
        public function audita($post){
            $id = $post['id'];
            $audit['audit'] = $post['audit'];
            $table = M('items');
            $res = $table->where("id = $id")->save($audit);
            return $res;
        }

        /*
            自动审核项目
         */
        public function tests($post){
            $audit['audit'] = $post;
            $table = M('items');
            $res = $table->where('audit = 0 or audit = 1')->save($audit);
            return $res;
        }
}