<?php
	foreach ($_GET as $secvalue) {
	    if ((eregi("<[^>]*script*\"?[^>]*>", $secvalue)) ||
		(eregi("<[^>]*object*\"?[^>]*>", $secvalue)) ||
		(eregi("<[^>]*iframe*\"?[^>]*>", $secvalue)) ||
		(eregi("<[^>]*applet*\"?[^>]*>", $secvalue)) ||
		(eregi("<[^>]*meta*\"?[^>]*>", $secvalue)) ||
		(eregi("<[^>]*style*\"?[^>]*>", $secvalue)) ||
		(eregi("<[^>]*form*\"?[^>]*>", $secvalue)) ||
		(eregi("\([^>]*\"?[^)]*\)", $secvalue)) ||
		(eregi("\"", $secvalue))) {
	   die ("<b>The html tags you attempted to use are not allowed</b><br>");
	    }
	}
	
	foreach ($_POST as $secvalue) {
	    if ((@eregi("<[^>]script*\"?[^>]*>", $secvalue)) ||	(@eregi("<[^>]style*\"?[^>]*>", $secvalue))) {
	        die ("<b>The html tags you attempted to use are not allowed</b>");
	    }
	}
	


	function check_access(){
		global $user,$grant_access;
		if (!is_array($user->data['user_group'])) {
			exit('Pristup odbijen!');
		}
		if (!in_array("super_user", $user->data['user_group']) && (count(@array_intersect($grant_access, $user->data['user_group'])) < 1)) exit('Pristup odbijen!');
	}
	
	include ('my_sess.php');
	
	$my_session = new my_sess();
	
	if (isset($_COOKIE['sess_id'])){
		$my_session->sess_get($_COOKIE['sess_id'],'tmp_user');
	}

	
	include('classes.inc');
	
	$user = new User($_MY_SESS['tmp_user']['username'],$_MY_SESS['tmp_user']['passwd']); 
	
	if (!isset($user->data['id'])) header("Location:index.php");

// Skara uradi sessione za kontrolu pristupa

	if (isset($_REQUEST['module'])){
		switch ($_REQUEST['module']){
			case "Software":
				include("Modules/Software/index.php");
				switch (@$_REQUEST['op']){
					case "MyLicence":
						$main = new Sw_my_licence(@$_REQUEST['id']);
						break;	
					case "MojaPitanja":
						$main = new Sw_my_pitanja(@$_REQUEST['id']);
						break;	
					case "SwSupport":
						if (isset($_REQUEST['id'])) $main = new Software(@$_REQUEST['id'],'support');
						break;
					case "Support":
						if (isset($_REQUEST['id'])) $main = new Sw_support(@$_REQUEST['id']);
						break;
					case "Odgovor":
						if (isset($_REQUEST['id'])) $main = new Sw_support(@$_REQUEST['id'],'odgovor');
						break;
					case "Download":
						$main = new Sw_download(intval(@$_REQUEST['id']));
						break;
					default:
						$main = new Software(intval(@$_REQUEST['id']));
						break;
				}
				break;
			default:
				$casedir = dir("case");
				while($func=$casedir->read()) {
				    if(substr($func, 0, 5) == "case.") {
					include($casedir->path."/$func");
				    }
				}
				closedir($casedir->handle);
				break; 
		}

		if (!isset($main)){
			include("Modules/FrontPage/index.php");
			$main = new Vesti_teme();
		}

		header("Content-type: text/xml; charset=UTF-8");
		$temp =	"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		if (@$main->xsl_source) {
			$temp .= "<?xml-stylesheet type=\"text/xsl\" href=\"$main->xsl_source\"?>";
		}else{
			$temp .= "<?xml-stylesheet type=\"text/xsl\" href=\"main.xsl\"?>";
		}
		$temp .= "<main>".@$main->xml."</main>";
		echo $temp;
/*		$filename = (@$main->xsl_source) ? $main->xsl_source : "main.xsl";
		$filename = $_SERVER['DOCUMENT_ROOT'] . $filename;
		$handle = fopen($filename, "r");
		$xsl_source = fread($handle, filesize($filename));
		fclose($handle);

		$arguments = array(
		     	'/_xml' => $temp,
	     		'/_xsl' => $xsl_source,
		);
		$xh = xslt_create();

		$result = xslt_process($xh, 'arg:/_xml', 'arg:/_xsl', NULL, $arguments);
		echo $result;
		xslt_free($xh);*/
	}else{
		header("Location:index.php");
	}
?>	