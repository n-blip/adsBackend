<?php

if(	isset($_ENV['CLEARDB_DATABASE_URL']) && 
	($_ENV['CLEARDB_DATABASE_URL']!= '') && 
	(strstr($_ENV['CLEARDB_DATABASE_URL'], 'mysql://') != FALSE)) {
	
	//parse out the heroku details for cleardb
	$dbDetails = substr($_ENV['CLEARDB_DATABASE_URL'], 8);
	
	$portions = explode('@', $dbDetails);
	
	$loginPass = explode(':', $portions[0]);
	
	$login = $loginPass[0];
	$pass = $loginPass[1];
	
	$serverPortions = explode('/', $portions[1]);
	$server = $serverPortions[0];
	
	$databasePortion = explode('?', $serverPortions[1]);
	$database = $databasePortion[0];
	
	
	
	if (isset($_GET[testDBdetails]) && ($_GET[testDBdetails] == TRUE) ) {
		echo('full server url = ' .$_ENV['CLEARDB_DATABASE_URL'] .'<br />');
		echo('login = ' .$login .'<br />');
		echo('pass = ' .$pass .'<br />');
		echo('server = ' .$server .'<br />');
		echo('database = ' .$database .'<br />');
	}
	
	

	define('DB_USER', $login);
	define('DB_PASSWORD', $pass);
	define('DB_HOST', $server);
	define('DB_NAME', $database);
	
	define('CONN', mysql_connect(DB_HOST, DB_USER, DB_PASSWORD));
	
	//$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_NAME);

}
else {
	die('clearDb details not specified, please make sure the clear db addon is installed');
}
?>