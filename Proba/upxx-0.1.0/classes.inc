<?php

if (!eregi("module.php", $_SERVER['PHP_SELF']) && !eregi("index.php", $_SERVER['PHP_SELF'])) {
        die ("You can't access this file directly...");
}

include('passwd.inc');

// Klasa za pristup mysql serveru
class SQL_connection {
	var $sql_host; 
	var $sql_user;
	var $sql_password;
	var $sql_database;
	var $sql_connected;				//Status Konekcije
	var $grant_access = Array("super_user"); 	//Prava pristupa klasi
	var $xml;					//xml output
	var $xsl_source;				//apsolutni link ka xsl fajlu
	var $module;					//naziv modula
	
// provera prava pristupa
	function check_access(){
		global $user;
		if (in_array("gost", $this->grant_access))	return true;			
		if (!is_array($user->data['user_group'])) {
			exit('not array');
		}
		if (!in_array("super_user", $user->data['user_group']) && (count(@array_intersect($this->grant_access, $user->data['user_group'])) < 1)) exit();
	}
	
// konektovanje na bazu
	function sql_connect(){
		if (!($link =@ mysql_pconnect($this->sql_host, $this->sql_user, $this->sql_password))) {
			$this->error_loging("Could not connect: " , mysql_error());
			return false;
		}
		if (!(mysql_select_db($this->sql_database,$link))){
			$this->error_loging('Can\'t use $this->sql_database : ' , mysql_error());
			return false;
		}
		$this->sql_connected = $link;
		return true;
	}
	
// logovanje gresaka u log file (pogledaj passwd.inc)
	function error_loging($error,$sql = ''){
		$filename = $GLOBALS['log_file'];
		if (is_writable($filename)) {
			if (!$handle = fopen($filename, 'a')) {
         			exit;
    			}
			if (!fwrite($handle, "$error \n $sql \n")) {
        			exit;
    			}
    			fclose($handle);
		}
	}				
	
// parsiranje xmla i xsla na serveru
	function parse($xml_source, $xsl_source){
/*		$arguments = array(
		     	'/_xml' => $xml_source,
	     		'/_xsl' => $xsl_source,
		);
		$xh = xslt_create();
		// Process the document
		$result = xslt_process($xh, 'arg:/_xml', 'arg:/_xsl', NULL, $arguments); */
//		xslt_free($xh);
		return "<?xml version=\"1.0\" encoding=\"ISO-8859-1\"?><?xml-stylesheet type=\"text/xsl\" href=\"$xsl_source\"?><suhart>" .$xml_source ."</suhart>";
	}
/*	
	function get_html (){
      	 	return $this->parse($this->xml, $this->xsl_source);
	}
*/	
// konstruktor dodeljuje globalne parametre i konektuje se na bazu
	function SQL_connection(){
		$this->sql_host = $GLOBALS['my_host'];
		$this->sql_user = $GLOBALS['my_user'];
		$this->sql_password = $GLOBALS['my_password'];
		$this->sql_database = $GLOBALS['my_database'];
		$this->sql_connect();
	}
}			
	
// SQL_get class, klasa za uzimanje podataka iz baze
class SQL_get extends SQL_connection{
	var $data; 				// SQL result data holder
		
// izvrsava sql komandu i rezultat trpa u $this->data array, rezultati se smestaju kao 
// data[0][ime],data[0][id],data[1][name]....

