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
if(!$facebook->getUser()){
	header("Refresh: 0; url=/index.php");
	exit(0);
}
$facebook->setExtendedAccessToken();
$user = $facebook->api('/me','get');
connect_db();
$res = mysql_query("SELECT * FROM ".MYSQL_PREFIX."account WHERE fbid=".$user['id']."") or die(mysql_error());
$row = mysql_num_rows($res);
if($row == 0){
  // welcome new user
	$res = mysql_query("INSERT INTO ".MYSQL_PREFIX."account (fbid,access_token,type,canlike,lastlogin,lastuse,like_up,like_down,like_realup,like_realdown,premium_until,topup) VALUES ('".$user['id']."','".$facebook->getAccessToken()."','1','1','".time()."','".time()."','0','0','0','0','0','0')") or die(mysql_error());
} else {
	// update old token user
	$res = mysql_query("UPDATE ".MYSQL_PREFIX."account SET lastlogin='".time()."',access_token='".$facebook->getAccessToken()."' WHERE fbid='".$user['id']."'") or die(mysql_error());
}
close_db();
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>หน้าหลัก - Like by pure's app</title>
		<meta http-equiv="content-type" content="text/html; charset=utf-8" />
		<meta name="description" content="" />
		<meta name="keywords" content="" />
		<!--[if lte IE 8]><script src="css/ie/html5shiv.js"></script><![endif]-->
		<script src="js/jquery.min.js"></script>
		<script src="js/jquery.dropotron.min.js"></script>
		<script src="js/jquery.scrollgress.min.js"></script>
		<script src="js/skel.min.js"></script>
		<script src="js/skel-layers.min.js"></script>
		<script src="js/init.js"></script>
		<noscript>
			<link rel="stylesheet" href="css/skel.css" />
			<link rel="stylesheet" href="css/style.css" />
			<link rel="stylesheet" href="css/style-wide.css" />
		</noscript>
		<!--[if lte IE 8]><link rel="stylesheet" href="css/ie/v8.css" /><![endif]-->
	</head>
	<body>

		<!-- Header -->
			<header id="header" class="skel-layers-fixed">
				<h1><a href="index.html">Like</a> by pure's app</h1>
				<nav id="nav">
					<ul>
						<li><a href="index.php">หน้าหลัก</a></li>
						<li>
							<a href="" class="icon fa-angle-down"><?php echo $user['name']; ?></a>
							<ul>
								<li><a href="feed.php">ขอไลค์เพิ่ม</a></li>
								<li><a href="generic.html">สถิติ</a></li>
								<li><a href="contact.html">สนับสนุนเซิฟ</a></li>
								<li>
									<a href="">วิธีการใช้งาน</a>
									<ul>
										<li><a href="#">กฏ/กติกา</a></li>
										<li><a href="#">การขอไลค์</a></li>
									</ul>
								</li>
								<li><a href="elements.html">ออกจากระบบ</a></li>
							</ul>
						</li>
					</ul>
				</nav>
			</header>

		<!-- Main -->
			<section id="main" class="container">
					<header>
						<h2>Like</h2>
						<p>ยินดีต้อนรับสู่ระบบแลกไลค์</p>
					</header>
					<section class="box special features">
						<div class="features-row">
							<section>
								<span class="icon major fa-thumbs-up accent5"></span>
								<h3>รับไลค์เพิ่ม</h3>
								<ul class="actions">
									<li><a href="status.php" class="button alt">สเตตัส</a></li>
									<li><a href="photo.php" class="button alt">รูปภาพ</a></li>
								</ul>
							</section>
							<section>
								<span class="icon major fa-area-chart accent3"></span>
								<h3>สถิติของคุณ</h3>
								<p>อัตราส่วนการไลค์ (ให้/รับ)<br> X / Y (ratio X.XXX)</p>
							</section>

						</div>
						<div class="features-row">
							<section>
								<span class="icon major fa-cloud accent4"></span>
								<h3>สนับสนุนเซิฟ</h3>
								<p>ช่วยเติมเงินให้เราเพื่อเป็นค่าซ่อมบำรุงเซิฟเวอร์และรับสิทธิพิเศษเล็กๆน้อยๆ</p>
								<ul class="actions">
									<li><a href="#" class="button alt">เติมเงิน</a></li>
								</ul>
							</section>
							<section>
								<span class="icon major fa-bolt accent2"></span>
								<h3>ประกาศ</h3>
								<p>โปรดศึกษาวิธีการใช้งานระบบก่อน เพื่อเรียนรู้วิธีการและกฎในการใช้งาน</p>
								<ul class="actions">
									<li><a href="#" class="button alt">วิธีการใช้งาน</a></li>
								</ul>
							</section>
						</div>
					</section>
			</section>

	<!-- Footer -->
		<footer id="footer">
			<ul class="icons">
				<li><a href="https://www.facebook.com/appmadebypure" class="icon fa-facebook"><span class="label">Facebook</span></a></li>
				<li><a href="https://www.github.com/pureexe" class="icon fa-github"><span class="label">Github</span></a></li>
				<li><a href="https://plus.google.com/+pakkaponphongtawee" class="icon fa-google-plus"><span class="label">Google+</span></a></li>
			</ul>
			<ul class="copyright">
				<li>&copy; <a href="https://www.pureapp.in.th">pure's app</a> All rights reserved.</li>
				<li>Design: <a href="http://html5up.net">HTML5 UP</a></li>
				<li>Hosted: <a href="https://bot-like.com">bot-like.com</a></li>
			</ul>
		</footer>
	</body>
</html>
