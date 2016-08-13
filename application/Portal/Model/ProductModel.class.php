<?php
/*
	颜值活动
*/
namespace Common\Model;
use Common\Model\CommonModel;
class ProductModel extends CommonModel {
	public function SelActive($id){
		$ProductTable = M("product");
		$result = $ProductTable->where("id = $id")->find();
		return $result;die;
	}
}