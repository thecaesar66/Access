<nav align="center">
	<a href="javascript:toggleLayer('req');">Required Login</a> | 
	<a href="javascript:toggleLayer('opt');">Optional Login</a> | 
	<a href="javascript:toggleLayer('admin');">Admin Login</a> | 
	<a href="http://cp.cioffitech.com/library/">Code Library</a>
</nav>

<div id="req">
<h2>Required Login:</h2>
<br />
<xmp>
		session_start(); // start session cookies (otherwise won't remember login between pages)
		
		require("includes/config.php");
		require("includes/Database.singleton.php");
		require("includes/Login.singleton.php");
		
		// create initial singleton database connection and connect
		$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
		$db->connect();
		
		// create login singleton object
		$login = Login::obtain();
		
		// force the user to log in
		$login->hard();
		
		?>
</xmp>
<br />
<p style="font-size: 10pt;">NOTE: Due to compatibility issues the beginning PHP reference tag is missing.</p>
</div>

<div id="opt">
<h2>Optional Login:</h2>
<br />
<xmp>
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
</xmp>
<br />
<p style="font-size: 10pt;">NOTE: Due to compatibility issues the beginning PHP reference tag is missing.</p>
</div>

<div id="admin">
	<h2>Admin Login:</h2>
<xmp>
	session_start(); // start session cookies (otherwise won't remember login between pages)
	
	require("includes/config.php");
	require("includes/Database.singleton.php");
	require("includes/Login.singleton.php");
	
	// create initial singleton database connection and connect
	$db = Database::obtain(DB_SERVER, DB_USER, DB_PASS, DB_DATABASE);
	$db->connect();
	
	// create login singleton object
	$login = Login::obtain();
	
	// force a user login
	$login->hard(2);
	
	?>
</xmp>
<br />
<p style="font-size: 10pt;">NOTE: Due to compatibility issues the beginning PHP reference tag is missing.</p>
</div>