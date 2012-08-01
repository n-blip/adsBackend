<?php
	
	error_reporting(E_ALL);
	
	define('DB_USER', 'b23e08cd756bd8');
	define('DB_PASSWORD', '07a735e2');
	define('DB_HOST', 'us-cdbr-east.cleardb.com');
	define('DB_NAME', 'heroku_60507ed4873ed71');
	
	echo('this is the index - NEW echo');
	
	$sql = 'SELECT * FROM test';
	
	$conn = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	mysql_select_db(DB_NAME);
	$result = mysql_query($sql,$conn);
	$info = mysql_fetch_array($result);
	
	echo('<pre>');
	print_r($info);
	echo('</pre>');
	
	if (isset($_GET['populate']) && ($_GET['populate'] == true)) {
		$sql = 'INSERT INTO twittersearch (searchTerm, searchResults) VALUES ("sample", "this is search results one")';
		$result = mysql_query($sql, $conn);
		echo('population done <br/>');
	}
	
	//twitter search table schema
	//create table twitterSearch (
		//recno int(11) not null auto_increment, 
		//searchTerm varchar(100), 
		//searchResults text, 
		//lastResult TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, 
		//primary key(recno)
	//);
	
	$sql = 'SELECT * FROM twitterSearch';
	$result = mysql_query($sql,$conn);
	$info = mysql_fetch_array($result);
	
	echo('<br />');
	echo('<pre>');
	echo('------------------------------<br />');
	print_r($info);
	echo('</pre>');
	
?>