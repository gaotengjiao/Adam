<?php
/* * 
 * 颜值专属活动管理
     颜值专属活动 
     查询颜值活动信息
     查看颜值已过期活动
     添加颜值活动页面
     添加活动
     验证活动结束时间是否在开始活动之前
     验证时间
     活动过期/活动开启
     修改颜值活动信息页面
     修改颜值活动信息
     删除颜值活动
     查询当前活动已经预约的人
     */
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class FaceController extends AdminbaseController {
    //查询颜值活动信息
    public function Activity(){
        $faceinfo = D("Product");  
        $facess   = $faceinfo->not_out_of_date();
        $this->assign('lists',$facess['1']);
        $this->assign('page',$facess['2']);
        $this->display('index');
    }
    /*
        查看颜值已过期活动
    */
     public function Dated(){
        $faceinfo = D("Product");
        $facess   = $faceinfo->past_due();
        $this->assign('lists',$facess['1']);
        $this->assign('page',$facess['2']);
        $this->display('down');
    }
    


    /*
     * 添加颜值活动页面
     */
    public function Faceadd(){
        $this->display('add');
    }

     /*
        添加活动
    */
    
    public function Faceadd_post(){
       if(IS_POST){ 
            $data = I("post.");
            $Face = D("Product");
            $result = $Face->FaceAdd($data);
            if($result){
                $this->success();
            }
       }else{
           echo "提交方式不正确";
       }
    }

    /*
        验证活动结束时间是否在开始活动之前
    */
    public function Stoptime()
    {
        if(IS_POST){
            $stoptime = I("post.stoptime");
            $FaceTime = D("Product");
            $FaceStopTime = $FaceTime->FaceStopTime($stoptime);
            print_r($FaceStopTime);
        }else{
            print_r("00009");
        }
    }

    /*
        验证时间
    */
    public function Exittime(){
        if(IS_POST){
            $timeto = I("post.");
            $FaceTime = D("Product");
            $FaceStopTime = $FaceTime->Facestrattime($timeto);
            print_r($FaceStopTime);
        }else{
            print_r("00009");
        }
    }
   
    /*
        活动过期/活动开启
    */

    public function Datedac(){
        if(IS_POST){
            $id   = I("id");
            $type = I("type");
            if($id=="" | $type==""){
                $result['resultnum'] = "101";
                $result['result_mess'] = "提交错误";
                $result['result'] = "";
                $this->ajaxReturn($result);exit;
            }else{
                $ProductModel = D("Product");
                $result = $ProductModel->editState($id,$type);
                $this->ajaxReturn($result);exit;
            }
        }else{
            $result['resultnum'] = "4003";
            $result['result_mess'] = "提交方式错误";
            $result['result'] = "";
            $this->ajaxReturn($result);exit;
        }
    }
    /*
     * 修改颜值活动信息页面
     */
    public function Facewedit(){
        $id = I("get.id");
        $lists = M("product")->where("id=$id")->find();
        foreach ($lists as $k => $v) {
            $lists[$k]['introduce'] = html_entity_decode($v['introduce']);
        }
        $this->assign("lists",$lists);
        $this->display("edit");
    }

    /*
        修改颜值活动
    */
    public function Faceexid(){
       if(IS_POST){
            $date = I("post.");
            $Face = D("Product"); 
            $result = $Face->FaceSave($date);
            if($result){
                $this->success();
            }
       }
    }

    /*
     * 删除颜值活动
     */
    public function Facedelete(){
            $id = I("get.value");
            $DateAC = D("Product");
            $result = $DateAC->DelDate($id);
            if($result){
                echo '1';
            }else{
                echo '2';
            }
    }    
    /*
        查询当前活动已经预约的人
    */
    public function Selinfo(){
        $id = I("get.id");
        $Activity = D("Activity");
        $result = $Activity->Selinfo($id);
        $this->assign("lists",$result);
        $this->display("details");
    }

    /*
        修改时间为静止
     */
    public function ajaxState(){
        $id = I('get.id');
        $table = M('product');
        $state['state'] = 99;
        $res = $table->where("id = $id")->save($state);
        if($res){
            echo '1';
        }
    }
}
