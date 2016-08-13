<?php

/**
 * JS_API支付demo
 * ====================================================
 * 在微信浏览器里面打开H5网页中执行JS调起支付。接口输入输出数据格式为JSON。
 * 成功调起支付需要三个步骤：
 * 步骤1：网页授权获取用户openid
 * 步骤2：使用统一支付接口，获取prepay_id
 * 步骤3：使用jsapi调起支付
*/

	include_once("../WxPayPubHelper/WxPayPubHelper.php");

	//使用jsapi接口
	$jsApi = new JsApi_pub();
	//=========步骤1：网页授权获取用户openid============
	//通过code获得openid
//	if (!isset($_GET['code']))
//	{
//		$uid 	= $_GET['uid'];
//		$token 	= $_GET['token'];
//		$order 	= $_GET['order'];
//		$typeUrl 	= $_GET['typeurl'];
//		//触发微信返回code码
//		$url = $jsApi->createOauthUrlForCode(WxPayConf_pub::JS_API_CALL_URL);
//		Header("Location: $url&uid=$uid&token=$token&order=$order&typeurl=$typeUrl");
//	}else
//	{
//		//获取code码，以获取openid
//	    $code = $_GET['code'];
//		$jsApi->setCode($code);
//		$openid = $jsApi->getOpenId();
//		$nickname = $jsApi->getNickname();
//		$headimgurl = $jsApi->getHeadimgurl();
//
//	}
//
	
	$linkID=@mysql_connect("rm-2ze921c76782e3772o.mysql.rds.aliyuncs.com","simeiliyan","sone123456");
		if(!$linkID){
			echo "没连接上";
			}

		$ss=@mysql_select_db("simeiliyan");
		mysql_query("SET NAMES 'UTF-8'");
		if(!$ss){
			echo "没找到数据库";
			}
		/* $appid="wxaf6078a557ceeeec";
		$secret="4290895a40612599370221ab71995c91";

		$json = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxaf6078a557ceeeec&secret=4290895a40612599370221ab71995c91&code=".$_GET['code']."&grant_type=authorization_code");
		$arr = json_decode($json,true);
		$refresh_token=$arr['refresh_token'];
		$json = file_get_contents("https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=wxaf6078a557ceeeec&grant_type=refresh_token&refresh_token=".$refresh_token);
		$arr = json_decode($json,true);
		$token = $arr['access_token'];
		$openid = $arr['openid'];
		//拿到token后就可以获取用户基本信息了
		$json = file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=".$token."&openid=".$openid."&lang=zh_CN");//获取微信用户基本信息 ，'字符串'
		$arr = json_decode($json,true);  //把json解析成数组
				 */
		$data = $jsApi->GetUserOrderInfo();
		if($data['type']==1){

		}else {
			$uid 	= $_GET['uid'];
			$token 	= $_GET['token'];
			$order 	= $_GET['order'];
			$typeUrl 	= $_GET['typeurl'];
			//查询用户
			$UserMess = mysql_query("select uid,token from s_user where uid = $uid and token = $token");
			$UserMess = mysql_fetch_assoc($UserMess);
			if($UserMess==""){
				return $jsApi->SimeiError();exit;
			}
			/*
			 *查询分期账单
			 * */
			$installments_repayment_relation = mysql_query("select installmentsId,repaymentCount,ispay,isoverdue from s_installments_repayment_relation where repaymentNum = '$order' and ispay = 0");
			$installments_repayment_relation = mysql_fetch_assoc($installments_repayment_relation);
			if($installments_repayment_relation==""){
				return $jsApi->SimeiError();exit;
			}
			/*
			 *查询账单分期关系
			 * */
			$BillInstallmentID = $installments_repayment_relation['installmentsId'];
			$s_bill_installments_relations = mysql_query("select billId,installmentsNum from s_bill_installments_relations where id = '$BillInstallmentID'");
			$s_bill_installments_relations = mysql_fetch_assoc($s_bill_installments_relations);
			if($s_bill_installments_relations==""){
				return $jsApi->SimeiError();exit;
			}
			/*
			 * 查询账单
			 * */
			$Billid = $s_bill_installments_relations['billId'];
			$user_bill = mysql_query("select billAmount from s_user_bill where id = '$Billid'");
			$user_bill = mysql_fetch_assoc($user_bill);
			if($user_bill==""){
				return $jsApi->SimeiError();exit;
			}
			/*
			 * 查询分期方案
			 * */
			$s_installmentId = $s_bill_installments_relations['installmentsNum'];
			$s_installment = mysql_query("select periods,rate from s_installment where id = '$s_installmentId'");
			$s_installment = mysql_fetch_assoc($s_installment);
			if($s_installment==""){
				return $jsApi->SimeiError();exit;
			}
			/*
			 * 计算每期金额
			 * */
			//粘单总金额
			$AllMooney	= $user_bill['billAmount'];
			//分期期数
			$periods 	= $s_installment['periods'];
			//分期利率
			$rate 		= $s_installment['rate'];
			//分期利率除以百分比
			$rate		= (float)$rate/100;
			//echo $rate."<br>";
			//计算每期应还的钱（本金）
			$stillPrincipal = $AllMooney / $periods;
			//计算每期手续费
			$CounterFee =  + (($AllMooney / $periods) * $rate);
			//最终应还本金
			$FinalPrincipal = $CounterFee + $stillPrincipal;
			if($installments_repayment_relation['isoverdue']==1){
				$s_late_fees_bill = mysql_query("select lateFeesDate,lateFeesNum,id from s_late_fees_bill where lateFeesNum = '$order' and lateFeesStatus = 0");
				$s_late_fees_bill = mysql_fetch_assoc($s_late_fees_bill);
				if($s_late_fees_bill==""){
					return $jsApi->SimeiError();exit;
				}
				$overdueId = $s_late_fees_bill['id'];
				$s_late_fees_relation = mysql_query("select lateFeesDate,lateFeesNum,sum(lateFeesCount) as money from s_late_fees_relation where overdueId = '$overdueId'");
				$s_late_fees_relation = mysql_fetch_assoc($s_late_fees_relation);
				//计算最终金额（显示）
				$Finalstillmoney = $FinalPrincipal + $s_late_fees_relation['money'];
			}else{
				//计算最终金额（显示）
				$Finalstillmoney = $FinalPrincipal;
			}
			$repaymentCount = $installments_repayment_relation['repaymentCount'];
			//计算交由微信使用的支付金额
			$WxFinalstillMoney = $Finalstillmoney * 100;
			/*
			 *
			 * */
			//=========步骤2：使用统一支付接口，获取prepay_id============
			//使用统一支付接口
			$unifiedOrder = new UnifiedOrder_pub();

			//设置统一支付接口参数
			//设置必填参数
			//appid已填,商户无需重复填写
			//mch_id已填,商户无需重复填写
			//noncestr已填,商户无需重复填写
			//spbill_create_ip已填,商户无需重复填写
			//sign已填,商户无需重复填写
			$unifiedOrder->setParameter("openid", "$openid");//商品描述
			$unifiedOrder->setParameter("body", "向思美丽妍付款");//商品描述
			//自定义订单号，此处仅作举例
			$timeStamp = time();
			$out_trade_no = WxPayConf_pub::APPID . "$timeStamp";
			$unifiedOrder->setParameter("out_trade_no", "$out_trade_no");//商户订单号
			//$unifiedOrder->setParameter("total_fee", "$WxFinalstillMoney");//总金额199900
			$unifiedOrder->setParameter("total_fee", "1");//总金额199900
			$unifiedOrder->setParameter("notify_url", WxPayConf_pub::NOTIFY_URL);//通知地址
			$unifiedOrder->setParameter("trade_type", "JSAPI");//交易类型
			//非必填参数，商户可根据实际情况选填
			//$unifiedOrder->setParameter("sub_mch_id","XXXX");//子商户号
			//$unifiedOrder->setParameter("device_info","XXXX");//设备号
			//$unifiedOrder->setParameter("attach","XXXX");//附加数据
			//$unifiedOrder->setParameter("time_start","XXXX");//交易起始时间
			//$unifiedOrder->setParameter("time_expire","XXXX");//交易结束时间
			//$unifiedOrder->setParameter("goods_tag","XXXX");//商品标记
			//$unifiedOrder->setParameter("openid","XXXX");//用户标识
			//$unifiedOrder->setParameter("product_id","XXXX");//商品ID

			$prepay_id = $unifiedOrder->getPrepayId();
			//=========步骤3：使用jsapi调起支付============
			$jsApi->setPrepayId($prepay_id);

			$jsApiParameters = $jsApi->getParameters();
			$jsA = json_decode($jsApiParameters, true);

			?>
			<!DOCTYPE html>
			<html lang="cn" use-rem="640">
			<head>
				<title>确认支付</title>


				<meta charset="UTF-8">
				<meta name="viewport"
					  content="width=device-width,initial-scale=1,minimum-scale=1,maximum-scale=1,user-scalable=no"/>
				<meta content="telephone=no" name="format-detection"/>
				<link href="../../web/css/styleCss.css" rel="stylesheet"/>
				<link href="../../web/css/style.css" rel="stylesheet"/>
				<link rel="stylesheet" href="../../web/css/wx.css">
				<script src="../../web/js/zepto.js"></script>
				<script src="../../web/js/rem.js"></script>
				<script src="../../web/js/jquery-2.2.1.js"></script>
				<script src="../../web/js/touch.js"></script>
				<script src="../../web/js/cookie.js"></script>
				<script src="../../web/js/allJs.js"></script>
				<script src="../../web/js/jsTB.js"></script>
				<script src="../../web/js/move.js"></script>
				<script src="../../web/js/yuming.js"></script>
				<script src="../../web/js/bdtouch.js"></script>
				<link rel="stylesheet" type="text/css" href="../css/Payment.css"/>
				<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
				<script type="text/javascript">

					//调用微信JS api 支付
					function jsApiCall() {
						WeixinJSBridge.invoke(
							'getBrandWCPayRequest',
							<?php echo $jsApiParameters; ?>,
							function (res) {
								console.log(res)
								WeixinJSBridge.log(res.err_msg);
								//alert(res.err_code+res.err_desc+res.err_msg);
								//var ordersmark = "<?=$out_trade_no?>";

								var openid 			= "<?=$openid?>";
								var nickname 		= "<?=$nickname?>";
								var headimgurl 		= "<?=$headimgurl?>";
								var uid			 	= "<?=$uid?>";
								var order 			= "<?=$order?>";
								var billid 			= "<?=$Billid?>";
								var PayAllCount		= "<?=$periods?>";
								var repaymentCount	= "<?=$repaymentCount?>";
								var type			= "repayment";
								var Cts = res.err_msg;
								if (Cts.indexOf("ok") > 0 || Cts.indexOf("OK") > 0) {
									$.ajax({
										type: 'POST',
										url: "http://www.buruwo.com/wxpay/demo/crowdorders.php",
										data: {"openid": openid, "nickname": nickname, "headimgurl": headimgurl, "order":order, "uid":uid, "billid":billid, "PayAllCount":PayAllCount, "repaymentCount":repaymentCount, "type":type},
										dataType: "text",
										success: function (count) {

											if (count == "0001") {
												alert('支付成功');
												var urlType = "<?=$typeUrl?>";
												if(urlType==1){
													window.location.href = "http://www.buruwo.com/wxpay/staging.html";
												}else{
													window.location.href = "http://www.buruwo.com/wxpay/staging.html";
												}
											} else {
												alert('订单生成失败！');
											}
										}
									});
								}
							}
						);
					}

					function callpay() {
						if (typeof WeixinJSBridge == "undefined") {
							if (document.addEventListener) {
								document.addEventListener('WeixinJSBridgeReady', jsApiCall, true);
							} else if (document.attachEvent) {
								document.attachEvent('WeixinJSBridgeReady', jsApiCall);
								document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
							}
						} else {
							jsApiCall();
						}
					}

				</script>

			</head>
			<body>
			<div class="clear a">
				<h1>思美丽妍订单</h1>
<!--				<h2 id="num">￥--><?php //echo $Finalstillmoney;?><!--.00</h2>-->
				<h2 id="num">￥<?php echo $Finalstillmoney;?>.00</h2>
			</div>
			<div class="clear shou padleft padright"><span>收款方</span><span>思美丽妍</span></div>
			<div id="ok" onClick="callpay()">确认支付</div>
			</body>
			</html>
			<!--<div class="bfmjgh"><button type="button" class="btn btn-primary btn-lg btn-block" style="background:#fe7701; border-color:#fe7701; font-weight:bold;">立即支付</button></div>-->

			</body>

			<?php
		}
?>