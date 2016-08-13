<?php
$linkID=@mysql_connect("rm-2ze921c76782e3772o.mysql.rds.aliyuncs.com","qiaohailin","qiao123@");
		if(!$linkID){
			echo "没连接上";
			}
	
		$ss=@mysql_select_db("a_step");
		mysql_query("SET NAMES 'GB2312'"); 
		if(!$ss){
			echo "没找到数据库";
			}
			
		$ordersmark = $_POST['ordersmark'];
        $corwdid = $_POST['corwdid'];
        $userid = $_POST['userid'];
        $newstime = date('Y-m-d H:i:s');
        $moneyub = $_POST['moneyub'];
        $state = '0';
		$fasercount = mysql_fetch_array(mysql_query("select count(*) from phome_ecms_crowdorders where corwdid='".$corwdid."' and userid=".$userid));
		
		if($fasercount[0]>0){
			echo 2;
		}else{
			$sql = "insert into phome_ecms_crowdorders(ordersmark,corwdid,userid,newstime,moneyub,state) values('".$ordersmark."','".$corwdid."','".$userid."','".$newstime."','".$moneyub."','".$state."')";
			$val = mysql_query($sql);
			if($val){
				$movie = mysql_fetch_array(mysql_query("select thenumber,state from phome_ecms_movie where id='".$corwdid."'"));
				$count = mysql_fetch_array(mysql_query("select count(*) from phome_ecms_crowdorders where corwdid='".$corwdid."'"));
				if($count[0] >= $movie['thenumber'] && $movie['state'] == 0){
					$state = mysql_fetch_array(mysql_query("update phome_ecms_movie set state =2 WHERE id='".$corwdid."'"));
				}
				echo 0;	
			}else{
				echo 1;	
			}
		}
?>