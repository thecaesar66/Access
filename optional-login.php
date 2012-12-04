<?php

session_start(); // start session cookies (otherwise won't remember login between pages)

require("includes/config.php");
require("includes/Database.singleton.php");
require("includes/Login.singleton.php");

// create initial singleton database connection and connect
$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
$db->connect();

// create login singleton object
$login = Login::obtain();

// see if the user is logged in
$login->soft();


// display a welcome message if user is logged in
if(!empty($login->info['user_id'])){
	echo "<h1>Welcome ".$login->info['username']."</h1>";
}

?>
<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus in leo id ligula ornare hendrerit. Praesent convallis dui sed elit pretium sed convallis arcu ultrices. Vestibulum egestas dignissim leo.</p>