<?php

session_start(); // start session cookies

require("../includes/config.php");
require("../includes/Database.singleton.php");
require("../includes/Login.singleton.php");
require("../includes/Account.class.php");
require("includes/Admin_Account.class.php");

$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();

$login = Login::obtain(); // object login

// make user login (requires admin status)
$login->hard(2);
?>

<!DOCTYPE html>
	<html>
		<head>
			<title>Access CP - Admin - FILE.PHP</title>
				<style>
					#req {
						display: none;
					}
					#opt {
						display: none;
					}
					#admin {
						display: none;
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
<?php
	$cmd = $_GET['cmd'];
	if ($cmd=="") { $cmd = "home"; }

	switch($cmd) 
	{
	
	case "home":
	@include('home.php');
	break;
	
	case "directory": 
	@include('php/directory.php'); 
	break;
	
	case "support": 
	@include('php/support.php'); 
	break;
	
	case "code": 
	@include('php/code.php'); 
	break;
	
	case "exte":
	@include('php/exte.php');
	break;
	
	case "error":
	@include('php/error.php');
	break;
	
	case "error-themes":
	@include('php/error/themes.php');
	break;

	}
?>
</div>

<div class="f_footer">
<?php require('includes/footer.inc.php'); ?>
</div>


</div>

	</body>
</html>
