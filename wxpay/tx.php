﻿<?php
error_reporting(0);
if (isset($_GET['code']))
	{	
$openid="";
		$code	= $_GET['code'];
		$appid	= "wxaf6078a557ceeeec";
		$secret	= "4290895a40612599370221ab71995c91";
		
		$json = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=".$appid."&secret=".$secret."&code=".$code."&grant_type=authorization_code");
		$arr = json_decode($json,true); 
		$refresh_token = $arr['refresh_token'];	
		$json = file_get_contents("https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=".$appid."&grant_type=refresh_token&refresh_token=".$refresh_token);
		$arr = json_decode($json,true);
		$token = $arr['access_token'];
		$openid = $arr['openid'];  
		//拿到token后就可以获取用户基本信息了   
		$json = file_get_contents("https://api.weixin.qq.com/sns/userinfo?access_token=".$token."&openid=".$openid."&lang=zh_CN");//获取微信用户基本信息 ，'字符串' 
		$arr = json_decode($json,true);  //把json解析成数组
		$openid = $arr['openid'];
	}else
	{
		echo 'no code';
	}
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>聚智道</title>
<meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0'/>
<link rel="stylesheet"  type="text/css" href="css/public.css" />
<script type="text/javascript" src="js/jquery.min.js" ></script>

<link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet" />
<script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>
<script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>


<link rel="stylesheet"  type="text/css" href="css/Payment.css" />
<script>
function ahuw(){
	var openid		= '<?php echo $openid; ?>';
	var gogsi		= $("#gogsi").val();
	var zhiwei		= $("#zhiwei").val();
	var weixinhao	= $("#weixinhao").val();
	var reg = new RegExp("[\\u4E00-\\u9FFF]+","g");    
	if(gogsi==""){
		alert('公司不能为空');
	}else if(zhiwei==""){
		alert('职务不能为空');
	}else if(weixinhao==""){
		alert('手机号不能为空');
	}else if(reg.test(weixinhao)){
		alert('手机号不能汉字');
	}else{
		$.ajax({
			type: 'POST',
			url: "chuli.php",
			data:{"openid":openid,"gogsi":gogsi,"zhiwei":zhiwei,"weixinhao":weixinhao},
			dataType: "text",
			success: function (count) {
				//alert(count);
				if(count=="0"){
					window.location.href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxaf6078a557ceeeec&redirect_uri=http://www.buruwo.com/wxpay/demo/js_api_call.php&response_type=code&scope=snsapi_userinfo&state=state#wechat_redirect";
				}
			}
		});
	}
}

</script>
</head>

<body>
<div class="injuhnhb">
    <div class="input-group">
         <span class="input-group-addon">公<span style="color:#eeeeee;">公</span>司：</span>
         <input type="text" id="gogsi" class="form-control" placeholder="如:聚智道">
    </div>
    <br />
    <div class="input-group">
         <span class="input-group-addon">职<span style="color:#eeeeee;">公</span>位：</span>
         <input type="text" id="zhiwei" class="form-control" placeholder="如:总监">
    </div>
    <br />
    <div class="input-group">
         <span class="input-group-addon">手机号：</span>
         <input type="text" id="weixinhao" class="form-control" placeholder="如:13800138000">
    </div>
    <br />
</div>

<div class="bfmjgh">
	<button type="button" class="btn btn-primary btn-lg btn-block" style="background:#fe7701; border-color:#fe7701;" onClick="ahuw()">
      立即加入
   </button></div>
   
   
<div class="ljx">咨询联系人</div>
<p class="juopiuh">联系人：张三</p> 
<p class="juopiuh">微信号：ju_zhidao</p> 
<p class="juopiuh">手机号：13800138000</p> 

<div class="ljx">报名人</div>


<?php
$linkID=@mysql_connect("rm-2ze921c76782e3772o.mysql.rds.aliyuncs.com","qiaohailin","qiao123@");
if(!$linkID){
	echo "没连接上";
	}

$ss=@mysql_select_db("hjd");
mysql_query("SET NAMES 'GB2312'"); 
if(!$ss){
	echo "没找到数据库";
	}

$member = mysql_query("select * from phome_enewsmember as a,phome_enewsmemberadd as b where a.userid=b.userid and groupid='2'"); 
while ($property = mysql_fetch_array($member)){
?>
<div class="jhih" style=" margin-top:5px;" >
	<div class="ifsdmg"><img src="<?=iconv('GB2312','UTF-8',$property['userpic'])?>" /></div>
    <div class="truel"><?=iconv('GB2312','UTF-8',$property['truename'])?><Br><?=iconv('GB2312','UTF-8',$property['gogsi'])?></div>
</div>

<?php
}
?>

</body>
</html>
