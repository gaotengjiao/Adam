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
	if (!isset($_GET['code']))
	{
		//触发微信返回code码
		$url = $jsApi->createOauthUrlForCode(WxPayConf_pub::JS_API_CALL_URL);
		Header("Location: $url"); 
	}else
	{
		//获取code码，以获取openid
	    $code = $_GET['code'];
		$jsApi->setCode($code);
		$openid = $jsApi->getOpenId();
	}
	
	
	$linkID=@mysql_connect("rm-2ze921c76782e3772o.mysql.rds.aliyuncs.com","qiaohailin","qiao123@");
		if(!$linkID){
			echo "没连接上";
			}
	
		$ss=@mysql_select_db("a_step");
		mysql_query("SET NAMES 'GB2312'"); 
		if(!$ss){
			echo "没找到数据库";
			}
		$corwdid = $_GET['state'];
		$_SESSION['openid'] = $openid;
		
		$crowdfunding = mysql_fetch_array(mysql_query("select * from phome_ecms_movie where id='".$corwdid."'")); //页面显示内容
		
		$crowdorders = mysql_fetch_array(mysql_query("select count(*) from phome_ecms_crowdorders where corwdid='".$corwdid."'")); //众筹人数
		
		$message = mysql_fetch_array(mysql_query("select * from weixin.wechat_message where openid='".$_SESSION['openid']."'"));	//查询username
		
		$enewsmember = mysql_fetch_array(mysql_query("select * from phome_enewsmember where username='".$message['uname']."'"));	//查询userid
		
		$fasercount = mysql_fetch_array(mysql_query("select count(*) from phome_ecms_crowdorders where corwdid='".$corwdid."' and userid=".$enewsmember['userid']));
		
		if($fasercount[0]>0){
			echo "<script>window.location.href='http://b.autostep.com.cn/qiaohailin/H5_zc_xin/index.php/Home/Raiselist/zc_yesok';</script>";
		}else{
		}
		
		$myenerwa=$crowdfunding['moviefen']*100;
	
	
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
	$unifiedOrder->setParameter("openid","$openid");//商品描述
	$unifiedOrder->setParameter("body","众筹课程");//商品描述
	//自定义订单号，此处仅作举例
	$timeStamp = time();
	$out_trade_no = WxPayConf_pub::APPID."$timeStamp";
	$unifiedOrder->setParameter("out_trade_no","$out_trade_no");//商户订单号 
	$unifiedOrder->setParameter("total_fee","$myenerwa");//总金额
	$unifiedOrder->setParameter("notify_url",WxPayConf_pub::NOTIFY_URL);//通知地址 
	$unifiedOrder->setParameter("trade_type","JSAPI");//交易类型
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
	require_once "jssdk.inc.php";
//$end = urlencode('from=singlemessage&isappinstalled=0');

$weixin = new jssdk($url='http://www.buruwo.com/wxpay/demo/js_api_call_xin.php?code='.$code.'&state='.$corwdid);//借用传入0或1,url是地址默认不借用,当前地址
$wx = $weixin->get_sign();
	
?>

<!doctype html>
<!--[if lt IE 7]> <html class="ie6 oldie"> <![endif]-->
<!--[if IE 7]>    <html class="ie7 oldie"> <![endif]-->
<!--[if IE 8]>    <html class="ie8 oldie"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>优一步--众筹</title>
<link href="__CSS__/boilerplate.css" rel="stylesheet" type="text/css">
<link href="__CSS__/index.css" rel="stylesheet" type="text/css">
<!-- 
要详细了解文件顶部 html 标签周围的条件注释:
paulirish.com/2008/conditional-stylesheets-vs-css-hacks-answer-neither/

如果您使用的是 modernizr (http://www.modernizr.com/) 的自定义内部版本，请执行以下操作:
* 在此处将链接插入 js 
* 将下方链接移至 html5shiv
* 将“no-js”类添加到顶部的 html 标签
* 如果 modernizr 内部版本中包含 MQ Polyfill，您也可以将链接移至 respond.min.js 
-->
<!--[if lt IE 9]>
<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]--><script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script>
<script src="jquery.min.js"></script>
<script type="text/javascript">

		//调用微信JS api 支付
		function jsApiCall(){
			WeixinJSBridge.invoke(
				'getBrandWCPayRequest',
				<?php echo $jsApiParameters; ?>,
				function(res){
					//WeixinJSBridge.log(res.err_msg);
					//alert(res.err_code+res.err_desc+res.err_msg);
					var ordersmark = "<?=$out_trade_no?>";
					var corwdid = "<?=$corwdid?>";
					var userid = "<?=$enewsmember['userid']?>";
					var moneyub = "<?=$crowdfunding['moneyub']?>";
								
					var Cts = res.err_msg;
					if(Cts.indexOf("ok")>0 || Cts.indexOf("OK")>0){
						$(function(){
							$.ajax({
								type: 'POST',
								url: "http://www.buruwo.com/wxpay/demo/crowdorders.php",
								data:{"ordersmark":ordersmark,"corwdid":corwdid,"userid":userid,"moneyub":moneyub},
								dataType: "text",
								success: function (count) {
									if(count == "0"){
										alert('支付成功');
										window.location.href='http://b.autostep.com.cn/qiaohailin/H5_zc_xin/index.php/Home/Raiselist/zc_yesok?from=singlemessage&isappinstalled=0';
									}else if(count== "2"){
										alert("您以众筹过！");
									}else{
										alert('订单生成失败！');
									}
								}
						   });
					   });
					}
				}
			);
		}

		function callpay(){
			if (typeof WeixinJSBridge == "undefined"){
			    if( document.addEventListener ){
			        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
			    }else if (document.attachEvent){
			        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
			        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
			    }
			}else{
			    jsApiCall();
			}
		}
	
	</script>
