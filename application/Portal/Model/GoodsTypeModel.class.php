<?php
namespace Common\Model;
use Common\Model\CommonModel;
class GoodsTypeModel extends CommonModel {
	public function SelType($typeoneid){
		if(empty($typeoneid)){
            $returnmess['resultnum']    = "101";
            $returnmess['result_mess']  = "提交错误";
            $returnmess['result']       = "";
            return $returnmess;exit;
		}
		$Table = M("goods_type");
		$lists = $Table->where("pid = $typeoneid")->field("id,typename")->select();
		if(empty($lists)){
            $returnmess['resultnum']    = "102";
            $returnmess['result_mess']  = "未能查询到分类信息";
            $returnmess['result']       = "";
            return $returnmess;exit;
		}
		$count = count($lists);
		for($i=0;$i<$count;$i++){
			$len = strlen($lists[$i]['typename']) / 3;
            $len = substr($len,0,1);
			if($len==2){
				$lists[$i]['len'] = "2.3";
			}
			if($len==3){
				$lists[$i]['len'] = "3";
			}
			if($len==4 | $len==1){
				$lists[$i]['len'] = "3.7";
			}
		}
		return $lists;
	}

	public function info($id){
		$Table = M("goods_type");
		$lists = $Table->where("pid = $id")->field("id,typename")->select();
		$count = count($lists);
		for($i=0;$i<$count;$i++){
			$len = strlen($lists[$i]['typename']) / 3;
			if($len==2){
				$lists[$i]['len'] = "2.3";
			}
			if($len==3){
				$lists[$i]['len'] = "3";
			}
			if($len==4){
				$lists[$i]['len'] = "3.7";
			}
		}
		return $lists;
	}

	/*
	 * 引导页-分类页
	 * */
	public function SelTypeAll(){
		$GoodsTypeTable = M("goods_type");
		$GoodsTypeOne = $GoodsTypeTable->where("pid = 2 and state = 1")->field("id,typename,pid")->select();
		$GoodsTypeOneCount = count($GoodsTypeOne);
		$str = "";
		for($i=0;$i<$GoodsTypeOneCount;$i++){
			$str.= $GoodsTypeOne[$i]['id'].",";
		}
		$str = rtrim($str,",");
		$GoodsTypeAll = $GoodsTypeTable->where("pid in($str) and state = 1")->field("id,typename,pid")->select();
		$data[0]['typeone'] = $GoodsTypeOne;
		$data[0]['typeall'] = $GoodsTypeAll;
		return SONEOK($data);exit;
	}
}