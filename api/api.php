<?php
/*
// จัดทำโดย: Access-Token By LikeForGift.Com
// เว็ตไซต์:  https://www.access-token.com
// หมายเหตุ : ไม่จำเป็นอย่าแก้ไขไฟล์นี้
// :) ขอบคุณครับ
*/
error_reporting(0);
set_time_limit(0);
require_once("inc/config.inc.php");
require_once("inc/function.php");
require_once("inc/class.mysql.php");
require_once('inc/AES.php');
$db = New DB();
if(get_client_ip() == '128.199.229.177' && isset($_GET['request'])){
	$aes = new Crypt_AES();
	$aes->setKey(APIKEY);
	$_GET['request'] = base64_decode(strtr($_GET['request'], '-_,', '+/='));
	$_GET['request'] = $aes->decrypt($_GET['request']);
	if($_GET['request'] != false){
		parse_str($_GET['request'],$request);
		$request['func'] = base64_decode($request['func']);
		$request['limit'] = base64_decode($request['limit']);
		$request['json'] = base64_decode($request['json']);
		switch ($request['func']) {
			case 'save':
				$jsonlink = $request['json'];
				$datajson = send("https://www.access-token.com/data/".$jsonlink.".json");
				$datajson1 = $aes->decrypt(base64_decode(strtr($datajson, '-_,', '+/=')));
				$json = json_decode($datajson1,true);
				$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
				foreach ($json as $key) {
					$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
					$res['check_token'] = $db->select_query("SELECT * FROM ".TB_TOKEN." WHERE ".FD_USERID."='".base64_decode($key['userid'])."' ");
					$arr['check_token'] = $db->fetch($res['check_token']);
					if(!$arr['check_token'][FD_USERID]){
						$db->add_db(TB_TOKEN,array(
							FD_USERID=>"".base64_decode($key['userid'])."",
							FD_TOKEN=>"".base64_decode($key['token']).""
						));
					}else{
						$db->update_db(TB_TOKEN,array(
							FD_TOKEN=>"".base64_decode($key['token']).""
						),"".FD_USERID."='".base64_decode($key['userid'])."' ");
					}
				}
				$db->closedb ();
				echo "SUCCESS";
				break;
			case 'load':
				$JsonArray = array();
				$db->connectdb(DB_NAME,DB_USERNAME,DB_PASSWORD);
				$res['tokenlist'] = $db->select_query("SELECT * FROM ".TB_TOKEN." WHERE ".FD_TOKEN."!='' ORDER BY RAND() LIMIT ".$request['limit']." ");
				while ($arr['tokenlist'] = mysql_fetch_array($res['tokenlist'])){
					$json = Array(
						"userid"=>base64_encode($arr['tokenlist'][FD_USERID]),
						"token"=>base64_encode($arr['tokenlist'][FD_TOKEN])
					);
					array_push($JsonArray,$json);
				}
				$db->closedb ();
				$jsonall = format_json(html_entity_decode(json_encode($JsonArray)));
				echo "list=".urlencode(base64_encode($aes->encrypt($jsonall)))."";
				break;
			case 'checkapi':
				echo 'true';
				break;
		}
	}else{
		echo 'INVALID_PASSKEY';
	}
}else{
	echo 'ACCESS_DENIED';
}
?>