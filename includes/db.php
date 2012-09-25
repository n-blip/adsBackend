<?php
	if (isset($_ENV['HTTP_HOST']) && strstr($_ENV['HTTP_HOST'], 'cloudcontrolled.com') != FALSE) {
		include('../includes/cloudControl.php');
	}
	else {
		include('../includes/heroku.php');
	}
?>