<?php
/***
COPYRIGHT ALLRESERVE pureapp.in.th 2014-2015
***/
// mysql configuration
define("MYSQL_SERV"  ,"localhost");
define("MYSQL_USER"  ,"pure_app");
define("MYSQL_PASS"	 ,"0805797336");
define("MYSQL_DB"	 ,"pure_like");
define("MYSQL_PREFIX","like_");

// Facebook app configuration
define("FB_SDK"		,"sdk/facebook.php");
define("APP_ID"		,"351070565012663"); // your app id
define("APP_SECRET"	,"e8b0cba1913bd44be7f1e4ec9e6f5c64");
define("GRAPH_VER"	,"v2.1");
define("LOGIN_PERM"	,"publish_actions,user_photos,user_videos,user_status");

// Defined Limit
define("STATUS_FETCH_LIMIT",20); 
define("LIKE_DOWN_LIMIT",30);


// Utility Function
function connect_db($db_name=MYSQL_DB){
	global $myserv;
	$myserv = mysql_connect(MYSQL_SERV,MYSQL_USER,MYSQL_PASS);
	mysql_select_db($db_name);
	mysql_set_charset('utf8');
}
function close_db(){
	global $myserv;
	mysql_close($myserv);
}
function sql_get($sql) {
    //$sql = preg_replace(sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"),"",$sql);
    //$sql = trim($sql);
    //$sql = strip_tags($sql);
    //$sql = addslashes($sql);
    return $sql;
}
?>
