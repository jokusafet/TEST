<?

/* ******************************************************

(c) Eye Am Alive, 2002. Roman Yaker.
This software is available under the conditions of the
LGPL (opensource/proprietary) public license.
Use at will.  Please see LICENSE for more info.


------------------------------------------------------
RANT:

Herein are stand-alone, mysql-connected
session handling functions. I decided to write this
because of the way PHP implements their
sessions, as in via cookie-based header-sending. 

Unfortunately, you can't always use this method.
For example, using include files will cause you to do
a "head-over-heels" caused by strict header use. 
I'm sure there are other reasons you might find to use 
this for session handling. If not.

Anyway, which ever the case on your britches, here they are:

------------------------------------------------------
BUGS:

EXPLANATION of bug system: the first number is the respective
			function in its respective order that the bug
			belongs to.
			The second number is the bug number.
			

1) Bug-2.001 - original concept was to have customizable
	global variables. You get to name the variable you want
	to use as the global array that has all the other session
	variables....
	Unfortunately, the $$blah concept is not (purposely?) advanced 
	enough to use with array capabilities--in the sense of:
	would have worked if: $($blah)[0]=3
	instead, it reads: $($blah[0])=3. Where $blah holds var's name as string.
	Obviously this type of assignment is erroneous for the concept at hand.
	This feature will have to wait until something new is figured out,
	or otherwise, will unfortunately never exist, unless PHP Team decided
	to have this changed. Not up to me.
	
=================================================================

*/

include("my_sess.config.inc"); // CONFIGURE THIS FILE (for things to work)!

mysql_pconnect($host,$user,$pass); // will bind to already-opened connection if one exists