<link href="zc_qecj.css" rel="stylesheet" type="text/css">
</head>
<body>
<div class="gridContainer clearfix">
  <div id="LayoutDiv1">
    
    <!--START===课程列表-->
    <div class="zc-course">
        <div class="list">
             <div class="text_1"></div><!--END-->
             <div class="text_1">
                <div class="usename_1">课程名称：</div>
                <div class="usename_2"><?=iconv('GB2312','UTF-8',$crowdfunding["title"])?></div>
            </div><!--END-->
            <div class="text_1">
                <div class="usename_1">课程讲师：</div>
                <div class="usename_2"><?=iconv('GB2312','UTF-8',$crowdfunding['player'])?></div>
            </div><!--END-->
            <div class="text_1">
                <div class="usename_1">上线时间：</div>
                <div class="usename_2"><?=$crowdfunding['starttime']?></div>
            </div><!--END-->
            
            <div class="text_1">
                <div class="usename_1">课程费用：</div>
                <div class="usename_2"><?=$crowdfunding['moviefen']?>元</div>
            </div><!--END-->
            <div class="text_1">
                <div class="usename_1">已参与人数：</div>
                <div class="usename_2"><?=40+$crowdorders[0]?><span>人</span></div>
            </div><!--END-->
        </div><!--END-->
            
            <div class="text_3">
				<button class="usename_1" type="button" onClick="callpay()" >确认参与</button>
            </div>
            
        <div class="fhu">
        	<a href="javascript:history.go(-1)"><div class="fhj">返回</div></a>
        </div>
        
        
    </div>
  	<!--END-->
    
    
    <!--START===底部按钮
    		<include file="Public/bottom" />
    <!--END-->
  
  </div>
</div><script>
  /*
   * 注意：
   * 1. 所有的JS接口只能在公众号绑定的域名下调用，公众号开发者需要先登录微信公众平台进入“公众号设置”的“功能设置”里填写“JS接口安全域名”。
   * 2. 如果发现在 Android 不能分享自定义内容，请到官网下载最新的包覆盖安装，Android 自定义分享接口需升级至 6.0.2.58 版本及以上。
   * 3. 完整 JS-SDK 文档地址：http://mp.weixin.qq.com/wiki/7/aaa137b55fb2e0456bf8dd9148dd613f.html
   *
   * 如有问题请通过以下渠道反馈：
   * 邮箱地址：weixin-open@qq.com
   * 邮件主题：【微信JS-SDK反馈】具体问题
   * 邮件内容说明：用简明的语言描述问题所在，并交代清楚遇到该问题的场景，可附上截屏图片，微信团队会尽快处理你的反馈。
   */
     var cfg = {
		share_url : "",
		title : '138秒 让你的客户说买',
		desc : '实战之尊苏桔良--为您剖析22种销售技巧',
		image_url : 'http://b.autostep.com.cn/qiaohailin/H5_zc_xin/H5raise/Home/Public/images/home/title.png',
	};

  wx.config({
    debug: 0,
	appId: "<?php echo $wx['appId'];?>",
	timestamp: <?php echo $wx['timestamp'];?>,
	nonceStr: "<?php echo $wx['nonceStr'];?>",
	signature: "<?php echo $wx['signature'];?>",
    jsApiList: [
      'onMenuShareTimeline','onMenuShareAppMessage'// 所有要调用的 API 都要加到这个列表中
    ]
  });

	wx.ready(function(){
		wx.onMenuShareTimeline({
			title: cfg.title, // 分享标题
			link: "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx4484942683d3f92c&redirect_uri=http://b.autostep.com.cn/qiaohailin/H5_zc_xin/index.php/Home/Index/index&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect", // 分享链接
			imgUrl: cfg.image_url, // 分享图标
			success: function () { 
				// 用户确认分享后执行的回调函数
			},
			cancel: function () { 
				// 用户取消分享后执行的回调函数
			}
		});
		wx.onMenuShareAppMessage({
			title: cfg.title, // 分享标题
			desc: cfg.desc, // 分享描述
			link: "https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx4484942683d3f92c&redirect_uri=http://b.autostep.com.cn/qiaohailin/H5_zc_xin/index.php/Home/Index/index&response_type=code&scope=snsapi_userinfo&state=123#wechat_redirect", // 分享链接
			imgUrl: cfg.image_url, // 分享图标
			type: '', // 分享类型,music、video或link，不填默认为link
			dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
			success: function () { 
				// 用户确认分享后执行的回调函数
			},
			cancel: function () { 
				// 用户取消分享后执行的回调函数
			}
		});
	});
</script>

</body>
</html>