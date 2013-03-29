<?php
	
	if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
	        die ("You can't access this file directly...");
	}

	$grant_access = Array("super_user");
	check_access();
	
	class Users extends SQL_get{	
		var $module = 'Users';
		var $xsl_source = "/Modules/Admin/admin.xsl";
		
		function Users($id = null) {
			$this->check_access();
			if ($id != null){
				$this->SQL_get("SELECT * FROM users WHERE id = $id","user");
			}else{
				$this->SQL_get("SELECT * FROM users","user");
			}
	   	}
	} 
	
	class User_admin extends SQL_post{	
		var $module = 'Users';
		var $sql_table = "users";
		var $xsl_source = "/Modules/Admin/admin.xsl";
		var $kontakt_id = '';
		
		function get_request_data(){
			parent::get_request_data();
			if(isset($this->datain['passwd']) && ($this->datain['passwd'] == '' || $this->datain['passwd'] == null)) unset($this->datain['passwd']);
			if(isset($this->datain['passwd'])) $this->datain['passwd'] = md5($this->datain['passwd']);
		}
		
		function update_form(){
			return parent::update_form(array('passwd'));
		}
		
		function insert_form (){
			$temp = parent::insert_form();
			return str_replace("</action>","<hidden name=\"kontakt_id\">" . $this->kontakt_id . "</hidden></action>",$temp);
		}
		
		function sql_insert(){
			if (parent::sql_insert()) {
				$user_id = mysql_insert_id($this->sql_connected);
				$result = mysql_query("UPDATE kontakti SET user_id='$user_id' WHERE id='$this->kontakt_id'",$this->sql_connected);
			}else{
				return false;
			}
			return true;
		}
		
		function sql_delete(){
			if (parent::sql_delete()) {
				$result = mysql_query("UPDATE kontakti SET user_id=null WHERE user_id='".$this->datain['id']."'",$this->sql_connected);
			}else{
				return false;
			}
			return true;
		}
		
		function User_admin($metod = null, $id = false) {
			$this->metod = $metod;
			if ($id !== false) $this->datain['id']=$id;
			$this->check_access();
	   	}
	}

	class Groups extends SQL_get{	
		var $module = 'Groups';
		var $xsl_source = "/Modules/Admin/admin.xsl";
		
		function Groups() {
			$this->check_access();
			$this->SQL_get("SELECT * FROM user_groups","group");
	   	}
	} 

	class Groups_admin extends SQL_post{	
		var $module = 'Groups';
		var $sql_table = "user_groups";
		var $xsl_source = "/Modules/Admin/admin.xsl";
		
		function sql_insert(){
			if (parent::sql_insert() != 1) return false;
			$result = mysql_query("SELECT group_name FROM user_groups",$this->sql_connected);
			$temp = Array();
			while ($row = @mysql_fetch_array($result, MYSQL_ASSOC)) array_push($temp,"'$row[group_name]'");		
			$result = mysql_query("ALTER TABLE users CHANGE user_group user_group SET(".implode(",",$temp).") DEFAULT 'gost'",$this->sql_connected);
			return true;
		}

		function sql_delete(){
			if (parent::sql_delete() != 1) return false;
			$result = mysql_query("SELECT group_name FROM user_groups",$this->sql_connected);
			$temp = Array();
			while ($row = @mysql_fetch_array($result, MYSQL_ASSOC)) array_push($temp,"'$row[group_name]'");		
			$result = mysql_query(	"ALTER TABLE users CHANGE user_group user_group SET(".implode(",",$temp).") DEFAULT 'gost'",$this->sql_connected);
			return true;
		}
		
		function Groups_admin($metod = null, $id = false) {
			$this->check_access();	
			$this->metod = $metod;
			if ($id !== false) $this->datain['id']=$id;
			
	   	}
	}


?>