<?php
	foreach( @_SERVER as  $key => $value ) {
		echo('key ' .$key ' is set to ' .$value .'<br />');
	}
?>