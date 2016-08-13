<?php
require_once "./jssdk.php";
$aaa=$_POST['url'];
$jssdk = new JSSDK("wxaf6078a557ceeeec", "4290895a40612599370221ab71995c91",$aaa);
$signPackage = $jssdk->GetSignPackage();
echo json_encode($signPackage);
?>