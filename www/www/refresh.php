<?php

$urls = array(
	'http://www.danhopewell.com',
	'http://www.danhopewell.com/resume',
	'http://www.danhopewell.com/colophon'
);

function curl($f)
{
	$ch = curl_init($f);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_exec($ch);
	curl_close($ch);
}

foreach ($urls as $url) {
	curl($url);
}

?>
