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
			<title>CT Access - Admin - File Uplodads</title>
				<style>

				</style>
			<link rel="stylesheet" href="main.css" type="text/css">
		<script src="http://www.cioffitech.com/ct/c/div.js"></script>
		<script>
			function openspace(){
				window.open("php/space.php","_blank", "height=160px, width=250px",false)
			}
		</script>
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
	<form action="upload-files.php" method="post" enctype="multipart/form-data">
		<p>
			<label for="file">Select a file:</label> <input type="file" name="userfile" id="file"> <button>Upload File</button>
		<p>
		<p><br />
			<p><h3>Available Formats:</h3> .jpg, .gif, .bmp, .png, .html, .php, .sql, .mp4, .wmv, .pdf - <a href="file.php?cmd=exte">Click Here</a> to Recommend an Extention</h3></p>
			<p><h3>Forbidden Formats:</h3> .exe, .app, .dll, .virus, .fake</p>
			
		</p>
	</form>
	<br /><br />
	<ul style="list-style: none;">
		<li><a href="files/" target="_blank">View Current Files On Disk</a></li>
		<li><a href="javascript:openspace();">Check Disk Space</a></li>
	</ul>
</div>

<div class="f_footer">
<?php require('includes/footer.inc.php'); ?>
</div>


</div>

	</body>
</html>