	function sql_get_data($sql){
		if ($result = mysql_query($sql)){
			$i = 0;
			while ($i < mysql_num_fields($result)) {
			    $meta[$i] = mysql_field_flags($result,$i);
			    $i++;
			}
			$i=0;		
			while ($row = @mysql_fetch_array($result, MYSQL_ASSOC)) {
				reset($row);
				$j=0;
				foreach ($row as $k => $v) {
					
			    		if (strpos($meta[$j],'set')=== false) {
			    			$this->data[$i][$k]=$v;
			    		}else{
			    			$this->data[$i][$k] = explode(",",$v);
			    		}
			    		$j++;
				}
				$i++;
			}
			return true;
		}else{
			 $this->error_loging("Invalid query: " . mysql_error(), $sql);
			 return false;
		}
	}

// pravi genericki xml od $this->data
	function xml_set($main_tag=''){
		if ($this->data) {
			$this->xml = '';
			if ($main_tag=='') $main_tag = get_class($this);
			foreach ($this->data as $k => $v) {
				$this->xml .= "<" . $main_tag . ">\n";
					foreach ($v as $k1 => $v1) {
    					if (strpos($k1,'datum')!==false){	
							$v1 = preg_replace ("/(19|20)(\d{2})-(\d{1,2})-(\d{1,2})/", "\\4/\\3/\\1\\2", $v1);
						}
						if (is_array($v1)){
    						foreach ($v1 as $v2) $this->xml .= ("\t<$k1>$v2</$k1>\n");
    					}elseif (is_a($v1,"sql_get")) {
    						
    						$this->xml .= $v1->xml;
    					}else{
    						$this->xml .= "\t<$k1>$v1</$k1>\n";
    					}
				}
				$this->xml .= "</" . $main_tag . ">";
			}
		}
	}
	
// konstruktor
	function SQL_get($sql=NULL,$main_tag=''){
		$this->SQL_connection();
		if ($sql && $this->sql_connected) {
			//$this->error_loging("query: " . mysql_error(), $sql);
			$this->sql_get_data($sql);
			$this->xml_set($main_tag);
		}
	}
}

// klasa za ubacivanje podataka u tabelu

class SQL_post extends SQL_get{
	var $sql_table;				// radna tabela
	var $data_flags;			// podaci o poljima: ime, maxlen, not null
	var $datain;				// podaci za ubacivanje u tabelu
	var $metod;				// metod rada: insert, insert_confirm, update, update_confirm, delete, delete_confirm
	var $fali;				// spisak polja koja su not_null a nisu uneta(setovana)


