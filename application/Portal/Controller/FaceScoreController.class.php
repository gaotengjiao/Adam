<?php
namespace Portal\Controller;
use Common\Controller\HomebaseController;
/**
 * 王鑫磊  2016-07-14  增加颜值Controller
 */
class FaceScoreController extends HomebaseController {
    /*
     * 颜值首页
     * $uid     用户ID
     * $token   用户唯一标示
     * */
    public function Index(){
        $Uid     = I("request.uid");
        $Token   = I("request.token");
        if($Uid=="" | $Token=="" | !is_numeric($Uid) | !is_numeric($Token)){
            $result = COMMITERROR();
            $this->ajaxReturn($result);
        }else{
            $UserLimitModel = D("UserLimit");
            $Result = $UserLimitModel->SelUserLimit($Uid,$Token);
            $this->ajaxReturn($Result);
        }
    }

    /*
     * 额度明细/查账还款
     * $uid     用户ID
     * $token   用户唯一标示
     * $type    当前用户需要访问的页面数据
     * */
    public function QuotaDetail(){
        $Uid     = I("request.uid");
        $Token   = I("request.token");
        $allnum   = I("request.allnum");
        $size     = I("request.size");
        $type     = I("request.type");
        if($Uid=="" | $Token==""| $type=="" | !is_numeric($Uid) | !is_numeric($Token) | !is_numeric($type)){
            $result = COMMITERROR();
            $this->ajaxReturn($result);
        }else{
            $UserLimitModel = D("UserLimit");
            $Result = $UserLimitModel->SelUserLimitDetail($Uid,$Token,$allnum,$size,$type);
            $this->ajaxReturn($Result);
        }
    }

    /*
     * 消费分期
     * $uid     用户ID
     * $token   用户唯一标示
     * */
    public function ConsumptionStage(){
        $Uid     = I("request.uid");
        $Token   = I("request.token");
        if(!is_numeric($Uid) | !is_numeric($Token) | $Uid=="" | $Token==""){
            $result = COMMITERROR();
            $this->ajaxReturn($result);
        }else{
            $UserBillModel = D("UserBill");
            $Result = $UserBillModel->ConsumptionStage($Uid,$Token);
            $this->ajaxReturn($Result);
        }
    }

    /*
     * 我要提额
     * $uid     用户ID
     * $token   用户唯一标示
     * */
    public function UserUpLimit(){
        $Uid     = I("request.uid");
        $Token   = I("request.token");
        if(!is_numeric($Uid) | !is_numeric($Token) | $Uid=="" | $Token==""){
            $result = COMMITERROR();
            $this->ajaxReturn($result);
        }else{
            $UserDataModel = D("UserData");
            $Result = $UserDataModel->SelUserData($Uid,$Token);
            $this->ajaxReturn($Result);
        }
    }

    /*
     * 我要分期
     * $Uid     用户ID
     * $Token   用户本站唯一标识
     * $usergroup   用户分组编号
     * */
    public function BillingInstallments(){
        $Uid     = I("request.uid");
        $Token   = I("request.token");
        $usergroup = I("request.group");
        if(!is_numeric($Uid) | !is_numeric($Token) |!is_numeric($usergroup) | $Uid=="" | $Token=="" | $usergroup==""){
            $result = COMMITERROR();
            $this->ajaxReturn($result);
        }else{
            $UserDataModel = D("Installment");
            $Result = $UserDataModel->SelInstallMent($Uid,$Token,$usergroup);
            $this->ajaxReturn($Result);
        }
    }
    /*
     * 当前接口的主要作用是用户还款入口
     * 三个值：
     * $Uid         用户ID
     * $Token       用户token
     * $BillNum     账单编号
     * $Stagesid    分期ID
     * */
    public function StageConsumption(){
        $Uid        = I("request.uid");
        $Token      = I("request.token");
        $BillNum    = I("request.num");
        $Stagesid   = I("request.iid");
        if($Uid=="" | $Token=="" | $BillNum=="" | $Stagesid=="" ){
            $result = COMMITERROR();
            $this->ajaxReturn($result);
        }
        if(!is_numeric($Uid) | !is_numeric($Token) | !is_numeric($Stagesid)){
            $result = COMMITERROR();
            $this->ajaxReturn($result);
        }else{
            $BillInstallmentsRelationsModel = D("BillInstallmentsRelations");
            $Result = $BillInstallmentsRelationsModel->UserWantStaging($Uid,$Token,$BillNum,$Stagesid);
            $this->ajaxReturn($Result);
        }
    }
    /*
     *
     * 选择期数
     * $Uid     用户ID
     * $Token   用户本站唯一标识
     * */
    public function SelectionPeriod(){
        $Uid     = I("request.uid");
        $Token   = I("request.token");
        if(!is_numeric($Uid) | !is_numeric($Token) | $Uid=="" | $Token==""){
            $result = COMMITERROR();
            $this->ajaxReturn($result);
        }else{
            $UserDataModel = D("Installment");
            $Result = $UserDataModel->SelInstallMent($Uid,$Token);
            $this->ajaxReturn($Result);
        }
    }

    /*
     * 用户查询账单详情
     * $Uid     用户ID
     * $Token   用户本站唯一标识
     * $BillNum 用户账单编号
     * */
    public function BillingDetails(){
        $Uid     = I("request.uid");
        $Token   = I("request.token");
        $BillNum = I("request.billnum");
        if(!is_numeric($Uid) | !is_numeric($BillNum) | !is_numeric($Token) | $Uid=="" | $Token=="" | $BillNum==""){
            $result = COMMITERROR();
            $this->ajaxReturn($result);
        }else{
            $UserDataModel = D("UserBill");
            $Result = $UserDataModel->BillingDetails($Uid,$Token,$BillNum);
            $this->ajaxReturn($Result);
        }
    }

    /*
     * 查询用户的所有订单
     * $uid     用户ID
     */
    public function SelUserOrder(){
        $uid = I("request.uid",'');
        $token = I("request.token",'');
        $where = I("request.uid");
        if($uid == "" | $token == ""){
            $result = COMMITERROR();
            $this->ajaxReturn($result);
        }else{
            
        }
    }
}