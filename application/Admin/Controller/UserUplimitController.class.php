<?php
namespace Admin\Controller;
use Common\Controller\AdminbaseController;
class UserUplimitController extends AdminbaseController
{
    /*
     * 授权
     * */
    public function Authorization(){
        $id = I("request.id");
        $uid = I("request.uid");
        $UserLimitModel = D("UserUplimit");
        $Result = $UserLimitModel->Authorization($id,$uid);
        echo $Result;exit;
    }

    /*
     * 拒绝用户申请
     * */
    public function RefuseAuthorization(){
        $id = I("request.id");
        $refuse     = I("request.refuse");
        $uid        = I("request.uid");
        $UserLimitModel = D("UserUplimit");
        $Result = $UserLimitModel->RefuseAuthorization($id,$refuse,$uid);
        echo $Result;exit;
    }

    /*
     * 查看拒绝原因
     * */
    public function ReasonsForRefusal(){
        $id = I("request.id");
        $refuse     = I("request.refuse");
        $uid        = I("request.uid");
        $UserLimitModel = D("UserUplimit");
        $Result = $UserLimitModel->ReasonsForRefusal($id,$refuse,$uid);
        echo $Result;exit;
    }
}
