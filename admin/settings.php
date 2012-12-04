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
		<title>Access CP - Admin - Settings</title>
			<style>
				#cont {
					text-align: center;
					font-family: Helvetica;
					font-weight: bold;
					border: 3px solid #d3e7f4;
					margin: 10px;
					margin-top: 30px;
					padding: .5em;
					background: #f0f0f0;
					-webkit-border-radius: 15px;
					-moz-border-radius: 15px;
					border-radius: 15px;
					color: #0000ff;
					font-size: 20d;
				}
				#htext {
					text-align: center;
				}
				#not {
					text-align: center;
				}
			</style>
		<link rel="stylesheet" href="main.css" type="text/css">
		<script src="http://www.cioffitech.com/ct/c/dropdown.js"></script>
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
		
			switch($cmd) 
			{
			
			case "save":
			@include('php/settings/save.php');
			break;
			
			case "directory": 
			@include('php/directory.php'); 
			break;
			
			case "support": 
			@include('php/support.php'); 
			break;
					
			}
		?>
	<br />
	<fieldset style="margin: 20px;">
		<legend>Change Control Panel Theme</legend>
			<form method="post" action="file.php?cmd=error" onSubmit="return dropdown(this.themechange)">
				<select name="themechange">
					<option value="?"> - Select New Theme - </option>
					<option value="file.php?cmd=error-themes">Blue (Default)</option>
					<option value="file.php?cmd=error-themes">Green</option>
					<option value="file.php?cmd=error-themes">Red</option>
					<option value="file.php?cmd=error-themes">Black</option>
				</select>
					<input type="Submit" value="Change Theme">
			</form>
	</fieldset>
	<fieldset style="margin: 20px;">
		<legend>Change Language</legend>
			<form method="get" action="file.php?cmd=error" onSubmit="return dropdown(this.changelang)">
				<select name="changelang">
					<option value=""> - Select Language - </option>
					<option value="settings.php?cmd=save">English</option>
					<option value="http://translate.google.com/translate?hl=en&amp;sl=en&amp;tl=es&amp;u=http%3A%2F%2Fcioffitech.com%2Faccess2%2Fadmin%2F%3Fcmd%3Dsavehttp://translate.google.com/translate?hl=en&amp;sl=en&amp;tl=es&amp;u=http%3A%2F%2Fcioffitech.com%2Faccess2%2Fadmin%2F%3Fcmd%3Dsave">Spanish</option>
					<option value="http://translate.google.com/translate?js=n&amp;prev=_t&amp;hl=en&amp;ie=UTF-8&amp;layout=2&amp;eotf=1&amp;sl=en&amp;tl=ru&amp;u=http%3A%2F%2Fcioffitech.com%2Faccess2%2Fadmin%2Fhttp://translate.google.com/translate?js=n&amp;prev=_t&amp;hl=en&amp;ie=UTF-8&amp;layout=2&amp;eotf=1&amp;sl=en&amp;tl=ru&amp;u=http%3A%2F%2Fcioffitech.com%2Faccess2%2Fadmin%2F%3Fcmd%3D=save">Russian</option>
				</select>
					<input type="Submit" value="Change Language">
			</form>
				<br />
			<h5 style="float:right;">Need another language? Send an email to <a href="mailto:cpsupport@cioffitech.com">cpsupport@cioffitech.com</a> with the requested language.</h5>
	</fieldset>
	<fieldset style="margin: 20px;">
		<legend>Commands</legend>
			<input type="button" value="Add New Users" onClick="window.location='users.php?action=new';">
			<input type="button" value="Upload Files" onClick="window.location='uploads.php';">
			<input type="button" value="About CT Access Control Panel" onclick="window.location='about.php';">
			<input type="button" value="Check For Updates" onclick="window.location='about.php?cmd=update';">
			<input type="button" value="Logout" onClick="window.location='users.php?action=clear_login';">
	</fieldset>
</div>
<div class="f_footer">
<?php require('includes/footer.inc.php'); ?>
</div>


</div>

	</body>
</html>
