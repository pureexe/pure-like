<?php
/*
COPYRIGHT ALLRESERVE pureapp.in.th 2014-2015
*/

require_once('z-config.php');
require_once(FB_SDK);
$config = array(
	'appId' => APP_ID,
	'secret' => APP_SECRET,
	'cookie' => TRUE,
);
$facebook = new Facebook($config);
if(isset($_GET['mode'])){
	if($_GET['mode']=='login'){
		if(!$facebook->getUser()){
			echo "plz_login";
			exit(0);
		}
		$facebook->setExtendedAccessToken();
		$user = $facebook->api('/me','get');
		connect_db();
		$res = mysql_query("SELECT * FROM ".MYSQL_PREFIX."account WHERE fbid=".$user['id']."") or die(mysql_error());
		$row = mysql_num_rows($res);
		if($row == 0){
			// welcome new user
			$res = mysql_query("INSERT INTO ".MYSQL_PREFIX."account (fbid,access_token,type,canlike,lastlogin,lastuse,like_up,like_down,like_realup,like_realdown,premium_until,topup) VALUES ('".$user['id']."','".$facebook->getAccessToken()."','1','1','".time()."','".time()."','0','0','0','0','0','0')") or die(error_log(mysql_error()));
		} else {
			// update old token user
			$res = mysql_query("UPDATE ".MYSQL_PREFIX."account SET lastlogin='".time()."',access_token='".$facebook->getAccessToken()."' WHERE fbid='".$user['id']."'") or die(error_log(mysql_error()));
		}
		close_db();
		echo "sussess";
		exit(0);
	} else if($_GET['mode']=='downlike'&&isset($_GET['id'])){
		if(isset($_GET['id'])){
			try{
				$post = $facebook->api('/'.$_GET['id'].'?fields=from');
				$user_id = $facebook->getUser();
				if($post['from']['id']==$user_id){
					connect_db();

					$res = mysql_query("SELECT * FROM ".MYSQL_PREFIX."account WHERE canlike=1 AND lastlogin>".(time()-5184000)." ORDER BY lastuse ASC LIMIT ".LIKE_DOWN_LIMIT."") or die(error_log(mysql_error()));
					$mytoken = $facebook->getAccessToken();
					$downcredit = 0;
					while($a = mysql_fetch_array($res)){
						try{
							$facebook->setAccessToken($a['access_token']);
							$facebook->api('/'.$_GET['id'].'/likes','POST');
							mysql_query("UPDATE ".MYSQL_PREFIX."account SET lastuse='".time()."',like_up='".($a['like_up']+1)."' WHERE fbid='".$a['fbid']."'") or die(error_log(mysql_error()));
							$downcredit++;
						} catch(FacebookApiException $e) {
							mysql_query("UPDATE ".MYSQL_PREFIX."account SET canlike=0 WHERE fbid='".$a['fbid']."'") or die(error_log(mysql_error()));
						}
					}
					echo $downcredit;
					$facebook->setAccessToken($mytoken);
					mysql_query("UPDATE ".MYSQL_PREFIX."account SET lastuse='".time()."',like_down='".($a['like_down']+$downcredit)."' WHERE fbid='".$user_id."'") or die(error_log(mysql_error()));
					close_db();
				}
			} catch(FacebookApiException $e) {
				error_log($e);
				echo "API error";
			}
		}
		exit(0);
	}else {
		echo "mode_not_found";
		exit(0);
	} 
}
?>
