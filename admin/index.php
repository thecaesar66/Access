<?php
session_start(); // start session cookies (otherwise won't remember login between pages)

require("../includes/config.php");
require("../includes/Database.singleton.php");
require("../includes/Login.singleton.php");

// create initial singleton database connection and connect
$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();

// create login singleton object
$login = Login::obtain();

// force a user login
$login->hard(2);
?>

<!DOCTYPE html>
<html>
	<head>
		<title>Access CP - Admin - Home</title>
			<style>
				#errorbar {
					background-color: #770000;
					color: #ffffff;
					text-align: center;
					font-size: 9pt;
					margin: auto;
				}
			</style>
		<link rel="stylesheet" href="main.css" type="text/css">
		<script src="http://www.cioffitech.com/ct/c/div.js"></script>
	</head>
	<body>
		<div class="f_container">


<div class="f_header">
<?php require('includes/header.inc.php'); ?>
</div>

<div class="f_menu">
<?php require('includes/menu.inc.php'); ?>
</div>

<div class="f_body">
	<span align="center" id="errorbar">ATTENTION: We are experiancing some issues with our news feed and the THREE NEW CONTROL BUTTONS at thee bottom of the page are not functioning.</span><br /><br />
	<?php
		/* News and Updates of the Control Panel are kept and feed through the CioffiTech Server. DO NOT TOUCH THIS LINE OF CODE! */
		/* @include('../../sd/cp/files/news.php'); */
		/* echo implode(file("http://http://cp.cioffitech.com/files/news.php")); */
		$newsfeed = file_get_contents('http://cp.cioffitech.com/files/news.php');
		echo $newsfeed;
	?>
</div>
<div class="f_footer">
<?php require('includes/footer.inc.php'); ?>
</div>


</div>

	</body>
</html>