	function get_request_data(){
		if (!$this->data_flags) $this->sql_get_data_flags();
		foreach ($this->data_flags as $k => $v){
			if (isset($_REQUEST['frm_'.$k])) {
				if (is_array($_REQUEST['frm_'.$k])) {
					$this->datain[$k] = implode(",",$_REQUEST['frm_'.$k]);
				}else{
					$this->datain[$k] = nl2br($_REQUEST['frm_'.$k]);
				}
			}
		}
	}
	
// SQL update fja
	function sql_modify(){
		if (!$this->datain['id']) return FALSE;
		if ($this->check_data()===TRUE){
			$sql = "UPDATE $this->sql_table SET ";
			$temp ='';
    			foreach ($this->datain as $k => $v) {
				if ($k!=='id'){
					if (!get_magic_quotes_gpc()) $v = addslashes($v);
					if ($v!='null'){$temp .= $k . "='$v' ,";}
				}
			}
			$sql .= rtrim($temp,",") . "WHERE id = '".$this->datain['id']."'";
			if (!($result = mysql_query($sql,$this->sql_connected))) {
				$this->error_loging("Invalid query: " . mysql_error() . $sql);
				return false;
			}
		}else{
				return false;
		}
		return true;
	}

// SQL insert fja
	function sql_insert(){
		if (!$this->check_keys()) return FALSE;
		if ($this->check_data()===TRUE){
			$sql = "INSERT INTO $this->sql_table ";
			$key_temp ='';
			$value_temp ='';
    			foreach ($this->datain as $k => $v) {
				if ($k!=='id'){
					$key_temp.=$k.',';
					if (!get_magic_quotes_gpc()) $v = addslashes($v);
					if ($v!='null') {$value_temp.="'$v',";}
					else {$value_temp.="null,";}
				}
			}
			$sql .= "(id," . rtrim($key_temp,",") . ") VALUES(NULL," . rtrim($value_temp,",") .")";
			$this->error_loging("query: " . $sql);
			if (!($result = mysql_query($sql,$this->sql_connected))) {
				echo mysql_error() . $sql;
				$this->error_loging("Invalid query: " . mysql_error() . $sql);
				return false;
			}
		}else{
			return false;
		}
		return true;
	}			
	
// SQL delete fja
	function sql_delete(){
		if ($this->datain['id']){
			if (!($result = mysql_query("DELETE FROM $this->sql_table WHERE id='".$this->datain['id']."'",$this->sql_connected))) {
				$this->error_loging("Invalid query: " . mysql_error());
				return false;
			}
			return true;
		}
		return false;
	}
		
// ucitava osobine polja tabela
	function sql_get_data_flags(){
		$fields = mysql_query("SHOW fields FROM $this->sql_table");
		while ($row = @mysql_fetch_array($fields, MYSQL_ASSOC)) {
			$this->data_flags[$row['Field']]['not_null'] = ($row['Null']=='YES') ? FALSE : TRUE;
			if ((strpos($row['Type'], 'set')!== false) || (strpos($row['Type'], 'enum')!== false)){
				$temp = $row['Type'];
				$temp = trim(strstr($temp, '('),"()");
				$this->data_flags[$row['Field']]['set'] = explode(',',str_replace("'","",$temp));
			}
			preg_match("/^(\w*)\(([\S|,]*)\)/",$row['Type'], $matches);
			switch ($row['Type']){
				case "text":
					$this->data_flags[$row['Field']]['type'] = "text";
					$this->data_flags[$row['Field']]['len'] = 65535;
					break;
				case "date":
					$this->data_flags[$row['Field']]['type'] = "date";
					$this->data_flags[$row['Field']]['len'] = 10;
					break;
				default:
					$this->data_flags[$row['Field']]['type'] = $matches[1];
					$this->data_flags[$row['Field']]['len'] = array_sum(explode(",",$matches[2]));
					break;
			}
		}			
	}							

// proverava da li su sva neophodna polja uneta
	function check_data(){
		if (!$this->data_flags) $this->sql_get_data_flags();
		reset($this->datain);
		$i = 0;
		foreach ($this->datain as $k => $v) {
		    if ($k !='id' && ($v == 'null' || $v == null) &&  $this->data_flags[$k]['not_null']){
		    	$this->fali[$i] = $k;
		    	$i++;
		    }			    	
		}
		if (is_array($this->fali)) {
/*				$filename = "g:/xampp/htdocs/access.log";
			$handle = fopen($filename, 'a');
			foreach ($fali as $k => $v) fwrite($handle, "fali :" . $v);
			fclose($handle); */			
			return FALSE;
		}else{
			return TRUE;
		}
	}	

// proverava da nema polja u datain koja nepostoje u tabeli
	function check_keys(){
		if (!$this->data_flags) $this->sql_get_data_flags();
		reset($this->datain);
		foreach ($this->datain as $k => $v) {
			if (!array_key_exists($k, $this->data_flags)) return FALSE;			    	
		}
		$this->error_loging("keys: ");
		return TRUE;
	}

// izvrsava metod
	function execute(){
		$this->SQL_connection();
		switch ($this->metod){
			case 'delete':
				$this->xml = $this->delete_form();
				break;
			case 'delete_confirm':
				$this->xml = $this->delete_form_check();
				break;
			case 'insert':
				$this->get_request_data();
				$this->xml = $this->insert_form();
				break;
			case 'insert_confirm':
				$this->get_request_data();
				$this->xml = $this->insert_form_check();
				break;
			case 'update':
				$this->xml = $this->update_form();
				break;
			case 'update_confirm':
				$this->get_request_data();
				$this->xml = $this->update_form_check();
				break;
		}	
	}				

// Form handle
// greska o neophodnim poljima
	function error_message(){
		return implode(",",$this->fali);
	}
	
// xml forma za update
	function update_form($no_value = array()) {
		if ($this->datain['id']){
			$this->SQL_get("SELECT * FROM $this->sql_table WHERE id ='".$this->datain['id']."'");
			if (!$this->data_flags) $this->sql_get_data_flags();
			$temp = "<action type=\"update_confirm\" id=\"".$this->data[0]['id']."\" module=\"$this->module\">";
			foreach ($this->data[0] as $k => $v) {
				if (isset($this->datain[$k])) $v = $this->datain[$k];
				$temp .="<data_input name=\"$k\" len=\"" . $this->data_flags[$k]['len'] . "\"";
				if ($this->data_flags[$k]['type']=='enum' || $this->data_flags[$k]['type']=='set'){
					$temp .=" type=\"".$this->data_flags[$k]['type']."\">";
					foreach ($this->data_flags[$k]['set'] as $k1 => $v1) {
						$temp .="<item";
						if (!is_array($v)) $v = explode(",",$v);
						if (in_array($v1,$v)) $temp .=" selected=\"true\"";
						$temp .=">$v1</item>";
					}
				}else{
					$temp .=">";
					if (!in_array($k,$no_value)) $temp .="<value>" . str_replace("<br />","\n",$v) . "</value>";
				}
				$temp .="</data_input>";	
			}
			$temp .="</action>";
			return $temp;
		}
	}
	
// provera updatetovanih podataka
	function update_form_check (){
		if (!$this->sql_modify()){
			return @$this->error_message . $this->update_form();
		}
		header("Location:module.php?module=$this->module");
	}
			
// provera insertovanih podataka
	function insert_form_check (){
		if (!$this->sql_insert()){
			return @$this->error_message . $this->insert_form();
		}
		header("Location:module.php?module=$this->module");
	}
	
// xml insert forma
	function insert_form (){
		$temp = "<action type=\"insert_confirm\" module=\"$this->module\">";
		if (!$this->data_flags) $this->sql_get_data_flags();
		foreach ($this->data_flags as $k => $v) {
			$temp .="<data_input name=\"$k\" len=\"$v[len]\"";
			if ($v['type']=='enum' || $v['type']=='set'){
				$temp .=" type=\"$v[type]\">";
				foreach ($v['set'] as $k1 => $v1) {
					$temp .="<item";
					if (@in_array($v1,explode(",",$this->datain[$k]))) $temp .=" selected=\"true\"";
					$temp .=">$v1</item>";
				}
				$temp .="</data_input>";	
			}else{
				$temp .="><value>" . str_replace("<br />","\n",@$this->datain[$k]) . "</value></data_input>";			    	
			}
		}
		$temp .="</action>";
		return $temp;
	}		
	
// potvrda brisanja podataka
	function delete_form(){
		if ($this->datain['id']) 
		return "<action type=\"delete_confirm\" id=\"".$this->datain['id']."\" module=\"$this->module\"><message>Brisanje podataka</message></action>";
	}
	
// brisanje podataka
	function delete_form_check(){
		if (isset($this->datain['id'])) $this->sql_delete();
		header("Location:module.php?module=$this->module");
	}

}

	class SQL_complex extends SQL_get {
		
		function extend_data($att,$pt,$class,$metod,$main_tag = null){
/* 	$att - attribute to add
	$pt  - what value to pass to
	$class - what class to call
	$metod - what metod to call, null = constructor
	$main_tag - main xml tag
*/ 
			@reset ($this->data);
			$i = 0;
			while (@$this->data[$i]) {
				if ($metod == null) {
					$this->data[$i][$att] = new $class($this->data[$i][$pt]);
				}else{
					$this->data[$i][$att] = new $class();
					$this->data[$i][$att]->$metod($this->data[$i][$pt]);
				}
				$i++;
			}
			$this->xml_set($main_tag);
			return true;
		}
	}

	
// klasa za ucitanje podataka o useru
class User extends SQL_get{	
	var $grant_access = Array("super_user","gost");
	
	function User($username = null, $passwd = null) {
		if (($username != null) && ($passwd != null)){
			$this->SQL_get("SELECT * FROM users WHERE username = '$username' AND passwd = '$passwd'");
			$this->data = $this->data[0];
		}
   	}
} 
?>