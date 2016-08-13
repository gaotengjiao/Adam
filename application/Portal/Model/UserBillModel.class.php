<?php
namespace Common\Model;
use Common\Model\CommonModel;
class UserBillModel extends CommonModel {
    /*
     * 当前model类内所有的方法需先进行用户准确性验证（关联方法除外）
     * */
    
    /*
     * 消费分期查询当前用户的全部账单
     * $uid     用户ID
     * $token   用户唯一标示
     * */
    public function ConsumptionStage($Uid,$Token){
        $UserResult = $this->UserToken($Uid, $Token);
        if ($UserResult['resultnum'] != 0) {
            $result['resultnum'] = "119";
            $result['result_mess'] = "用户未注册";
            $result['result'] = "";
            return $result;
            exit;
        }else {
            $UserBillTable = M("user_bill");
            $UserBillResult = $UserBillTable->where("userid = $Uid and isstallments=0")->field("id,userId,billcontent,billAmount,billDate,billNum,ishavebill")->select();
            if(empty($UserBillResult)){
                $result['resultnum'] = "119";
                $result['result_mess'] = "用户未注册";
                $result['result'] = "";
                return $result;
                exit;
            }else{
                $result['resultnum'] = "0";
                $result['result_mess'] = "成功";
                $result['result'] = $UserBillResult;
                return $result;
                exit;
            }
        }
    }

