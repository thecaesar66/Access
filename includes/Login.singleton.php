<?php
# Name: Login.class.php
# File Description: Login script to 
# Author: ricocheting
# Web: http://www.ricocheting.com/
# Update: 2010-05-09
# Version: 3.0.0 singleton
# Copyright 2003 ricocheting.com


/*
This script is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/



###################################################################################################
###################################################################################################
###################################################################################################
# CLASS desc: for calling login authentication
# CLASS req: looks for constants LOGIN_USER and LOGIN_PASS
# Can be called:  ?action=clear_login   ?action=prompt
class Login {

	// settings
	private $prefix = "cms_";
	private $cookie_duration = 21;  // days "remember me" cookies will remain

	public $status = array("0"=>"Suspended", "1"=>"Active", "2"=>"Administrator");

	// info that 
	public $info=array();

	// store the single instance of Class
	private static $instance;


#-#############################################
# desc: constructor
private function __construct(){

	if(isset($_COOKIE[$this->prefix.'username']) && isset($_COOKIE[$this->prefix.'password'])){
		$_SESSION[$this->prefix.'username'] = $_COOKIE[$this->prefix.'username'];
		$_SESSION[$this->prefix.'password'] = $_COOKIE[$this->prefix.'password'];
	}

	//if setting vars
	if(isset($_POST['action']) && $_POST['action'] == "set_login"){
		$this->set();//exits if incorrect
	}
	//if forced prompt
	elseif(isset($_GET['action']) && $_GET['action'] == "prompt"){
		$this->clear();
		$this->prompt();//exits
	}
	//if clearing the login
	elseif(isset($_GET['action']) && $_GET['action'] == "clear_login"){
		$this->clear();
		$msg='<h2 class="msg">Logout complete</h2>';
		$this->prompt($msg);//exits
	}

}#-#constructor()


#-#############################################
# desc: singleton declaration
public static function obtain(){
	if (!self::$instance){ 
		self::$instance = new self(); 
	} 

	return self::$instance; 
}#-#obtain()


#-#############################################
# desc: checks login to see if correct. if not, forces login
# param: minimum status level to access page
public function hard($status=1) {

	//if no sessions set, prompt
	if(!isset($_SESSION[$this->prefix.'password']) || !isset($_SESSION[$this->prefix.'username'])){
		$this->prompt();//exits
	}
	// if not valid, force login
	elseif($this->check($_SESSION[$this->prefix.'username'], $_SESSION[$this->prefix.'password']) == false){
		$this->clear();
		$msg='<h2 class="msg">Incorrect password or username</h2>';
		$this->prompt($msg);//exits
	}
	// don't meet status level
	elseif($this->info['status'] < $status){
		$this->clear();
		$msg='<h2 class="msg">Incorrect permission to access this page</h2>';
		$this->prompt($msg);//exits
	}

	return true;
}#-#hard()


#-#############################################
# desc: checks login to see if correct. if invalid or not logged in, nothing happens
# returns: true if login correct, false if error
public function soft() {

	if(!isset($_SESSION[$this->prefix.'password']) || !isset($_SESSION[$this->prefix.'username'])){
		return false;
	}

	return $this->check($_SESSION[$this->prefix.'username'], $_SESSION[$this->prefix.'password']);
}#-#soft()


#-#############################################
# desc: sets the login info from form data
private function set(){
		$db=Database::obtain();

		// if not valid info
		if($this->check(@$_POST['username'], null, @$_POST['password']) == false){

			// if some status (eg: not paid)
			//if($this->info['status']=='5'){
			//	header("Location:payment.php?memberid=$this->memberid");
			//	exit;
			//}

			$msg='<h2 class="msg">Incorrect username or password.</h2>';
			$this->clear();
			$this->prompt($msg);//exits
		}

		//if "remember me" is checked
		if(isset($_POST['remember'])){
			setcookie($this->prefix."username", $this->info['username'], time()+($this->cookie_duration*86400));// (d*24h*60m*60s)
			setcookie($this->prefix."password", $this->info['password'], time()+($this->cookie_duration*86400));// (d*24h*60m*60s)
		}

		//set session
		$_SESSION[$this->prefix.'username'] = $this->info['username'];
		$_SESSION[$this->prefix.'password'] = $this->info['password'];

}#-#set()


#-#############################################
# desc: clears all cookies and sensitive object properties
function clear(){
	@session_unset();
	@session_destroy();
	// destroy cookie by setting time in past
	setcookie($this->prefix."username", "blanked", time()-3600);
	setcookie($this->prefix."password", "blanked", time()-3600);

	$this->info=array();
}#-#clear()


#-#############################################
# desc: sets the $this->info array to the username's data
# param: username
public function set_user($username){
	$db=Database::obtain();

	$sql = "SELECT user_id, username, password, salt, email, created, accessed, status FROM `" .TABLE_USERS. "` WHERE username = '".$db->escape($username)."'";

	$this->info=$db->query_first($sql);
}#-#set_user()


#-#############################################
# desc: grabs user info
private function check($username, $password, $plain_password=null){

	// assigns $this->info values
	$this->set_user($username);

	// if plain password, hash it
	if($plain_password!=null && isset($this->info['salt'])) $password=md5($plain_password.$this->info['salt']);

	// no user by that name, or bad password
	if(!isset($this->info['username']) || $password != $this->info['password']){
		$this->clear();
		return false;
	}
	// if status is funky (eg; waiting payment so no login but need user info)
	elseif($this->info['status'] == '-1'){
		return false;
	}

	//clean login
	return true;

}#-#check()


#-#############################################
# desc: prompt to enter password
# param: any custom message to display
# returns: nothing, but it exits at end
public function prompt($msg=''){
?>
<html><head>
<title>Login</title>
	<style>
	body{font:normal 10pt verdana,arial;color:black;background-color:white;margin:15px;}
	table, td{border-collapse:collapse;}
	td{font-size:10pt;border:1px #535353 solid;padding:2px 3px;text-align:center;background-color:#eeeeee;}
	.msg{text-align:center;color:green;font-weight:bold;}
	.header{background-color:#cccccc;font-weight:bold;}
	</style>
</head><body>
	<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="post">
	<input type="hidden" name="action" value="set_login">

	<?php echo $msg; ?>

	<table align="center">
	<tr><td class="header" colspan="2">Enter Login Info</td></tr>
	<tr>
		<td><label for="user">Username:</label><br /><input type="text" name="username" id="user" autofocus="autofocus"></td>
		<td><label for="pass">Password:</label><br /><input type="password" name="password" id="pass"></td>
	</tr>

	<tr><td colspan="2" style="text-align:left;">
		<input type="checkbox" name="remember" id="remember"> <label for="remember">Remember me on this computer</label>
	</td></tr>

	<tr><td colspan="2" style="text-align:right;"><input type="submit" value="Login"></td></tr>

	</table>

	</form>
</body></html>
<?php
	//don't run the rest of the page
	exit;
}#-#prompt()


}//CLASS Login
###################################################################################################

?>