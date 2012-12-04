<?php
require("includes/config.php");
require("includes/Database.singleton.php");
require("includes/Login.singleton.php");

// create initial singleton database connection and connect
$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();

// create login singleton object
$login = Login::obtain();

// force a user login
$login->hard();
?>

<!DOCTYPE html>
<html>
	<head>
		<title>CioffiTech Access</title>
	</head>
	<body>
		<p>There Would Be Content Here But Were Still Making This Place.</p>
	</body>
</html>
