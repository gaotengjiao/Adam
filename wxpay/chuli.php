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
			
		$openid		= $_POST['openid'];
        $gogsi		= mb_convert_encoding($_POST['gogsi'],"GB2312","UTF-8");
        $zhiwei		= mb_convert_encoding($_POST['zhiwei'],"GB2312","UTF-8");
        $weixinhao	= mb_convert_encoding($_POST['weixinhao'],"GB2312","UTF-8");
        $time		= time();
		
		$sql="insert into phome_ecms_wxuser(openid,gogsi,zhiwei,weixinhao,time) values('".$openid."','".$gogsi."','".$zhiwei."','".$weixinhao."','".$time."')";
		$val = mysql_query($sql);
			if($val){
				echo 0;	
			}else{
				echo 1;	
			}
		

?>