<?php
namespace Common\Model;
use Common\Model\CommonModel;
class GoodsModel extends CommonModel {

	/*

	通过分类ID查询属于此分类的所有数据值

	*/

	public function SelMessage($id){
		$Table = M("goods");
		$lists = $Table->where("gtype = $id and state = 1")->order("gorder")->field("id,gname,about,img,humbimg,keyword,gorder")->select();
		$stick = $Table->where("gtype = $id and stick = 1 and state = 1")->order("time")->field("id,gname,about,img,humbimg,keyword,time,gorder")->select();
		$count = count($lists);
		for($i=0;$i<$count;$i++){
			$lists[$i]['keyword']=explode(' ',$lists[$i]['keyword']);
		}
		return $lists;
	}
	/*

	查询当前项目的详细信息

	*/

	public function SelPjectMessage($id){
		$GoodsTable = M("goods");
		$ProductTable = M("product");
		$Goods_2Table = M("goods_2");
//		$ServiceTable = M("service");
//		$ServiceTypeTable = M("service_type");
		$UsersTable = M("users");
		$lists = $GoodsTable->where("id = $id")->field("id,gname,about,content,aid")->find();
		$gid = $lists['id'];
		$goods2 = $Goods_2Table->where("gid = $gid")->find();
		unset($lists['parameter']);
		$doctor = $UsersTable->where("is_doctor = 1 and recommend = 1")->field("id,user_login,user_url")->find();
		$doctor['did'] = $doctor['id'];
		unset($doctor['id']);
		$ProductID = $lists['aid'];
		if(!empty($ProductID)){
			$Product = $ProductTable->where("id =$ProductID")->field("id,picture")->find();
		}
//		$serviceID = $lists['service'];
//		$ServiceLists = $ServiceTable->where("sid = $serviceID")->field("service")->find();
//		$Serviceids = $ServiceLists['service'];
//		$ServiceTypeLists = $ServiceTypeTable->where("id in ($Serviceids)")->field("tname")->select();
		$lists['content'] = $this->ortp($lists['content']);
		//$lists['content'] = html_entity_decode($lists['content']);
		$Date['goodsmess'] = $lists;
		$arr = array();
		$arr = $goods2;
		if($goods2!=""){
			$Date['parameter'] = $arr;
		}
		$Date['doctor'] = $doctor;
		$Date['facemess'] = $Product;
		//$Date['servicemess'] = $ServiceTypeLists;
		return $Date;
	}
	/*
    过滤<p>标签
    */
	public function ortp($count){
		$arr = str_replace("&lt;p&gt;","",$count);
		$arr = str_replace("&lt;/p&gt;","",$arr);
		$arr = str_replace("&nbsp;","",$arr);
		$arr = str_replace("&lt;P&gt;","",$arr);
		$arr = str_replace("&lt;/P&gt;","",$arr);
		$arr = str_replace("<p>","",$arr);
		$arr = str_replace("<br/>","",$arr);
		$arr = str_replace("</p>","",$arr);
		$arr = str_replace("<P>","",$arr);
		$arr = str_replace("</P>","",$arr);
		//$arr = html_entity_decode($arr);
		return $arr;
	}

	/*
	 * 过滤字符串
	 * */
	public function br2nl($string){
		$array = array('<p>','</p>','&lt;&gt;');
		return str_replace($array,"",$string);//字符串替换
	}
	/*

        根据项目ID查询服务流程ID

        */

	public function SelGoodsService($Projectid){
		$id = $Projectid['projectid'];
		$GoodsServiceId = M("goods")
			->where("id = $id")
			->field("service")
			->find();
		return $GoodsServiceId;
	}
	/*

        根据预约单的ID查询出颜值、项目、的信息

    */

	public function SelGoodsReservation($Projectid){
		$GoodsTable = M("goods");
		$GoodsTypeTable = M("goods_type");
		$result = $GoodsTable->where("id = $Projectid")->field("id,aid,gname,gtype,humbimg,about,keyword")->select();
		$count = count($result);
		for($i=0;$i<$count;$i++){
			$result[$i]['typename'] = $GoodsTypeTable->where("id = ".$result[$i]['gtype'])->field("id,typename,pid")->find();
			$result[$i]['typename'] = $GoodsTypeTable->where("id = ".$result[$i]['typename']['pid'])->field("id,typename,pid")->find();
			$result[$i]['typeid'] = $result[$i]['typename']['pid'];
			$result[$i]['typename'] = $result[$i]['typename']['typename'];
			if($result[$i]['aid']!=0){
				$result[$i]['activelink'] = $result[$i]['aid'];
			}
		}
		return $result;die;
	}
	/*

     * 查询相关数据

     * $key  用户输入的关键字

     * */

	public function SearchKey($key,$type){
		$GoodsTable = M("goods");
		$where = "gname like '%$key%' and state=1";
		if(empty($type)){
			$field = "id,gname";
		}else{
			$field = "id,gname,about,img,humbimg,keyword";
		}
		$resultGoods = $GoodsTable->where($where)->field($field)->select();
		if(empty($resultGoods)){
			$result['resultnum'] = "1";
			$result['result_mess'] = "失败";
			$result['result'] = "";
		}else{
			$result['resultnum'] = "0";
			$result['result_mess'] = "成功";
			$result['result'] = $resultGoods;
		}
		return $result;exit;
	}
	/*

     * 搜索加载页面

     * */

	public function SelSearchHot(){
		$GoodsTable  = M("goods");
		$DoctorTable = M("users");
		$Goods  = $GoodsTable->order("number desc")->field("id,keyword")->limit(0,6)->select();
		$Doctor = $DoctorTable->where("is_doctor = 1")->field("id,user_login,user_url")->limit(0,3)->select();
		$data['goods']  = $Goods;
		$data['doctor'] = $Doctor;
		$returnmess['resultnum'] = "0";
		$returnmess['result_mess'] = "成功";
		$returnmess['result'] = $data;
		return $returnmess;exit;
	}
}