<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class AuthorizationController extends AdminbaseController
{
    /*
     * 授权管理首页
     * */
    public function index(){
        $type = I("request.type");
        $UserLimitModel = D("UserLimit");
        $Result = $UserLimitModel->SelAllUser($type);
        $this->assign("lists",$Result['data']);
        $this->assign("page",$Result['page']);
        $this->display("index");
    }
    /*
     * 已授权
     * */
    public function AlreadyAuthorized(){
        $type = I("request.type");
        $UserLimitModel = D("UserLimit");
        $Result = $UserLimitModel->SelAllUser($type);
        $this->assign("lists",$Result['data']);
        $this->assign("page",$Result['page']);
        $this->display("already_authorized");
    }

    /*
         * 未授权
         * */
    public function NotAuthorized(){
        $type = I("request.type");
        $UserLimitModel = D("UserLimit");
        $Result = $UserLimitModel->SelAllUser($type);
        $this->assign("lists",$Result['data']);
        $this->assign("page",$Result['page']);
        $this->display("not_authorized");
    }
    /*
         * 评分不足
         * */
    public function InsufficientScore(){
        $type = I("request.type");
        $UserLimitModel = D("UserLimit");
        $Result = $UserLimitModel->SelAllUser($type);
        $this->assign("lists",$Result['data']);
        $this->assign("page",$Result['page']);
        $this->display("insufficient_score");
    }
    /*
         * 提额管理
         * */
    public function VolumeManagement(){
        $type = I("request.type");
        $UserLimitModel = D("UserLimit");
        $Result = $UserLimitModel->SelAllUser($type);
        $this->assign("lists",$Result['data']);
        $this->assign("page",$Result['page']);
        $this->display("volume_management");
    }

    /*
     * 搜索公共方法
     * */
    public function AuthorizationSearch(){
        $where = I("request.");
        $UserLimitModel = D("UserLimit");
        $Result = $UserLimitModel->SeachUserLimit($where);
        print_r($Result);die;
        $this->ajaxReturn($Result);die;
    }

    /*
     * 授权
     * */
    public function Authorization(){
        $id = I("request.id");
        $money = I("request.money");
        $UserLimitModel = D("UserLimit");
        $Result = $UserLimitModel->Authorization($id,$money);
        echo $Result;exit;
    }

    /*
     * 修改建议金额
     * */
    public function EditMonet(){
        $id = I("request.id");
        $money = I("request.money");
        $type  = I("request.type");
        $UserLimitModel = D("UserLimit");
        $Result = $UserLimitModel->Authorization($id,$money,$type);
        echo $Result;exit;
    }

    /*
     * 拒绝用户申请
     * */
    public function RefuseAuthorization(){
        $id = I("request.id");
        $refuse     = I("request.refuse");
        $uid        = I("request.uid");
        $UserLimitModel = D("UserLimit");
        $Result = $UserLimitModel->RefuseAuthorization($id,$refuse,$uid);
        echo $Result;exit;
    }

    /*
     * 查看拒绝原因
     * */
    public function ReasonsForRefusal(){
        $id = I("request.id");
        $UserLimitModel = D("UserLimit");
        $Result = $UserLimitModel->ReasonsForRefusal($id);
        echo $Result;exit;
    }
}