    /*
     * 查询账单详情
     * $uid     用户ID
     * $token   用户唯一标示
     * $BillNum 用户账单编号
     * */
    public function BillingDetails($Uid,$Token,$BillNum){
        $UserResult = $this->UserToken($Uid, $Token);
        if ($UserResult['resultnum'] != 0) {
            $result['resultnum'] = "119";
            $result['result_mess'] = "用户未注册";
            $result['result'] = "";
            return $result;
            exit;
        }else {
            $UserBillTable = M("user_bill");
            $BillInstallmentsRelationsTable = M("bill_installments_relations");
            $InstallmentTable = M("installment");
            $InstallmentsRepaymentRelationTable = M("installments_repayment_relation");
            $bill = $UserBillTable->where("id = $BillNum")->field("id,userId,billcontent,billAmount,isstallments,billDate,billStatus,billNum")->find();
            $billid = $bill["id"];
            $StagingResult = $BillInstallmentsRelationsTable->where("billid = $billid")->field("id,installmentsNum")->find();
            $stagingid = $StagingResult['installmentsnum'];
            $BillInstallid = $StagingResult['id'];
            $InstallmentResult = $InstallmentTable->where("id = $stagingid")->field("periods,rate,periodsType")->find();
            $Rate = $InstallmentResult['periods'];
            $InstallResult = $InstallmentsRepaymentRelationTable->where("installmentsId = $BillInstallid")->field("id,installmentsId,repaymentCount,repaymentDate,repaymentNum,ispay,isoverdue")->limit($Rate)->select();
            $payCount = $InstallmentsRepaymentRelationTable->where("installmentsId = $BillInstallid and ispay=1")->field("count(id) as countInstall")->limit($Rate)->select();
            $NewArr = $this->ArraySet($bill,$StagingResult,$InstallmentResult,$InstallResult,$payCount);
            if($NewArr==""){
                $result['resultnum'] = "1";
                $result['result_mess'] = "失败";
                $result['result'] = "";
                return $result;exit;
            }else{
                $result['resultnum'] = "0";
                $result['result_mess'] = "成功";
                $result['result'] = $NewArr;
                return $result;exit;
            }
        }
    }
    /*
     * 获取集合数组
     * */
    public function ArraySet($bill,$StagingResult,$InstallmentResult,$InstallResult,$payCount){
        $NewResult = array();
        $NewResult['projectname'] = $bill['billcontent'];
        $NewResult['billnum'] = $bill['billnum'];
        $NewResult['billdate'] = $bill['billdate'];
        $NewResult['installmentmoney'] = $bill['billamount'] / $InstallmentResult['periods'];
        $NewResult['number'] = $InstallmentResult['periods'];
        $NewResult['servicecharge'] = $bill['billamount'] * ($InstallmentResult['rate'] / 100) / 3;
        $NewResult['paycount'] = $payCount[0]['countinstall'];
        $NewResult['paymoney'] = ($bill['billamount'] / $InstallmentResult['periods'] + $bill['billamount'] * ($InstallmentResult['rate'] / 100) / 3) * $payCount[0]['countinstall'];
        $YuMoney = $this->SelUserIsoverdue($InstallResult,$NewResult);
        $NewResult['fenbill'] = $YuMoney;
        $dontmony = $this->Allmoney($NewResult['fenbill']);
        $NewResult['dontmony'] = $dontmony;
        $arr = $this->FillVirtualData($NewResult,$NewResult['number'],$NewResult['fenbill']);
        return $arr;exit;
    }
    /*
     * 填充虚拟数据
     * */
    public function FillVirtualData($NewResult,$number,$fen){
        $fencount = count($fen);
        for($i=$fencount;$i<$number;$i++){
            $m = $i - 1;
            $Year = date("Y", strtotime($fen[$i-1]['repaymentdate']));
            $month = date("m", strtotime(" +$m month"));
            $day = date("d", strtotime($fen[$i-1]['repaymentdate']));
            $NewResult['fenbill'][$i]['id'] =  $fen[$i-1]['id'];
            $NewResult['fenbill'][$i]['installmentsid'] =  $fen[$i-1]['installmentsid'];
            $NewResult['fenbill'][$i]['repaymentcount'] =  $fen[$i-1]['repaymentcount'];
            $NewResult['fenbill'][$i]['repaymentdate'] =  $Year."-".$month."-".$day;
            $NewResult['fenbill'][$i]['repaymentnum'] =  1;
            $NewResult['fenbill'][$i]['ispay'] =  "未还";
            $NewResult['fenbill'][$i]['isoverdue'] =  1;
            $NewResult['fenbill'][$i]['isoverdue'] =  1;
            $NewResult['fenbill'][$i]['currentpayment'] =  1;
            $starttime = date("Y-n-j");
            $endtime = date("Y-n-j",strtotime( $NewResult['fenbill'][$i]['repaymentdate']));
            $NewResult['fenbill'][$i]['daysremaining'] = $this->diffBetweenTwoDays($starttime,$endtime);
            $NewResult['fenbill'][$i]['moneyy'] =  1;
            $NewResult['fenbill'][$i]['money'] =  $NewResult['paymoney'];
        }
        return $NewResult;exit;
    }
    /*
     * 计算全部未还金额包括逾期账单
     * */
    public function Allmoney($result){
        $UserDetallCount = count($result);
        $NewResult["allmoney"] = 0;
        for($j=0;$j<$UserDetallCount;$j++){
            $NewResult["allmoney"] += $result[$j]['money'];
        }
        $money = $NewResult["allmoney"];
        return $money;die;
    }
    /*
    * 查询用户是否已经逾期
    * */
    public function SelUserIsoverdue($InstallResult,$NewResult){
        $UserInterestTable = M("user_interest");
        $UserDetallCount = count($InstallResult);
        for($i=0;$i<$UserDetallCount;$i++) {
            if($InstallResult[$i]['ispay']=="1"){
                $InstallResult[$i]['ispay']="已还";
            }else{
                if($InstallResult[$i]['isoverdue']=="1"){
                    $InstallResult[$i]['ispay']="已逾期";
                }else{
                    $InstallResult[$i]['ispay']="未还清";
                }
            }
            $starttime = date("Y-n-j");
            $endtime = date("Y-n-j",strtotime($InstallResult[$i]['repaymentdate']));
            $time    = date("n",strtotime($InstallResult[$i]['repaymentdate']));
            $date = date("n");
            if($time==$date){
                $InstallResult[$i]['currentpayment']=1;
            }else{
                $InstallResult[$i]['currentpayment']=0;
            }
            $InstallResult[$i]['daysremaining'] = $this->diffBetweenTwoDays($starttime,$endtime);
            if($InstallResult[$i]['isoverdue']==1){
                $billnum = $InstallResult[$i]['repaymentnum'];
                $money = $UserInterestTable->where("overdueid = '$billnum'")->field('sum(money) as money')->select();
                $InstallResult[$i]['moneyy'] = sprintf("%.2f", $money[0]["money"]);
            }else{
                $InstallResult[$i]['moneyy'] = 0;
            }
            $InstallResult[$i]['money'] = $InstallResult[$i]['moneyy'] + $NewResult['paymoney'];
        }
        return $InstallResult;exit;
    }
    /*
      * 求两个日期之间的天数
      * */
    function diffBetweenTwoDays ($starttime,$endtime)
    {
        $second1 = strtotime($starttime);
        $second2 = strtotime($endtime);
        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ($second1 - $second2) / 86400;
    }
}