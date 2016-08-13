<?php
/*
 * 时间管理
 */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class TimeController extends AdminbaseController {
    public function time(){
        $DateModel = D("Date");
        $day7 = date('Y-m-d', strtotime('+7 day'));
        $TimeAdd = $DateModel->TimeAdd($day7);
        $this->assign('date',$TimeAdd);
        $this->display("index");
    }
    //显示move页面
    public function move(){
        $date = I("request.");
			$Type_Model = D("Time");
		if(IS_POST){
			if(isset($_GET['ids']) && isset($_POST['interest'])){
                                $post = I("request.");
				$Type_Model = M("date");
                                $id = $post['ids'];
                                $date = $post['interest'];
                                $count = count($date);
                                $str = "";
                                for($i=0;$i<=$count;$i++){
                                    $str.=$date[$i].",";
                                }
                                $str = trim($str,",");
                                $date['false'] = $str;
				$IS_OK = $Type_Model->where("id = $id")->save($date);
				if($IS_OK==""){
					$this->error("移动失败");
				}else{
					$this->success("移动成功");
				}
			}
		}else{
			$arr = $Type_Model->MoveType();
			$this->assign("posts",$arr);
			$this->display();
		}
	}
        //修改状态
        public function Edit(){
        if(IS_POST){
            $date = I("post.");
            $Table = D("Time");
            $result = $Table->MoveEdit($date);
            if($result=="1"){
                    $this->success("成功");
            }else{
                    $this->error("失败");
            }
    }else{
            $this->error("网络错误");
    }
	}
}
?>