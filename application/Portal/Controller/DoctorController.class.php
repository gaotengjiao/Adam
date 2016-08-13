<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController; 
/**
 * 名师预约
 */
 //header("Access-Control-Allow-Origin: *");
class DoctorController extends HomebaseController {
  	/*
  	通过预约类型ID查询相关的信息
  	*/
  	public function Doctor(){
  		$UsersModel = D("users");
  		$lists = $UsersModel->SelDoctor();
  	}
}