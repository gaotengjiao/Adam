<?php

	$mysql_server_name="rm-2ze921c76782e3772o.mysql.rds.aliyuncs.com"; //数据库服务器名称
    $mysql_username="simeiliyan"; // 连接数据库用户名
    $mysql_password="sone123456"; // 连接数据库密码
    $mysql_database="simeiliyan"; // 数据库的名字
    
    // 连接到数据库
    @$link=mysql_connect($mysql_server_name, $mysql_username,
                        $mysql_password);

    mysql_select_db($mysql_database, $link);
    mysql_query("set names UTF8");
    // if($link){
    // 	echo '1';
    // }else{
    // 	echo '2';
    //  // 从表中提取信息的sql语句
    // }
    $time = date('YmdHis');
	$sql="insert into s_user(time) value(".$time.")";
	
    // 执行sql查询
    $result = mysql_query($sql);
    // 获取查询结果
    // var_dump($result);
    // @$row = mysql_fetch_row($result);
    // $result=array();
    if($result){
    	echo '成功';
	}else{
		echo '失败';
	}
    // print_r($row);    }
