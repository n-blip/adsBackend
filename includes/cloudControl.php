<?php
//note for nothing
echo('environment-------------------------------------------<br />');
echo('<pre>');
	print_r($_ENV);
echo('</pre>');
echo('/environment-------------------------------------------<br />');


if(	isset($_ENV['CRED_FILE']) && ($_ENV['CRED_FILE']!= '')) {
	
	$string = file_get_contents($_ENV['CRED_FILE'], false);
	if ($string == false) {
    	die('FATAL: Could not read credentials file');
	}

	# the file contains a JSON string, decode it and return an associative array
	$creds = json_decode($string, true);

	# use credentials to set the configuration for your Add-on
	# replace ADDON_NAME and PARAMETER with Add-on specific values
	$config = array(
    	'VAR1_NAME' => $creds['ADDON_NAME']['PARAMETER_1'],
	    'VAR2_NAME' => $creds['ADDON_NAME']['PARAMETER_2'],
    	'VAR3_NAME' => $creds['ADDON_NAME']['PARAMETER_3'],
	# e.g. for MYSQLS: 'MYSQLS_HOSTNAME' => $creds['MYSQLS']['MYSQLS_HOSTNAME'],
);
	
echo('creds-------------------------------------------<br />');
	echo('<pre>');
	print_r($creds);
	echo('</pre>');
echo('/creds-------------------------------------------<br />');
	die();
	die();
	
	
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
	
	
	
	if (isset($_GET['testDBdetails']) && ($_GET['testDBdetails'] == TRUE) ) {
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