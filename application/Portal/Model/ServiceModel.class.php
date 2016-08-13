<?php
namespace Common\Model;
use Common\Model\CommonModel;
class ServiceModel extends CommonModel {
	/*
	查询服务流程信息
	*/
	public function SelService($GoodsServiceId){
		$serviceid = $GoodsServiceId['service'];
		$ServiceTable = M("service")
		->where("sid = $serviceid")
		->field("service")
		->find();
		$ServiceInId = $ServiceTable['service'];
		$ServiceTypeTable = M("service_type")
		->where("id in ($ServiceInId)")
		->field("tname,img")
		->select();
		return $ServiceTypeTable;die;
	}
}