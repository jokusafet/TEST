<?php
	if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
	        die ("You can't access this file directly...");
	}
 
	switch ($_REQUEST['module']){
		case "Users":
			include("Modules/Admin/index.php");
			if (isset($_REQUEST['metod'])){					
				$main = new User_admin($_REQUEST['metod'],@$_REQUEST['id']);
				if (($_REQUEST['metod'] == 'insert' || $_REQUEST['metod'] == 'insert_confirm') && isset($_REQUEST['frm_kontakt_id'])) $main->kontakt_id = intval($_REQUEST['frm_kontakt_id']);
				$main->execute();
			}else{
				$main = new Users(@$_REQUEST['id']);
			}
			break;
		case "Groups":			
			include("Modules/Admin/index.php");
			if (isset($_REQUEST['metod'])){
				$main = new Groups_admin($_REQUEST['metod'],@$_REQUEST['id']);
				$main->execute();
			}else{
				$main = new Groups();
			}
			break;
	}
?>