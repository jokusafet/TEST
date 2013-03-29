<?php
	if (!eregi("module.php", $_SERVER['PHP_SELF'])) {
	        die ("You can't access this file directly...");
	}
 
	switch ($_REQUEST['module']){
		case "Addressbook":
			include("Modules/Addressbook/index.php");
			$main = new Addressbook();
			break;
		case "Kontakt":
			include("Modules/Addressbook/index.php");
			if (isset($_REQUEST['metod'])){
				$main = new Kontakt_admin($_REQUEST['metod'],@$_REQUEST['id']);
				$main->execute();
			}else{
				@$main = new Kontakt(intval($_REQUEST['id']),$_REQUEST['op']);
			}
			break;
	}
?>