<?php
	define('DB_USER', 'b23e08cd756bd8');
	define('DB_PASSWORD', '07a735e2');
	define('DB_HOST', 'us-cdbr-east.cleardb.com');
	define('DB_NAME', 'heroku_60507ed4873ed71');
	
	define('CONN', mysql_connect(DB_HOST, DB_USER, DB_PASSWORD));
	
	//$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_NAME);
?>