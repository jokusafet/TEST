<?php
	$grant_access = Array("gost","dentalis");
	if ((in_array("super_user", $user->data['user_group'])) || (count(array_intersect($grant_access, $user->data['user_group'])) > 0)){ 
		phpinfo();
	}
?>