Class my_sess {
		

	function sess_put ($varname, $var, $this_sess_key=4){			
		
	
			// -----------------------------------------------------------
			// VAR ASSOCIATION BEGIN -- my_sess::sess_put()
			
				global $sess_db;
				global $sess_tbl;
				global $errrept;
				global $sess_key;
			
				// expire set to time()+7h
				$exp = $expire = (time()+25200); // current time + 7h
		
			
				// if session key exists, s =1
				$s = 1;
			
				
				// create sess_key and s = 0
				if ($this_sess_key == 4 ){
					$sess_key = my_sess::sess_mk_key();
					$s = 0;
				}
				
		
				
				// if $var != array, t=0; ("t" for var [t]ype)
				$t=0;
				
				// $var == array; serialize(var), t = 1
				if (is_array($var)){
					$var = serialize($var);
					$t = 1;
				}
			
			// ------------------------------------------------------------
			// END VAR ASSOCIATION
			
			
			
			
			// ------------------------------------------------------------
			//  if s==1 :: **VAR CHECK** -- my_sess::sess_put()
			// ("exists(var)" in db), if so, sql will update, rather than insert
			
			// if var exists, xv = 1
			
			
				$xv = 0; // var !exists
							
				if ($s=="1"){
					$sql  = "SELECT varname ";
					$sql .= "FROM $sess_db.$sess_tbl ";
					$sql .= "WHERE varname = '$varname' ";
					$sql .= "AND sess_key = '$sess_key'";
					
					if(($q = mysql_query($sql))==false){
						echo "MySQL query failed.";
						echo "<br>";
						echo "Unable to continue";
						if ($errrept == 1){
							echo "<br>";
							echo "<b>Lines: 113-137</b>";
							echo "<br>";
							echo "<b>Using Statement:</b> $sql";
						}
					}
					
					$n = mysql_num_rows($q);
					
					if ($n>0){
						
						$xv = 1; // var exists! (update, !insert)!
					}
				}
			
			// -------------------------------------------------------------
			// END VAR CHECK
			
			
			// -------------------------------------------------------------------------
			// INSERT SESSION DATA
			
				// -----------------------------
				// if new variable
					if ($xv == 0){ 
						$sql  = "INSERT INTO $sess_db.$sess_tbl ";
						$sql .= "(sess_key, var, varname, exp, vartype) ";
						$sql .= "values('$sess_key', '$var', '$varname', '$expire', '$t')";
						
						
						// query [works||doesn't]
						if (mysql_query($sql)==true){
							$err=0;
							$errt = "i";
						}
						// report error
						else {
							echo "Inserting New_Var[$varname] for Sess_ID[$sess_key] failed.";
							if ($errrept==1){
								echo "<br>";
								echo "<b>Lines: 148-168</b>";
							}
							exit;	
						}
					}
					
				// ------------------------------
				// if updating variable
					if ($xv == 1 && $s==1){ // s should always be 1 here--regardless
						$sql  = "UPDATE $sess_db.$sess_tbl ";
						$sql .= "SET var = '$var', exp = '$exp', vartype = '$t', ";
						$sql .= " WHERE varname = '$varname' AND sess_key = '$sess_key'";
						
						if (mysql_query($sql)==true){
							$err=0;
							$errt = "u";
						}
						else {
							echo "Updated Var[$varname] for Sess_ID[$sess_key] failed.";
							if ($errrept==1){
								echo "<br>";
								echo "<b>Lines 172-188</b>";
							}
						}
					}
		// END my_sess::sess_put()			
	} 
	
	
	
	
	
	
	
	
	function sess_get ($this_sess_key, $varname=4) {
		
			global $sess_db;
			global $sess_tbl;
			global $errrept;
			global $_MY_SESS;
			global $sess_key;
			//global $$sessvarname;  //concept doesn't work [Bug-2.001]
			// ($sess_key) scope is local

		// ------------------------------------------------------				
		// MULTIPLE CHECKS to make sure everything's cool....
			// check if key exists in db, and selects exp	
				$sql  = "SELECT exp ";
				$sql .= "FROM $sess_db.$sess_tbl ";
				$sql .= "WHERE sess_key='$this_sess_key' ";
				$sql .= "GROUP BY sess_key ORDER BY exp DESC";
				
				if (($q = mysql_query($sql))==false){
					echo "MySQL error.";
					if ($errrept==1){
						echo "<br>";
						echo "Unable to query MYSQL";
						echo "<br>";
						echo "Lines: 217 - 228";
						echo "<br>";
						echo "<b>Using statement:</b> $sql";
					}
					exit;
				}
				
			// check availability of pre-existing concurrect session key
				$q2 = $q;
				if ( mysql_num_rows($q2)==0 ){
					echo "Session does not exist.";
					if ($errrept==1){
						echo "<br>";
						echo "<b>Lines:</b> 232-241";
						echo "<br>";
						echo "The session key $sess_key does not exist";
					}
					exit;
				}
				unset($q2);
				

			// ok-thus, check if key has expired
				$res = mysql_fetch_array($q);
				$exphorizon = time();
				if ($res['exp']<($exphorizon)){
					
					echo "Session has expired";
					if ($errrept == 1){
						echo "<br>";
						echo "It's been more than 7 hours since the last ";
						echo "<br>";
						echo "instance of $sess_key was used.";
						echo "<br>";
						echo "exiting...";
						echo "<br>";
						echo "<b>Lines:</b> 238-250";
					}
					
				// clean up garbage, and you're ready to go!
					my_sess::sess_clean($sess_key);
					
					exit;
					
				}
						
					
			 // sess_key passed?	// this bit should never be called
			if ($this_sess_key==""){
				echo "<b>No Session Key (ID) found!</b>";
				echo "<br>";
				echo "Exiting....";
				if ($errrept==1){
					echo "<br>";
					echo "You must pass a sess_key to the sess_get() function!";
					echo "<br>";
					echo "<b>Lines:</b> 271 onward.";
				}
				exit;
			}
			
			unset($res);
			
			if ($varname==4){
			// VAR NOT SUPPLIED; GRAB ALL
			
				
				$sql  = "SELECT var, varname, vartype t ";
				$sql .= "FROM $sess_db.$sess_tbl ";
				$sql .= "WHERE sess_key = '$this_sess_key'";
					
				if(($q = mysql_query($sql))==false){
					
					echo "<b>MySQL Query failed.  Exiting (-1)</b>";
					if ($errrept==1){
						echo "<br>";
						echo "Please check that MySQL tables have been properly created.";
						echo "<br>";
						echo "Also make sure you have the necessary permissions to execute a query.";
						echo "<br>";
						echo "<b>Lines:</b> 294(sql), *298(query)";
						echo "<br>";
						echo "<b>Using statement:</b> $sql";
					}
					exit;
				}
			// --------------------------------------------------------------
			// END CHECKS

				
			// $varname == ""
			
					while (1){
						
					//grab row, one by one, and assign to $_MY_SESS
						$res = mysql_fetch_array($q);
						if ($res==false)
							break;
						
						if ($res['t'] == "1"){// check if var == array
							$ser = $res['var'];
							$unser = unserialize($ser);	
							$_MY_SESS[$res['varname']] = $unser;
						}
						else {
							$_MY_SESS[$res['varname']] = $res['var'];
						}
						
						// end while	
					} 
				unset($res);
				// end if varname=="" ((varname==4))
				
			} 
			
			else { //$sess_key == "__something__"
				
			// check that var exists (where sess_key = $sess_key)
				
				$sql  = "SELECT var, varname, vartype t ";
				$sql .= "FROM $sess_db.$sess_tbl ";
				$sql .= "WHERE varname='$varname' ";
				$sql .= "AND sess_key='$this_sess_key'";
				
				if (($q = mysql_query($sql))==false){
					echo "MySQL error. Will not proceed.";
					if ($errrept==1){
						echo "<br>";
						echo "<b>Lines: </b> 346(sql), 351-358";
						echo "<br>";
						echo "<b>Using statement:</b> $sql";
					}
				}
				
				$q2 = $q;
				
			// verify row existance for var existance
				if (mysql_num_rows($q2)==0){
					echo "Session error. Will not proceed.";
					if ($errrept==1){
						echo "<br>";
						echo "The variable <i>\$\"$varname\"</i> does not exist under session $sess_key";
						echo "<br>";
						echo "The session may have expired.";
						echo "<br>";
						echo "Or $varname was never created/inserted properly";
						echo "<br>";
						echo "Check fields where <b>varname</b> = '$varname' and <b>sess_key</b> = '$sess_key', manually";
						echo "<br>";
						echo "<b>Lines:</b> 348-370";
						echo "<br>";
						echo "<b>Using statement:</b> $sql"; 
					}
					exit;
				}
				unset($q2);
				
				
				$q3 = $q;
				
			// retrieve one variable	
				$res = mysql_fetch_array($q3);
					
				if ($res['t']=="1"){
					$ser = $res['var'];
					$unser = unserialize($ser);
					$_MY_SESS[$varname]=$unser;
				}
				else {
					$_MY_SESS[$varname]=$res['var'];
				}
			//$$sessvarname[$res[$varname]]=$res['var']; // concept doesn't work [Bug-2.001]

				unset($res);
				unset($q3); // just for fun, halfly
				unset($q); // just for fun, halfly
				
			}
		// END my_sess::sess_get()					
		
		//return $$sessvarname; // concept does not work [Bug-2.001]
		$retval = $_MY_SESS;
		return $retval;
	}
	
	
	
	
	function sess_mk_key(){
		
		// Create Session key
		
		global $sess_db;
		global $sess_tbl;
		global $errrept;
		global $_MY_SESS;
		//global $$sessvarname; // concept does not work [Bug-2.001]
		
		while(1){
				
			$key = my_sess::sess_randString();
		
			// check if new key already exists
			
				$sql  = "SELECT id ";
				$sql .= "FROM $sess_db.$sess_tbl ";
				$sql .= "WHERE sess_key = '$key'";
				
				if (($q = mysql_query($sql))==false){
					echo "MySQL Query failed.";
					if ($errrept==1){
						echo "<br>";
						echo "<i>Duplicate key check failed</i>";
						echo "<br>";
						echo "<b>Lines:</b> 431(sql), 435 - 445";
						echo "<br>";
						echo "Using: $sql";
					}
				}
				
				if (mysql_num_rows($q)==0)
					break;
		}
		
		return $key;
				
		// END my_sess::sess_mk_key()		
	}
		
	
	function sess_clean($key=4){
		
		if ($key==4){
			
			global $sess_db;
			global $sess_tbl;
			global $errrept;
			
			$exphorizon = time(); // <= ($exp==(time()+7h))
			// cleans out all sessions whose (up to) first key has expired
			
			$sql  = "DELETE FROM $sess_db.$sess_tbl ";
			$sql .= "WHERE sess_key < $exphorizon ";
						
			if (($q=mysql_query($sql))==false){
				echo "MySQL Query failed.";
				if ($errrept==1){
					echo "<br>";
					echo "Failed to collect garbage";
					echo "<br>";
					echo "<b>Line:</b> 471-481";
					echo "<br>";
					echo "<b>Using statement:</b> $sql";
				}
			}
			
			// end if (!$key)
		}
	
		else {
			
			$sql = "DELETE FROM $sess_db.$sess_tbl WHERE sess_key = $key";
			
			if (mysql_query($sql)==false){
				echo "MySQL Query Failed.";
				if ($errrept==1){
					echo "<br>";
					echo "Unable to delete sess_key[$key]";
					echo "<br>";
					echo "<b>Line:</b> 490";
					echo "<br>";
					echo "Using statement: $sql";
				}
			}
		}

			// END my_sess::sess_clean()
	}
	
	
	
	function sess_randString($length=16){
	
		// The following random string generation algorithm was originally 
		// conceived by Alvaro G. V. It is being used with his permission.
		// Thanks.
		// Certain objects have been modified. Possibly. Probably not.
		
		
		// Generates a random string with the specified length
		// Includes: a-z, A-Z, 0-9
		//
 
			mt_srand((double)microtime()*1000000);
			$newstring="";
		
			if($length>0){
				while(strlen($newstring)<$length){
					switch(mt_rand(1,3)){
						case 1: $newstring.=chr(mt_rand(48,57)); break;  // 0-9
						case 2: $newstring.=chr(mt_rand(65,90)); break;  // A-Z
						case 3: $newstring.=chr(mt_rand(97,122)); break; // a-z
					}
				}
			}
			return ("b".$newstring."z");
			
			// END sess_randString()
		}
	
	
	
}
		


				
		

	