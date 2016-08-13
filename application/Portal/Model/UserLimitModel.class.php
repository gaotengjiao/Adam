<?php
namespace Common\Model;
use Common\Model\CommonModel;
class UserLimitModel extends CommonModel {
    /*
     * 查询用户额度
     * */
    public function SelUserLimit($Uid,$Token)
    {
        $UserResult = $this->UserToken($Uid, $Token);
        if ($UserResult['resultnum'] != 0) {
            $result['resultnum'] = "119";
            $result['result_mess'] = "用户未注册";
            $result['result'] = "";
            return $result;
            exit;
        }else{
            $UserLimitTable = M("user_limit");
            $UserBillTable  = M("user_bill");
            $UserLimit = $UserLimitTable->where("uid = $Uid and status = 1")->field("limit,ramining,uid")->find();
            $UserBill  = $UserBillTable->where("userId = '$Uid' and  date_sub(curdate(), INTERVAL 7 DAY) <= date(`RepaymentDate`)")->find();
            if(empty($UserBill)){
                $UserLimit['ishavebill'] = 1;
            }else{
                $UserLimit['ishavebill'] = 0;
                $UserLimit['billamount'] = $UserBill['billamount'];
                $UserLimit['billnum'] = $UserBill['billnum'];
            }
            if($UserLimit==""){
                $result['resultnum'] = "404";
                $result['result_mess'] = "用户还未申请颜值额度";
                $result['result'] = "";
                return $result;
                exit;
            }else{
                $result['resultnum'] = "0";
                $result['result_mess'] = "ok";
                $result['result'] = $UserLimit;
                return $result;
                exit;
            }
        }
    }

    /*
     * 申请用户额度
     * */
    public function ApplicationUserQuota(){

    }

