<?php
$linkID=@mysql_connect("rm-2ze921c76782e3772o.mysql.rds.aliyuncs.com","simeiliyan","sone123456");
if(!$linkID){
	echo "没连接上";
}

$ss=@mysql_select_db("simeiliyan");
mysql_query("SET NAMES 'UTF-8'");
if(!$ss){
	echo "没找到数据库";
}
$openid		= $_POST['openid'];
$headimgurl	= $_POST['headimgurl'];
$uid				= $_POST['uid'];
$order				= $_POST['order'];
$billid				= $_POST['billid'];
$PayAllCount		= $_POST['PayAllCount'];
$repaymentCount		= $_POST['repaymentCount'];
$type				= $_POST['type'];
$Paytime			= date("Y-m-d H:i:s");
$time				= time();
/*
 * 添加用户付款信息避免后期订单出现问题无法查证
 * */
$s_user_payment_details = mysql_query("INSERT INTO s_user_payment_details (userId,payNum,payType,payment,payDatetime,isSuccess) VALUES ('$uid','$order','$type','Vx','$Paytime','1')");
$s_user_payment_details = mysql_fetch_assoc($s_user_payment_details);
if($installments_repayment_relation<1){
	echo "0";
}
/*
 *修改逾期账单支付状态
 * */
$s_late_fees_bill = mysql_query("update s_late_fees_bill set lateFeesStatus = '1' where lateFeesNum = '$order'");
$s_late_fees_bill = mysql_fetch_assoc($s_late_fees_bill);
if($s_late_fees_bill<1){
	echo "0";
}
/*
 * 修改分期状态
 * */
$s_installments_repayment_relation = mysql_query("update s_installments_repayment_relation set ispay = '1' where repaymentNum = '$order'");
$s_installments_repayment_relation = mysql_fetch_assoc($s_installments_repayment_relation);
if($s_installments_repayment_relation<1){
	echo "0";
}
/*
 * 判断当前账单是否已经全部还完还完测修改账单的状态
 * */
if($PayAllCount == $repaymentCount){
	$s_user_bill = mysql_query("update s_user_bill set billStatus = '0' where id = '$billid'");
	$s_user_bill = mysql_fetch_assoc($s_user_bill);
	if($s_user_bill == 1){
		echo "1";
	}else{
		echo "0";
	}
}else{
	echo "1";
}
?>