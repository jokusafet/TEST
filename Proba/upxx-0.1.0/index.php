<?php
include ('my_sess.php');
include ('classes.inc');
$my_session = new my_sess();

if (isset($_COOKIE['sess_id'])){
	$my_session->sess_get($_COOKIE['sess_id'],'tmp_user');
	$user = new User($tmp_user['username'],$tmp_user['passwd']); 
	if (isset($user->data['id'])){
		header("Location:module.php?module=none");
	}
}
if ($_REQUEST['Submit']){
	$user = new User($_REQUEST['username'],md5($_REQUEST['passwd'])); 
	if (isset($user->data['id'])){
		$tmp_user['username'] = $_REQUEST['username'];
		$tmp_user['passwd'] = md5($_REQUEST['passwd']);
		$my_session->sess_put("tmp_user", $tmp_user);
		setcookie ('sess_id', $sess_key);
		header("Location:module.php?module=none");
	}else{
		header("Location:index.php");
	}
}else{
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title>Dental IS Tehnicka podrska </title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
</head>

<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<table width="100%" height="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td><div align="center">
        <form name="form1" method="post" action="">
          <table width="30%" border="0" cellspacing="0" cellpadding="2">
            <tr> 
              <td><div align="right">Korisniè ime:</div></td>
              <td><input name="username" type="text" id="username" maxlength="16"></td>
            </tr>
            <tr>
              <td><div align="right">šifra:</div></td>
              <td><input name="passwd" type="password" id="passwd" maxlength="15"></td>
            </tr>
            <tr> 
              <td><div align="right"></div></td>
              <td><input type="submit" name="Submit" value="Prijava"></td>
            </tr>
          </table>
        </form>
      </div></td>
  </tr>
</table>
</body>
</html>
<?php
}
?>