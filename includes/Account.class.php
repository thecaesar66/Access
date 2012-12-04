<?php

###################################################################################################
###################################################################################################
class Account{

	public $error=array();

	public $date_long_format = "l, F j, Y, g:i a";
	public $date_short_format = "M j, Y";
	public $status = array("0"=>"Suspended", "1"=>"Active", "2"=>"Admin");

	private $default_status = 0; // default user status
	private $allow_change_username = false; // allows the user to change their own username

	// current user info
	public $info = array('user_id'=>0, 'username'=>'', 'email'=>'', 'status'=>0);

#-#############################################
# desc: constructor
public function __construct(){

}#-#construct()



#-#############################################
# desc: fills out the object with values from $_POST
# param: 
# returns: 
public function populate_post(){

	// if the user_id isn't already set/forced and is valid (user_id=0 fails. is used for "add new")
	if($this->info['user_id']==0 && !empty($_POST['user_id']) && is_numeric($_POST['user_id']))
		$this->info['user_id'] = $_POST['user_id'];

	if(!empty($_POST['username']))
		$this->info['username'] = $_POST['username'];

	// by default info['password'] isn't set
	if(!empty($_POST['password']))
		$this->info['password'] = $_POST['password'];
	if(!empty($_POST['password2']))
		$this->info['password2'] = $_POST['password2'];

	if(!empty($_POST['email']))
		$this->info['email'] = $_POST['email'];

	if(isset($_POST['status']) && is_numeric($_POST['status']))// no empty() cause can be zero
		$this->info['status'] = $_POST['status'];

}#-# populate_post()



#-#############################################
# desc: get user info from the database based off id
# param: id
# returns: assigned to object
public function populate_id($id) {
	// bad data
	if(!is_numeric($id)) return false;
	
	$db=Database::obtain();
	$sql = "SELECT user_id, username, email, created, accessed, status FROM ".TABLE_USERS." WHERE user_id = '".$db->escape($id)."' LIMIT 0,1";

	$user=$db->query_first($sql);
	
	// no user found
	if(empty($user['user_id'])) return false;

	$this->info['user_id']=$user['user_id'];
	$this->info['username']=$user['username'];
	$this->info['email']=$user['email'];
	$this->info['created']=$user['created'];
	$this->info['accessed']=$user['accessed'];
	$this->info['status']=$user['status'];

	return true;
}#-#populate_id()


#-#############################################
# desc: get user info from the database based off name
# param: username
# returns: assigned to object
public function populate_user($name){

	$db=Database::obtain();
    $sql = "SELECT user_id, username, email, created, accessed, status FROM ".TABLE_USERS." WHERE username = '".$db->escape($name)."' LIMIT 0,1";
	
	$user=$db->query_first($sql);
	
	// no user found
	if(empty($user['user_id'])) return false;

	$this->info['user_id']=$user['user_id'];
	$this->info['username']=$user['username'];
	$this->info['email']=$user['email'];
	$this->info['created']=$user['created'];
	$this->info['accessed']=$user['accessed'];
	$this->info['status']=$user['status'];

	return true;
}#-# populate_user()


#-#############################################
# desc: if form submitted data is valid for creating a user
# param: user is allowed to change username
# returns: true/false if valid. saves errors to $this->error[]
protected function validate($change_username=false){

	// if user doesn't exist or can change existing name, check username syntax and if name exists
	if($this->info['user_id']==0 || $change_username){
		if($this->is_str($this->info['username'],true,4,32) == false)
			$this->error['username']='Username must be between 4-32 alphanumeric characters.';
		elseif($this->exists($this->info['username']) == true)
			$this->error['username']='Username already exists. Please login or pick another name.';
	}

	// (user exists, either password not blank) or (user doesn't exist): checking password
	if( ($this->info['user_id']>0 && (!empty($this->info['password'])||!empty($this->info['password2']))) || $this->info['user_id']==0 ){
		if(empty($this->info['password']) || empty($this->info['password2']) || $this->is_str($this->info['password'],true,4,32) == false)
			$this->error['password']='Password must be between 4-32 alphanumeric characters.';
		elseif($this->info['password'] != $this->info['password2'])
			$this->error['password2']='Passwords do not match. Make sure you typed it correctly.';
	}

	if($this->is_email($this->info['email']) == false)
		$this->error['email']='Please enter a valid Email Address.';


	if(!empty($this->error))
		return false;
	else
		return true;
}#-#validate()


#-#############################################
# desc: save for the normal user (OVERWRITTEN IN Admin_Account)
# param: 
# returns: 
public function save(){

	// needs to be set to $user object so they don't change someone elses for user
	$this->info['user_id']=1;

	$this->populate_post();
	
	//error checking
	if(!$this->validate($this->allow_change_username)){
		return false;
	}

	// insert new record
	if(empty($this->info['user_id'])){
		$status=$this->default_status;
		$this->insert($status);
		return true;
	}
	// update old
	else{
		$this->update($this->allow_change_username);
		return true;
	}

}#-#save()


#-#############################################
# desc: inserts a new user into database
# param: 
# returns: id of inserted user
protected function insert($status){

	$data['username']=$this->info['username'];
	$data['salt']=$this->create_salt(3);
	$data['password']=md5($this->info['password'].$data['salt']);

	$data['email']=$this->info['email'];
	$data['created']="NOW()";

	$data['status']=$status;

	$db=Database::obtain();
	return $db->insert(TABLE_USERS, $data);
}#-#insert()


#-#############################################
# desc: check input, update user info if exists, inserts if does not exist
# param: user is allowed to change username
# returns: 
protected function update($change_username=false, $status=null){

	if(!empty($this->info['password'])){
		$data['salt']=$this->create_salt(3);
		$data['password']=md5($this->info['password'] . $data['salt']);
	}

	if($change_username){
		$data['username']=$this->info['username'];
	}

//	$data['firstname']=$this->info['firstname'];
//	$data['lastname']=$this->info['lastname'];
	$data['email']=$this->info['email'];

	// admin can feed a status and change it
	if($status != null){
		$data['status']=$status;
	}

	$db=Database::obtain();
	$db->update(TABLE_USERS, $data, 'user_id="'.$db->escape($this->info['user_id']).'"');

	return $this->info['user_id'];
}#-#update()


#-#############################################
# desc: 
# param: (int) status to be upgraded to. (int) member id of account to be upgraded. IF no member id, takes $_POST['chk'][] array and recursively upgrades all values
# returns: 
public function upgrade($to, $id=''){
	// not on specific user and input boxes are checked
	if(empty($id) && !empty($_POST['chk'])){
		foreach($_POST['chk'] as $v) {
			$this->upgrade($to, $v);			
		}
	}
	elseif(is_numeric($to) && is_numeric($id)){
		$data['status']=$to;
		$db=Database::obtain();
		$db->update(TABLE_USERS, $data, "user_id='".$db->escape($id)."'");
	}
}#-#upgrade()


#-#############################################
# desc: checks to see if a username exists in the database (was get_user_exists())
# param: 
# returns: true if exists, false if none exists
protected function exists(){
	if(empty($this->info['username'])) return false;

	$db=Database::obtain();
    $sql = "SELECT user_id FROM ".TABLE_USERS." WHERE username = '".$db->escape($this->info['username'])."' LIMIT 0,1";
	
	$row = $db->query_first($sql);
	
	// if user_id exists and does not match the current user
	if(!empty($row['user_id']) && $row['user_id'] != @$this->info['user_id'] )
		return true;
	else
		return false;
}#-# exists()


#-#############################################
# desc: 
# param: 
# returns: 
function send_welcome_email($id){

	/*$sql = "SELECT username, email FROM ".TABLE_USERS." WHERE user_id='$user_id LIMIT 0,1";

	$db=Database::obtain();
	$member = $db->query_first($sql);

	$search = array("{username}", "{firstname}", "{lastname}", "{email}");
	$replace = array($member['username'], $member['firstname'], $member['lastname'], $member['email']);

	$headers = 'From: '.$config['welcome_from'].'';

	if(mail($member['email'], str_replace($search, $replace, $config['welcome_subject']), str_replace($search, $replace, $config['welcome_message']), $headers))*/
	if(1)
		return true;
	else
		return false;
}#-# send_welcome_email()


#-#############################################
# desc: 
# param: 
# returns: 
protected function create_salt($length=3){
	$a=preg_split('//', "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#%^&*()_+=-[]{}|~`;:,.<>?", -1, PREG_SPLIT_NO_EMPTY);

	$salt = '';
	for ($i=0; $i<$length; ++$i){
		$salt .= $a[rand(0, count($a)-1)];
	}

	return $salt;
}#-#create_salt()


#-#############################################
# desc: 
# param: string, safe characters only (a-z,0-9,_,-), min chars, max chars
# returns: 
protected function is_str($str, $safe=false, $min=1, $max='') {
	$str=trim($str);

	if($safe)
		$pattern="/^[\w-]{".$min.",".$max."}\$/i";
	else
		$pattern="/^.{".$min.",".$max."}\$/i";

	if(!preg_match($pattern,$str)){
		return false;
	}
	
	return true;
}#-# is_str()


#-#############################################
# desc: 
# param: email adress
# returns: true if email syntax is valid. false if invalid
protected function is_email($email) {
	if(preg_match("/^([-_\.\+0-9a-z])+@[0-9a-z][-.0-9a-z]*\.[a-z]{2,6}$/i",$email))//v1.1
		return true;
	else
		return false;
}#-# is_email()


#-#############################################
# desc: creates an <select> box
# param: name, array['val']="display" of data, default selected, extra parameters
# returns: html of box and options
public function ric_create_selectbox($name, $data, $default='', $param=''){
    $out='<select name="'.$name.'"'. (!empty($param)?' '.$param:'') .">\n";

	foreach($data as $key=>$val) {
		$out.='<option value="' .$key. '"'. ($default==$key?' selected="selected"':'') .'>';
		$out.=$val;
		$out.="</option>\n";
	}
	$out.="</select>\n";

	return $out;
}#-# ric_create_selectbox()




}#-#CLASS: Account
###################################################################################################

?>