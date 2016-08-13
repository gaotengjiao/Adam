<?php
namespace Common\Model;
use Common\Model\CommonModel;
class ProductModel extends CommonModel {

	/*
		查询未过期活动

	*/
	public function not_out_of_date()
	{
		$face = M('product'); // 实例化User对象
        $count      = $face->where("state = 1")->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $face->where("state = 1")->order('starttime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        foreach ($list as $k => $v) {
        	$list[$k]['introduce'] = strip_tags(htmlspecialchars_decode($v['introduce']));
        }
        $facearr['1'] = $list;
        $facearr['2'] = $show;
		return $facearr;
	}

	/* 
		查询过期的活动
	*/

		public function past_due()
	{
		$face = M('product'); // 实例化product对象
    	$count      = $face->where("state = 99")->count();// 查询满足要求的总记录数
        $Page       = new \Think\Page($count,10);// 实例化分页类 传入总记录数和每页显示的记录数(25)
        $show       = $Page->show();// 分页显示输出
        // 进行分页数据查询 注意limit方法的参数要使用Page类的属性
        $list = $face->where("state = 99")->order('starttime DESC')->limit($Page->firstRow.','.$Page->listRows)->select();
        $introduce = count($list);
        for($i=0;$i<$introduce;$i++){
            $str = $list[$i]['introduce'];
           $str =  mb_substr($str,0,14,'utf-8');
            $list[$i]['content'] = $str."...";
        }
        $facearr['1'] = $list;
        $facearr['2'] = $show;
		return $facearr;
	}

	/*
		验证活动结束时间是否与
	*/
		public function FaceStopTime($stoptime){
			$stopTime = strtotime("$stoptime");
			$starttime = time();
			if($stopTime<$starttime){
				return 1;
			}else{
				return 2;
			}
		}

		/*
			验证开始时间
		*/
		public function Facestrattime($timeto){
			$endtime = $timeto['stoptime'];
			$statime = $timeto['stoptime'];
			$stopTime = strtotime("$endtime");
			$startTime = strtotime("$statime");
			$time = time();
			if($startTime<$time){
				if($stopTime<$startTime){
					return 3;
				}
				return 1;
			}else{
				return 2;
			}
		}

		/*
			添加颜值专属活动
		*/
		public function FaceAdd($data){
			$face = M('product'); // 实例化product对象 
            $NewArr['p_name']           = $data['p_name'];
            $NewArr['level']            = $data['level'];
            $NewArr['picture']          = $data['img_url']['0'];
            $NewArr['price']            = $data['price'];
            $NewArr['state']  			= $data['state'];
            $NewArr['introduce']        = html_entity_decode($data['content']);

            $NewArr['starttime']        = strtotime($data['start_time']);
            $NewArr['stoptime']         = strtotime($data['end_time']);
            $FaceAdd = $face->add($NewArr);
            $data = date('Ymd');
            chmod("data/upload/".$data, 0777);
			return $FaceAdd;
		}

		 /*
        过滤<p>标签
     */
	    public function ortp($count){
	        $arr = str_replace("&lt;p&gt;","",$count);
	        $arr = str_replace("&lt;/p&gt;","",$arr);
	        $arr = str_replace("&lt;P&gt;","",$arr);
	        $arr = str_replace("&lt;/P&gt;","",$arr);
	        $arr = str_replace("<p>","",$arr);
	        $arr = str_replace("</p>","",$arr);
	        $arr = str_replace("<P>","",$arr);
	        $arr = str_replace("</P>","",$arr);
	        return $arr;
	    }

		/*
		修改当前活动状态
		*/
		public function EditState($id,$type){
            $where = "";
            if(is_array($id)){
                $id = implode(",", $id);
                $where.= "id in ($id)";
            }else{
                $id = $id;
                $where.= "id = $id";
            }
			$product = M("product");
			if($type=="Down"){
				$EdDate = '99';
			}
			if($type=="Up"){
				$EdDate = '1';
			}
			$product->state = "$EdDate";
			$resultNUm = $product->where($where)->save();
            if($resultNUm>0){
                $result['resultnum'] = "0";
                $result['result_mess'] = "成功";
                $result['result'] = "";
                return $result;exit;
            }else{
                $result['resultnum'] = "1";
                $result['result_mess'] = "失败";
                $result['result'] = "";
                return $result;exit;
            }
		 }

		 /*
			修改数据
		 */
		public function FaceSave($date){
			$post['p_name'] = $date['p_name'];
			$post['level']  = $date['level'];
			$post['price']  = $date['price'];
			$post['state']  = $date['state'];
			if($date['img_url'][0] == ''){
				$post['picture'] = $date['img'];
			}else{
				$post['picture'] = $date['img_url'][0];
			}

			$post['starttime'] = strtotime($date['start_time']);
			$post['stoptime']  = strtotime($date['end_time']);
			$post['introduce'] = html_entity_decode($date['content']);
			$product = M("product");
			$id = $date['id'];
			$result = $product->where("id=$id")->save($post);
			return $result;
		 }

		 /*
			删除数据（修改状态为删除）
		 */
		public function DelDate($id){
			$product = M("product");
			$state['state'] = "44";
			$result = $product->where("id = $id")->save($state);
			return $result;
		}
}