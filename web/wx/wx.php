<?php
require_once "jssdk.php";
$jssdk = new JSSDK("wxaf6078a557ceeeec", "4290895a40612599370221ab71995c91");
$signPackage = $jssdk->GetSignPackage();
echo print_r($signPackage);
?>