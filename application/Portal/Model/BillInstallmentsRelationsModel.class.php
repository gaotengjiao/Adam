<?php
namespace Common\Model;
use Common\Model\CommonModel;
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/7/25
 * Time: 15:28
 * 当前Model用户分期
 */
class BillInstallmentsRelationsModel extends CommonModel
{
   /*
    * 当前类是对用户分期进行操作
    * 1、验证用户的真实性、准确性（当前账单是否属于但前用户）和权限（是否可以使用分期付款）
    * 2、查证无误之后对用户的账单记性分期操作将分期信息入库保存
    * 3、用户分期成功之后对分期业务相关的信息进行修改
    * 4、返回数据通知用户是否分期成功
    * */
    /*
     * 验证用户的信息
     * 验证用户的真实性统一使用公共model的UserToken方法进行验证
     * */
    public function UserWantStaging($Uid,$Token,$BillNum,$Stagesid){
        $UserResult = $this->UserToken($Uid, $Token);
        if ($UserResult['resultnum'] != 0) {
            return USERNOTREGISTERED();
            exit;
        }
        $BillAccuracyResult = $this->BillAccuracy($Uid,$BillNum);
        if ($BillAccuracyResult['resultnum'] != 0) {
            return SONEBILLERROR();exit;
        }
        $UserRightResult = $this->UserRights($Uid);
        if($UserRightResult['resultnum'] != 0){
            return DONTSTAGE();exit;
        }else{
            $Result = $this->BillingInstallments($Uid,$BillNum,$Stagesid);
            return $Result;exit;
        }
    }

     /*
     * 对用户的分期账单进行入库操作
     * */
    public function BillingInstallments($Uid,$BillNum,$Stagesid){
        $BillInstallmentsRelationsTable = M("bill_installments_relations");
        $UserBillTable = M("user_bill");
        $BillInstallmentsRelationsTable->startTrans();//开启事务
        $BillNum = explode(',',$BillNum);
        $BillNumCount = count($BillNum);
        for($i=0;$i<$BillNumCount;$i++){
            $billid = $UserBillTable->where("billNum = '$BillNum[$i]'")->field("id")->find();
            $BillInstallmentsRelations[$i]['billId'] = $billid['id'];
            $BillInstallmentsRelations[$i]['installmentsNum'] = $Stagesid;
            $BillInstallmentsRelations[$i]['installmentsDate'] = date("Y-m-d H:i:s");
        }
        if($BillInstallmentsRelationsTable->addAll($BillInstallmentsRelations)){
            $SaveBillResult = $this->SaveBillStatus($BillNum,$BillNumCount);
            if($SaveBillResult=="1"){
                echo 1;exit;
            }else{
                echo 2;exit;
            }
            $BillInstallmentsRelationsTable->commit();//提交事务成功
            return SONEOK();exit;
        }else{
            $BillInstallmentsRelationsTable->rollback();//事务有错回滚
            return SONEERROR();exit;
        }
    }

    /*
     * 修改账单状态
     * */
    public function SaveBillStatus($BillNum,$BillNumCount){
        $BillNum = implode(",",$BillNum);
        $UserBillTable = M("user_bill");
        $BillInstallmentsRelations['isstallments'] = 1;
        $BillInstallmentsRelations['ishavebill'] = 1;
        if($UserBillTable->where("billNum in ($BillNum)")->save($BillInstallmentsRelations)){
            return 1;exit;
        }else{
            return 0;exit;
        }
    }
}