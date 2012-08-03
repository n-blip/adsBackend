<?php

if(	isset($_ENV['CLEARDB_DATABASE_URL']) && 
	($_ENV['CLEARDB_DATABASE_URL']!= '') && 
	(strstr($_ENV['CLEARDB_DATABASE_URL'], 'mysql://') != FALSE)) {
	echo('in the if true statement <br />');
	
	//parse out the heroku details for cleardb
	$dbDetails = substr($_ENV['CLEARDB_DATABASE_URL'], 7);
	
	echo('dbdetails -----------------------------------<br />');
	echo('<pre>');
		print_r($dbDetails);
	echo('</pre>');
	
	$portions = explode($dbDetails, '@');
	
	echo('portions -----------------------------------<br />');
	echo('<pre>');
		print_r($portions);
	echo('</pre>');
	
	$loginPass = explode($portions[0], ':');
	
	echo('loginPass -----------------------------------<br />');
	echo('<pre>');
		print_r($loginPass);
	echo('</pre>');
	
	$login = $loginPass[0];
	$pass = $loginPass[1];
	
	$server = $portions[1];

	echo('login = ' .$login .'<br />');
	echo('pass = ' .$pass .'<br />');
	echo('server = ' .$server .'<br />');
	

	define('DB_USER', 'b23e08cd756bd8');
	define('DB_PASSWORD', '07a735e2');
	define('DB_HOST', 'us-cdbr-east.cleardb.com');
	define('DB_NAME', 'heroku_60507ed4873ed71');
	
	define('CONN', mysql_connect(DB_HOST, DB_USER, DB_PASSWORD));
	
	//$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_NAME);

}
else {
	die('clearDb details not specified, please make sure this addon is installed');
}
?>