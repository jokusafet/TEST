<?
/*
MY_SESS.PHP v 1.0.5 CONFIG

(c) Eye Am Alive Works, 2002. Roman Yaker.
Please notice that the software herein can be used
willingly and freely. This software falls under the 
conditions of the LGPL (proprietary) public license.

See LICENSE for more info.







/* MY_SESS.PHP configuration  */

/* Please configure the following items: */

////////////////////////////////////////////////////////////////////
// Error Reporting
// Set $errrept = 1 for maximum built-in error reporting
// You might choose this setting if something's not working, 
// and you are trying to figure out why....
// For minimal reporting, use $errrept = 0;
// This option is recommended when using this class under final
// deployment.
$errrept = 1;

/////////////////////////////////////////////////////////////////////////
// SESS VARIABLE NAME -- THIS SEGMENT IS NOT AVAILABLE DUE TO BUG-2.001
// 
// The following variable is set to the name of the array
// variable that holds all of your session-data/variables 
// If $sessvarname = "foo", your session data will be located
// in the variable called $foo within all ("recursive") scripts.
// Default is $sessvarname = "_MY_SESS";
// Warning: choose a unique name.
// $sessvarname = "_MY_SESS";

////////////////////////////////////////////////////////////////////////////
// ***MYSQL INFORMATION: You _MUST_ have these settings CORRECT!
// if $mconf=1, you can use the following settings to configure your
// mysql connection info. Otherwise, set it to 0, and use your own 
// method. If you choose $mconf=1, you must configure the mysql
// info below. You'll be able to use it with mysql_connect($host,$user,$pass),
// etc.
// You might want to set it to 0, if you have your own method of delivery.
// HOWEVER: the host, user, password, and database name, must all correspond
// to the following variable names, which these classes use!
$mconf=1;
/////////////////////////////////
if ($mconf==1){
	////////////////// MYSQL INFO ///////////////////////////////
// $host = "your_host"
$host = "localhost";
// $user = "user";
// The default is $user="root". This is of the least security nor smart.
$user = "root";
// $pass = "password";
$pass = "";
/////
// Database name
// $db = "db";
// ************************ DO NOT CHANGE THIS
// (unless you know what you're doing)
$sess_db = "epro";
/////
// Table name
// $tbl = "tbl_sess";
// ***if you change this, you must change the real table name in the
// table creation script.
// ************************ DO NOT CHANGE THIS
$sess_tbl = "tbl_sess";
// You might want to clear this out, and include() your own db.information
// file.
}

/* ------------------------------------- */
// CONFIG END
// ------------------------------------- */
?>