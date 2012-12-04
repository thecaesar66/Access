<?php

// upgrade needs changes to check if only admin

###################################################################################################
###################################################################################################
class Admin_Account extends Account{

	private $allow_namechange=true;


#-#############################################
# desc: constructor
public function __construct(){

}#-#construct()




#-#############################################
# desc: save for the admin. (OVERWRITES USER VERSION)
# param: 
# returns: false if errors saving (validation), true if worked
public function save(){
	$this->populate_post();
	
	//error checking. $this->validate(name change allowed)
	if($this->validate(true) == false){
		return false;
	}

	// insert new record
	if($this->info['user_id']==0){
		$status=$this->info['status'];
		$this->insert($status);
	}
	// update old
	else{
		$status=$this->info['status'];
		$this->update(true,$status); //$this->update(name change allowed,status)
	}

	return true;

}#-#save()


#-#############################################
# desc: deletes specified user id. if none set, removes ids from _POST['chk'] array
# param: id to delete
# returns: 
public function delete($id=''){
	if(empty($id) && !empty($_POST['chk'])){
		foreach($_POST['chk'] as $v) {
			$this->delete($v);			
		}
	}
	elseif(is_numeric($id) && !$this->is_only_admin($id)){
		$db=Database::obtain();
		$db->query("DELETE FROM ".TABLE_USERS." WHERE user_id='".$db->escape($id)."'");
	}
}#-#delete()


#-#############################################
# desc: checks to see if user is the only admin (so they don't lock themself out)
# param: user id to check
# returns: true if that user is the only, false if there are other admin
private function is_only_admin($id){
	$db=Database::obtain();

    $sql = "SELECT status FROM ".TABLE_USERS." WHERE user_id = '".$db->escape($id)."'";
	$status = $db->query_first($sql);

    $sql = "SELECT count(user_id) AS count FROM ".TABLE_USERS." WHERE status = 2";
	$count = $db->query_first($sql);


	if($status['status']==2 && $count['count']==1)
		return true;
	else
		return false;
}#-# is_only_admin()


#-#############################################
# desc: gets list of all users for displaying
#		will sort depending on  ?sort=asc&order=realname
# param: starting position, max number to of users to retrieve
# returns:
public function list_all($offset=0,$max=50){
	$db=Database::obtain();

    $sql = "SELECT user_id, username, email, created, status FROM `".TABLE_USERS."`";

	// if filtering down
	if(isset($_GET['status']) && is_numeric($_GET['status'])){
		$sql.=" WHERE status = ".(int)$_GET['status'];
	}

	// what field to sort
	$sql.= " ORDER BY `".((!empty($_GET['order'])&&($_GET['order']=='username'||$_GET['order']=='email'||$_GET['order']=='created'||$_GET['order']=='status'))?$db->escape($_GET['order']):'created')."`";

	// what direction to sort
	$sql.= " ".(!empty($_GET['sort'])&&$_GET['sort']=='asc'?'ASC':'DESC')."";

	// what to limit (page-ify)
	$sql.= " LIMIT ".(int)$offset.",".(int)$max;

	return $db->fetch_array($sql);
}#-#list_all()


}#-#CLASS: Admin_Account
###################################################################################################

?>