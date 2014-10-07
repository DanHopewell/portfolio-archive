<?php

$files = array(
	'ie8.css'
);

require_once('loader.php');
$url = new Dh_UrlHelper();

function file_curl($f)
{
	$ch = curl_init($f);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$result = curl_exec($ch);
	curl_close($ch);

	return $result;
}

$images = array();
foreach ($files as $file) {
	$f = $url->css($file, 'str');
	$css = file_curl($f);
	preg_match_all("/url\(\"?([^()]+)\"?\)?/i", $css, $images);
}
$images = array_unique($images[1]);

if (count($images) > 0) {
	header("Content-type: application/javascript");
	echo '(function(){', PHP_EOL;
	echo '    var images = [];', PHP_EOL;
	foreach ($images as $key => $image) {
		echo "    images[$key] = new Image();", PHP_EOL;
		echo "    images[$key].src = $image;", PHP_EOL;
	}
	echo '})();';
}

?>