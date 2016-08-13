<?php

/*
 * 预约管理
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class SubscribeController extends AdminbaseController {
	//预约管理首页
	public function Order(){
		$Table = D("order");
		$result = $Table->SelOrder();
		$this->assign('posts',$result['1']);
		$this->assign('page',$result['2']);
        $this->assign('arr',$result['3']);
		$this->display("index");
	}
        //预约老师列表
        public function Teacher(){
            $Table = D("order");
            $result = $Table->Teachers();
            $this->assign('lists',$result['1']);
            $this->assign('page',$result['2']);
            $this->assign('iphone',$result['3']);
            $this->assign('num',$result['4']);
            $this->assign('time1',$result['5']);
            $this->assign('time2',$result['6']);
            $this->display("teacher");
        }
        //预约项目列表
        public function Items(){  
            $Table = D("order");
            $result = $Table->Items();
            $this->assign('lists',$result['1']);
            $this->assign('page',$result['2']);
            $this->assign('iphone',$result['3']);
            $this->assign('num',$result['4']);
            $this->assign('time1',$result['5']);
            $this->assign('time2',$result['6']);
            $this->display("items");
        }
        //预约酒店列表
        public function Pub(){
            $Table = D("order");
            $result = $Table->Pub();
            $this->assign('lists',$result['1']);
            $this->assign('page',$result['2']);
            $this->assign('arr',$result['3']);
            $this->display("pub");
        }
         //审核订单
    public function Edits(){
        if(IS_GET){
            $date = I("get.");
            $Table = D("Order");
            $result = $Table->Editsaudit($date);
            if($result=="1"){
                    $this->success("审核中");
            }elseif($result=="2"){
                    $this->success("审核失败");
            }  elseif ($result=="3"){
                    $this->success("审核成功");
            }else {
                    $this->error("未审核");
            }
        }else{
                $this->error("网络错误");
        }
    }
    //审核项目
    public function Editss(){
        if(IS_GET){
            $date = I("get.");
            $Table = D("Order");
            $result = $Table->EditsItems($date);
            if($result=="1"){
                    $this->success("审核中");
            }elseif($result=="2"){
                    $this->success("审核失败");
            }  elseif ($result=="3"){
                    $this->success("审核成功");
            }else {
                    $this->error("未审核");
            }
        }else{
                $this->error("网络错误");
        }
    }
    //审核医师
    public function Editsss(){
        if(IS_GET){
            $date = I("get.");
            $Table = D("Order");
            $result = $Table->EditsTeacher($date);
            if($result=="1"){
                    $this->success("审核中");
            }elseif($result=="2"){
                    $this->success("审核失败");
            }  elseif ($result=="3"){
                    $this->success("审核成功");
            }else {
                    $this->error("未审核");
            }
        }else{
                $this->error("网络错误");
        }
    }
    //审核酒店
    public function Editssss(){
        if(IS_GET){
            $date = I("get.");
            $Table = D("Order");
            $result = $Table->EditsPub($date);
            if($result=="1"){
                    $this->success("审核中");
            }elseif($result=="2"){
                    $this->success("审核失败");
            }  elseif ($result=="3"){
                    $this->success("审核成功");
            }else {
                    $this->error("未审核");
            }
        }else{
                $this->error("网络错误");
        }
    }
    //审核总开关
    public function manselect(){
        $date = I("get.");
        $DateAC = D("Order");
        $result = $DateAC->ManSelect($date);
        $this->success();
    }

    /*
        ajsx审核医师预约
     */
    public function audits(){
        $post = I('get.');
        $table = D('Order');
        $res = $table->audits($post);
        if($res){
            echo '1';
        }else{
            echo '2';
        }
    }

    /*
        ajsx审核项目预约
     */
    public function audita(){
        $post = I('get.');
        $table = D('Order');
        $res = $table->audita($post);
        if($res){
            echo '1';
        }else{
            echo '2';
        }
    }

    /*
        自动审核
     */
    public function tests(){
        $post = I('get.audit');
        $table = D('Order');
        $red = $table->tests($post);
        return $red;
    }
}