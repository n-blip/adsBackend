<?php
	define('DB_USER', 'root');
	define('DB_PASSWORD', 'yoshim00');
	define('DB_HOST', 'localhost');
	define('DB_NAME', 'herokuTest');
	
	define('CONN', mysql_connect(DB_HOST, DB_USER, DB_PASSWORD));
	
	//$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_NAME);
?>