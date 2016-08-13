<?php
namespace Common\Model;
use Common\Model\CommonModel;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/25
 * Time: 15:28
 * 当前model类内所有的方法需先进行用户准确性验证（关联方法除外）
 */
class InstallmentModel extends CommonModel
{
    /*
     * 查询可分期方案
     * $uid     用户ID
     * $token   用户唯一标示
     * $usergroup   用户分组编号
     * */
    public function SelInstallMent ($Uid, $Token,$usergroup){
        $UserResult = $this->UserToken ($Uid, $Token);
        if ($UserResult['resultnum'] != 0) {
            return USERNOTREGISTERED();
            exit;
        } else {
            $InstallmentUsergroupTable = M("installment_usergroup");
            $UserWantFenQi = $InstallmentUsergroupTable->where("usergroupid = '$usergroup' and isusess = 1")->field("usergroupid,isinstallment,installment,isusess")->find();
            if($UserWantFenQi['isinstallment']=="0"){
                return DONTSTAGE();
                exit;
            }else{
                $InstallName = $UserWantFenQi['installment'];
                $InstallmentTable = M("installment");
                $UserInstall = $InstallmentTable->where("periodsType='$InstallName'")->field("periodsType,rate,id,periods")->order("periods")->select();
                if($UserInstall==""){
                    return NOTFOUND();
                    exit;
                }else{
                    return SONEOK($UserInstall);exit;
                }
            }
        }
    }
}