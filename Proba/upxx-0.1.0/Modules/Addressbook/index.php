<?php

	if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
	        die ("You can't access this file directly...");
	}

	$grant_access = Array("super_user");
	check_access();
	
	class Addressbook extends SQL_get{	
		var $module = 'Addressbook';
		var $xsl_source = "/Modules/Addressbook/kontakt.xsl";
		
		function Addressbook() {
			$this->check_access();
			$this->SQL_get("SELECT id, first, last FROM kontakti","kontakt");
			$this->xml = "<addressbook>" . $this->xml . "</addressbook>";
	   	}
	} 
	
	class Kontakt extends SQL_get{	
		var $module = 'Kontakt';
		var $xsl_source = "/Modules/Addressbook/kontakt.xsl";
		
		function Kontakt($id = null,$op) {
			$this->check_access();
			if ($id == null) header("Location:module.php?module=Addressbook");
			$sql = "SELECT id,first,last,middle,title,user_id";
			switch ($op){
				case "home":
				$sql .= ",home_street_addrese,home_city,home_state,home_zip_code,home_country,home_web_page,home_phone,home_fax,home_mobile";
				break;
				case "business":
				$sql .= ",business_company,business_job_title,business_department,business_office,business_street_addrese,business_city,business_state,business_zip_code,business_country,business_web_page,business_phone,business_fax,business_mobile,business_fix_ip";
				break;
				case "personal":
				$sql .= ",personal_spouse,personal_children,personal_gender,personal_birthday,personal_annaversary,note";
				break;
				case "messinger":
				$sql .= ",messinger_netmeeting_server,messinger_netmeeting_address,messinger_icq,messinger_yahoo,messinger_msn,messinger_skype,pgp_digital_id";
				break;
				default:
				$sql .= ",email1,email2,email3";
				break;
			}
			$sql .= " FROM kontakti WHERE id = $id";
			$this->SQL_get($sql,"kontakt");
	   	}
	} 
	
	class Kontakt_admin extends SQL_post{	
		var $module = 'Kontakt';
		var $sql_table = "kontakti";
		var $xsl_source = "/Modules/Addressbook/kontakt.xsl";
		
		function insert_form (){
			if (!$this->data_flags) $this->sql_get_data_flags();
			unset($this->data_flags['user_id']);
			return parent::insert_form();
		}		
		
		function Kontakt_admin($metod = null, $id = false) {
			$this->metod = $metod;
			if ($id !== false) {
				$this->datain['id']=$id;
			}else{
				header("Location=module.php?module=Addressbook");
			}
			$this->check_access();
	   	}
	}
?>