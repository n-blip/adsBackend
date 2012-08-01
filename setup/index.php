<?php
	$resource = fopen('../includes/testfile.php', 'W');
	
	$data = 'this is a test of the writing';
	
	fwrite($resource, $data);
	
	fclose($resource);
?>