<?php
namespace Common\Model;
use Common\Model\CommonModel;
class InstallMentModel extends CommonModel {
	/*
		主页显示
	 */
	public function sel($post){
		$sfit = $this->searchtime($post);
		$where = $sfit[1];
		$table = M('installment');
		$len = $table->group('periodstype')->select();
		$len = count($len);
		$page = new \Think\Page($len,10);
		$show = $page->show();
		$res[1] = $table->field('periodstype,createDate,period,status,count(periodstype)')->where($where)->limit($page->fristRow.','.$page->listRows)->group('periodstype')->select();
		$res[2] = $show;
		$res[3] = $sfit[2];
		$res[4] = $sfit[3];
		$res[5] = $sfit[4];
		return $res;
	}
	/*
		添加
	 */
	public function add_post($post){
		
		$date = date('Y-m-d H:i:s',time()); // 添加当前时间
		
		// 分期期数表
		$table = M('installment');

		$data['createDate'] 	= $date;				// 生成时间
		$data['periodsType'] 	= $post['periodstype'];	// 期数名称
		$data['operator'] 		= $_SESSION['name'];
		$data['period']			= $post['period'];
		$test = $post['reta'];
		foreach ($test as $k => $v) {
			$data['periods'] = $k+1;
			$data['rate'] = $v;
			$res = $table->add($data);
		}
		return $res;
	} 

	/*
		查询利率
	 */
	public function selment($get){
		$table = M('installment');
		$res = $table->where("periodstype = '$get'")->field('rate,period')->select();
		return $res;
	}

	/*
		查询单条
	 */
	public function selfind($name){
		$table = M('installment');
		$res = $table->where("periodstype = '$name'")->select();
		return $res;
	}

	/*
		修改启用禁用
	 */
	public function editstatus($get){
		$periodstype = $get['value'];
		$status['status'] = $get['key'];
		$table = M('installment');
		$res = $table->where("periodstype = '$periodstype'")->save($status);
		return $res;
	}

	/*
		修改利率与信息
	 */
	public function editrate($post){
		$data['periodsType'] = $post['periodstype'];
		$data['period'] 	 = $post['dateint'];
		$table = M('installment');
		foreach ($post['id'] as $k => $v) {
			$data['rate'] = $post['reta'][$k];
			$res = $table->where("id = $v")->save($data);
		}
		return $res;
	}

	/*
		查询产品
	 */
	public function Seltype(){
		$table = M('goods_type');
		$res = $table->select(); // 查询产品分类
		foreach ($res as $k => $v) {
			$res[$k]['level'] = $v['pid'];
		}
		$red = $this->sortOut($res);
		$len = count($res);
		for($i=0;$i<$len;$i++){
			$id = $red[$i]['id'];
			$red[$i]['types'] = implode(',',M('installment_goodstype')->where("goodstypeid = $id")->field('installmentid')->find());
		}
		$list[1] = $red;
		$list[2] = $res;
		return $list;
	}

	/*
		查询子类
	 */
	public function selgoods($get){
		$table = M('goods');
		$res = $table->where("gtype = $get")->select(); // 查询产品分类

		$len = count($res);
		for($i=0; $i<$len; $i++){
			$id = $res[$i]['id'];
			$res[$i]['types'] = implode(',',M('installment_goods')->field('installmentid')->where("goodsid = $id")->find());
		}
		return $res;
	}