    /*
     * 查询用户额度明细
     * $uid     用户唯一ID
     * $token   用户唯一签名
     * */
    public function SelUserLimitDetail($Uid,$Token,$allnum,$size,$type){
        $UserResult = $this->UserToken($Uid, $Token);
        if ($UserResult['resultnum'] != 0) {
            $result['resultnum'] = "119";
            $result['result_mess'] = "用户未注册";
            $result['result'] = "";
            return $result;
            exit;
        }else{
            $money = $this->UserLimit($Uid);
            $data = $this->SelUserConsumptionDetail($Uid,$allnum,$size,$type);
            if($money=="" | $data==""){
                $resultAll['money'] = $money;
                $result['resultnum'] = "404";
                $result['result_mess'] = "未查询到数据";
                $result['result'] = $resultAll;
                return $result;
                exit;
            }else{
                if($type==1){
                    $resultAll['money'] = $money;
                    $resultAll['data'] = $data;
                    $result['resultnum'] = "0";
                    $result['result_mess'] = "成功";
                    $result['result'] = $resultAll;
                    return $result;
                    exit;
                }else{
                    $result['resultnum'] = "0";
                    $result['result_mess'] = "成功";
                    $result['result'] = $data;
                    return $result;
                    exit;
                }
            }
        }
    }
    /*
     * 查询用户额度和余额
     * */
    public function UserLimit($Uid){
        $UserLimitTable = M("user_limit");
        $UserLimit = $UserLimitTable->where("uid = $Uid and status = 1")->field("limit,ramining,uid")->find();
        return $UserLimit;exit;
    }
    /*
     * 查询颜值消费明细表
     * */
    public function SelUserConsumptionDetail($Uid,$allnum,$size,$type)
    {
        $ConsumptionDetallTable                 = M("consumption_detail");
        $UserDetall = $ConsumptionDetallTable->where("uid = $Uid")->limit($allnum, $size)->select();
        $Result = $this->BillInstallmentsRelations($UserDetall);
        $Result = $this->GetResust($Result,$type);
        return $Result;exit;
    }
    /*
     * 获取最终结果
     * */
    public function GetResust($Result,$type){
        $UserDetallCount = count($Result);
        for($i=0;$i<$UserDetallCount;$i++){
            $year = date("Y", strtotime($Result[$i]['userbill']['billdate']));
            $monut = date("n", strtotime($Result[$i]['userbill']['billdate']));
            $day = date("n-d", strtotime($Result[$i]['instarep']['repaymentdate']));
            $ResultArr[$i]['zname'] = "主动还款-颜值" . $year . "年" . $monut . "月账单";
            $ResultArr[$i]['billcontent'] = $Result[$i]['userbill']['billcontent'];
            $ResultArr[$i]['allmoney'] = $Result[$i]['userbill']['billamount'] + $Result[$i]['userbill']['billamount'] *$Result[$i]['install']['li'] ;
            $ResultArr[$i]['repaycount'] = $Result[$i]['install']['periods'];
            $ResultArr[$i]['paycount'] = $Result[$i]['instarep']['payok'];
            $ResultArr[$i]['payment'] = $Result[$i]['install']['periods'] -  $Result[$i]['instarep']['payok'];
            $ResultArr[$i]['billid'] = $Result[$i]['bill']['billid'];
            $ResultArr[$i]['time'] = $day;
            $ResultArr[$i]['billnum'] = $Result[$i]['instarep']['repaymentnum'];
            $ResultArr[$i]['creatime'] = $year."-".$monut;
            $ResultArr[$i]['creatimeyear'] = $year;
            $ResultArr[$i]['should'] =$Result[$i]['money']['should'];
            $ResultArr[$i]['wait'] = $Result[$i]['money']['wait'];
            $ResultArr[$i]['isoverdue'] = $Result[$i]['instarep']['isoverdue'];
        }
        $Result = $this->SelUserIsoverdue($ResultArr);
        if($type==1){
            $ResultArr = $this->AuditPayments($Result);
            $ResultArr = $this->GetFinalResult($ResultArr);
            return $ResultArr;exit;
        }else{
            $ResultArr = $this->AuditPayments($Result);
            $Result = $this->AuditPaymentsResult($ResultArr);
            $result['money'] = $Result;
            $result['data'] = $ResultArr;
            return $result;die;
        }
    }
    /*
     * 最终结果
     * */
    public function AuditPaymentsResult($ResultArr){
        $UserDetallCount = count($ResultArr);
        for($i=0;$i<$UserDetallCount;$i++) {
           if($ResultArr[$i]['status']==1){
                $Result[] = $ResultArr[$i]['should'];
           }
               $Allmoney[] = $ResultArr[$i]['wait'];
        }
        $result['sevenmoney'] = array_sum($Result);
        $result['allmoney'] = array_sum($Allmoney);
        //$Result['allmony'] = array_sum($Result);
        return $result;exit;;
    }
    /*
        * 查账还款
        * */
    public function AuditPayments($Result){
        $UserDetallCount = count($Result);
        $data = date('n-j', strtotime('+1 week'));
        $starttime = date("Y-n-j");
        for($i=0;$i<$UserDetallCount;$i++) {
            $endtime = $Result[$i]['creatimeyear']."-".$Result[$i]['time'];
            $Result[$i]['daysremaining'] = $this->diffBetweenTwoDays($starttime,$endtime);
            if($Result[$i]['daysremaining']>7){
                $Result[$i]['status'] = 0;
            }elseif($Result[$i]['daysremaining']<=7){
                $Result[$i]['status'] = 1;
            }else{
                $Result[$i]['status'] = 2;
            }
            if($Result[$i]['isoverdue']==1){
                $Result[$i]['isoverdue'] = "已逾期";
            }elseif($Result[$i]['repaycount'] ==  $Result[$i]['paycount']){
                $Result[$i]['isoverdue'] = "已还清";
            }else{
                $Result[$i]['isoverdue'] = "未还清";
            }
        }
        return $Result;exit;;
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
    /*
  * 查询用户是否已经逾期
  * */
    public function SelUserIsoverdue($ResultArr){
        $UserInterestTable = M("user_interest");
        $UserDetallCount = count($ResultArr);
        for($i=0;$i<$UserDetallCount;$i++) {
            if($ResultArr[$i]['isoverdue']==1){
                $billnum = $ResultArr[$i]['billnum'];
                $money = $UserInterestTable->where("overdueid = '$billnum'")->field('money')->find();
                $ResultArr[$i]['should'] = $ResultArr[$i]['should']+$money['money'];
                $ResultArr[$i]['wait'] = $ResultArr[$i]['wait']+$money['money'];
                $ResultArr[$i]['allmoney'] = $ResultArr[$i]['allmoney']+$money['money'];
            }
        }
        return $ResultArr;exit;
    }
    /*
     * 获取最终结果
     * */
    public function GetFinalResult($Result){
        $UserDetallCount = count($Result);
        for($i=0;$i<$UserDetallCount;$i++){
           if($Result[$i]['creatime'] = $Result[$i]['creatime']){
               $Results[$Result[$i]['creatime']][$i] = $Result[$i];
           }
        }
        return $Results;die;
    }
    /*
     * 查询用户账单分期
     * */
    public function BillInstallmentsRelations($UserDetall){
        $BillInstallmentsRelationsTable     = M("bill_installments_relations");
        $UserDetallCount = count($UserDetall);
        for($i=0;$i<$UserDetallCount;$i++){
            $bid    =   $UserDetall[$i]['billid'];
            $Result[$i]['billin'] = $BillInstallmentsRelationsTable->where("billid = $bid")->field("id,installmentsNum")->find();
            $Result[$i]['bill'] = $UserDetall[$i];
        }
        $Result = $this->Installment($Result);
        return $Result;exit;
    }

    /*
     * 查询用户分期期数和利率
     * */
    public function Installment($Result){
        $InstallmentTable                       = M("installment");
        $UserDetallCount = count($Result);
        for($i=0;$i<$UserDetallCount;$i++){
            $installmentsnum    =   $Result[$i]['billin']['installmentsnum'];
            $Result[$i]['install'] = $InstallmentTable->where("id = $installmentsnum")->field("periods,rate")->find();
        }
        $Result = $this->InstallmentsRepaymentRelation($Result);
        return $Result;exit;
    }
    /*
     * 查询用户已分期数
     * */
    public function InstallmentsRepaymentRelation($Result){
        $InstallmentsRepaymentRelationTable      = M("installments_repayment_relation");
        $UserDetallCount = count($Result);
        for($i=0;$i<$UserDetallCount;$i++){
            $bid    =   $Result[$i]['billin']['id'];
            $Result[$i]['instarep'] = $InstallmentsRepaymentRelationTable->where("installmentsId = $bid and ispay = 0")->field("id,repaymentCount,repaymentDate,repaymentNum,isoverdue")->find();
            $payok = $InstallmentsRepaymentRelationTable->where("installmentsId = $bid and ispay = 1")->field("count(id) as payok,ispay")->select();
            $Result[$i]['instarep']['payok'] = $payok[0]['payok'];
        }
        $Result = $this->UserBill($Result);
        return $Result;exit;
    }
    /*
     * 查询用户账单
     * */
    public function UserBill($Result){
        $UserBillTable  = M("user_bill");
        $UserDetallCount = count($Result);
        for($i=0;$i<$UserDetallCount;$i++){
            $bid    =   $Result[$i]['bill']['billid'];
            $Result[$i]['userbill'] = $UserBillTable->where("id = $bid")->field("userId,billAmount,billDate,billNum,billcontent")->find();
        }
        $Result = $this->IsStallMents($Result);
        return $Result;exit;
    }
    /*
     * 计算利率
     * */
    public function IsStallMents($Result){
        $UserDetallCount = count($Result);
        for($f=0;$f<$UserDetallCount;$f++){
            $li = $Result[$f]['install']['rate'];

            $li = (float)$li/100;
            $Result[$f]['install']['li'] = $li;
            $allmoney = $Result[$f]['userbill']['billamount'];
            $allCount = $Result[$f]['install']['periods'];
            $payok    = $Result[$f]['instarep']['payok'];
            if($payok==0){
                $should = ($allmoney + $allmoney*$li) / $allCount;
                $Result[$f]['money']['should'] = $should;
                $Result[$f]['money']['wait'] = $allmoney + $allmoney*$li;
            }else{
                $should = ($allmoney + $allmoney*$li) / $allCount;
                $Result[$f]['money']['should'] = $should;
                $Result[$f]['money']['wait'] = ($allmoney + $allmoney*$li) - $should * $payok;
            }
        }
       return $Result;exit;
    }

}