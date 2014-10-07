<?php

function php_get_contents($filename)
{
	$return = false;

	ob_start();
	
	@include($filename);

	$return .= ob_get_contents();
	ob_end_clean();

	return $return;

}

?>