	/*
    	无限极分类查询分类内容
  	*/
  	static public function sortOut($cate,$pid=0,$level=0,$html='&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'){
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

    /*
    	查询分期信息
     */
    public function editstall(){
    	$table = M('installment');
    	$res = $table->group('periodstype')->where('status = 1')->select();
    	return $res;
    }
    
    /*
    	修改信息
     */
    public function setting($post){
    	$id = $post['id'];    	// 20
    	$type = $post['type'];	// 1
    	$install = $post['s'];	// 12期

    	$data['installmentid'] = $install;
    	// 等于0 说数据库没有数据
    	$tables = M('installment_goodstype')->where("goodstypeid = $id")->select();
    	if($type == '1' && count($tables) == '0'){
    		$data['goodstypeid'] = $id;      					// 第一层 id = 2 
    		$res = M('installment_goodstype')->add($data);		// 修改id = 2 的
    		// 如果不等以1，说明分类下面有子类
    		$tabls = M('goods_type')->where("pid = $id")->field("id")->select();  // 查询2下面的pid
    		$len = count($tabls);
    		if($len != '0'){
    			for($i=0; $i<$len; $i++){
    				$isd = $tabls[$i]['id'];
    				$data['goodstypeid'] = $isd;
    				$res = M('installment_goodstype')->add($data);
    				$res = M('installment_goodstype')->save($data);
    				// 不等于一，说明下面还有子类
    				$tab = M('goods_type')->where("pid = $isd")->field("id")->select();  // 查询2下面的pid
    				$lens = count($tab);
    				if($lens != '0'){
    					for($j=0; $j<$lens; $j++){
    						$data['goodstypeid'] = $tab[$j]['id'];
    						$res = M('installment_goodstype')->add($data);
    						$res = M('installment_goodstype')->save($data);
    					}
    				}
    			}
    		}
       	}

       // 如果大于1说明数据库有数据
    	if($type == '1' && count($tables) > '0'){
    		$res = M('installment_goodstype')->where("goodstypeid = $id")->save($data);
    		// 不等于一。说明下面有子类
    		$tabls = M('goods_type')->where("pid = $id")->field("id")->select();  // 查询2下面的pid
    		$len = count($tabls);
    		if($len != '0'){
    			for($i=0; $i<$len; $i++){
    				$isd = $tabls[$i]['id'];
    				$data['goodstypeid'] = $isd;
    				$res = M('installment_goodstype')->where("goodstypeid = $isd")->save($data);
    				$res = M('installment_goodstype')->add($data);
    				// 不等以一 说明下面有子类
    				$tab = M('goods_type')->where("pid = $isd")->field("id")->select();  // 查询2下面的pid
    				$lens = count($tab);
    				if($lens != '0'){
    					for($j=0; $j<$lens; $j++){
    						$idd = $tab[$j]['id'];
    						$data['goodstypeid'] = $tab[$j]['id'];
    						$res = M('installment_goodstype')->add($data);
    						$res = M('installment_goodstype')->where("goodstypeid = $idd")->save($data);

    						// $this->asdlkj($idd, $install);
    					}
    				}
    				// else{
    				// 	$this->asdlkj($isd, $install);
    				// }
    			}
    		}
    		// else{
    		// 	$this->asdlkj($id, $install);
    		// }
    	}

    	// type = 2 , 穿过来的是商品
   		//$table = M('installment_goods')->where("goodsid = $id")->select();
   		//if($type == '2' && count($table) == '0') {
   		//$data['goodsid'] 	 = $id;
   		//$res = M('installment_goods')->add($data);
   		//}
   		//if($type == '2' && count($table) > '0'){
		//$res = M('installment_goods')->where("goodsid = $id")->save($data);
   		//}
    }

    /*
    	dsada
     */
    public function asdlkj($id, $install){
    	$goods = M('goods')->where("gtype = $id")->field('id')->select();
    	$good['installmentid'] = $install;
        $len = count($goods);
        if(count($len) != '0'){
	        for($i=0; $i<$len; $i++){
	        	$godid = $goods[$i]['id'];

	        	$reds = M('installment_goods')->where("goodsid = $godid")->find();
	        	if(count($reds) > '0'){
	            	M('installment_goods')->where("goodsid = $godid")->save($good);
		        }
		        if(count($reds) == '0'){
		        	$good['goodsid'] = $godid;
		        	M('installment_goods')->add($good);
		        }

	        }
	    }
    }
    /*
    	查询名字是否存在
     */
    public function ajaxName($post){
    	$res = M('installment')->where("periodsType = '$post'")->select();
    	return $res;
    }

    /*
		搜索
	 */
	public function searchtime($search, $lonk){
		$iphone = $search['search'];
		$time1  = $search['start_time'];
		$time2 	= $search['end_time'];
		if($iphone){
			$sear[1] = "periodstype = $iphone OR periodstype = $iphone";
			$sear[2] = $iphone;
		}

		if($time1){
			$sear[1] = "createDate >= '$time1'";
			$sear[3] = $time1;
		}

		if($time2){
			$sear[1] = "createDate <= '$time2'";
			$sear[4] = $time2;
		}

		if($iphone && $time1){
			$sear[1] = "createDate >= '$time1' and periodstype = $iphone OR periodstype = $iphone";
			$sear[2] = $iphone;
			$sear[3] = $time1;
		}

		if($iphone && $time1 && $time2){
			$sear[1] = "createDate >= '$time1' and createDate <= '$time2' and periodstype = $iphone OR periodstype = $iphone";
			$sear[2] = $iphone;
			
			$sear[3] = $time1;
			$sear[4] = $time2;
		}
		return $sear;
	}

	/*
		修改是否分期
	 */
	public function isinstall($post){
		$data['installmentid'] = $post['s'];

		$goodsid = $post['id'];
		$res = M('installment_goods')->where("goodsid = $goodsid")->find();
		if($res == ''){
			$data['goodsid'] = $goodsid;
			$red = M('installment_goods')->add($data);
		}else{
			$red = M('installment_goods')->where("goodsid = $goodsid")->save($data);
		}
		return $red;
	}

	/*
		修改用户组是否分期 // 自动刷新
	 */
	public function userInstall(){
		$table = M('member')->group('usergroup')->field('usergroup')->select();
		$len = count($table);
		for($i=0; $i<$len; $i++){
			$usergroup = $table[$i]['usergroup'];
			$table1 = M('installment_usergroup')->where("usergroupid = $usergroup")->find();
			if(!$table1){
				$data['usergroupid'] = $usergroup;
				M('installment_usergroup')->add($data);
			}
		}
		$res[2] = M('installment')->group('periodstype')->select();

		$len = M('installment_usergroup')->count();
		$page = new \Think\Page($len, 6);
		$show = $page->show();

		$res[1] = M('installment_usergroup')->limit($page->firstRow.','.$page->listRows)->select();
		$res[3]	= $show;
		return $res;
	}

	/*
		修改是否分期和分期信息
	 */
	public function editisinstall($post){
		$bug = $post['s'];
		$id = $post['id'];
		if($bug == 1){
			$data['isinstallment'] = $post['val'];
			$res = M('installment_usergroup')->where("id = $id")->save($data);
		}elseif($bug == 2){
			$data['installment'] = $post['val'];
			$res = M('installment_usergroup')->where("id = $id")->save($data);
		}
		return $res;
	}

	/*
		修改用户组的是否使用
	 */
	public function editisusess($post){
		$id = $post['id'];
		$value = $post['dat'];
		if($value == 1){
			$data['isusess'] = 0;
		}elseif($value == 0){
			$data['isusess'] = 1;
		}
		$res = M('installment_usergroup')->where("id = $id")->save($data);
		return $res;		
	}
}
