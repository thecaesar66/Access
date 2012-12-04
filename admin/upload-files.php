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
		<title>Uploading Files to Control Panel...</title>
		
		<link rel="stylesheet" href="main.css">
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
		   // Configuration - Your Options
		      $allowed_filetypes = array('.jpg','.gif','.bmp','.png', '.html', 'php', '.sql', '.mp4', '.wmv', '.pdf'); // These will be the types of file that will pass the validation.
		      $max_filesize = 104857600; // Maximum filesize in BYTES (currently 100MB).
		      $upload_path = 'files/'; // The place the files will be uploaded to (currently a 'files' directory).
		 
		   $filename = $_FILES['userfile']['name']; // Get the name of the file (including file extension).
		   $ext = substr($filename, strpos($filename,'.'), strlen($filename)-1); // Get the extension from the filename.
		 
		   // Check if the filetype is allowed, if not DIE and inform the user.
		   if(!in_array($ext,$allowed_filetypes))
		      die('The file you attempted to upload is not allowed.');
		 
		   // Now check the filesize, if it is too large then DIE and inform the user.
		   if(filesize($_FILES['userfile']['tmp_name']) > $max_filesize)
		      die('The file you attempted to upload is too large.');
		 
		   // Check if we can upload to the specified path, if not DIE and inform the user.
		   if(!is_writable($upload_path))
		      die('You cannot upload to the specified directory, please CHMOD it to 777.');
		 
		   // Upload the file to your specified path.
		   if(move_uploaded_file($_FILES['userfile']['tmp_name'],$upload_path . $filename))
		         echo 'Your file upload was successful, view the file <a href="' . $upload_path . $filename . '" title="Your File">here</a> - <a href="../users.php">Continue</a>'; // It worked.
		      else
		         echo 'There was an error during the file upload.  Please try again.'; // It failed :(.
		 
		?>
		</div>
		<div class="f_footer">
			<?php require('includes/footer.inc.php'); ?>
		</div>
		
	</div>
</body>
</html>