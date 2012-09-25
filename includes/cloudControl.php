<?php
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
	
	
	$login = $creds['MYSQLS']['MYSQLS_USERNAME'];
	$pass = $creds['MYSQLS']['MYSQLS_PASSWORD'];
	
	$server = $creds['MYSQLS']['MYSQLS_HOSTNAME'];
	
	$database = $creds['MYSQLS']['MYSQLS_DATABASE'];
	
	if (isset($_GET['testDBdetails']) && ($_GET['testDBdetails'] == TRUE) ) {
	
		echo('creds-------------------------------------------<br />');
		echo('<pre>');
		print_r($creds);
		echo('</pre>');
		echo('/creds-------------------------------------------<br /><br />');
	
		echo('login = ' .$login .'<br />');
		echo('pass = ' .$pass .'<br />');
		echo('server = ' .$server .'<br />');
		echo('database = ' .$database .'<br />');
		
		
		echo('ENV-------------------------------------------<br />');
		echo('<pre>');
		print_r($_ENV);
		echo('</pre>');
		echo('/ENV-------------------------------------------<br /><br />');
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
	die('DB details not specified, please make sure the db addon is installed');
}
?>