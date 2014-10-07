<?php

function optipng($i, $flags = '-o3')
{	
	$path = '/usr/bin/optipng';
	$dir = '.:/home/username/private/temp';

	if ( is_object($i) ) {
		$obj = TRUE;
	} else {
		$obj = FALSE;
	}

	if (!is_dir($dir)) {
		if ( @mkdir($dir, 0711, true) === false ) {
			return $i;
		}
	}

	$tmp = $dir . uniqid() . '.png';
	$cmd = $path . ' ' . $flags . ' ' . $tmp;

	if ($obj) {
		$i->stripImage();
		if ( $i->writeImage($tmp) === false) {
			return $i;
		}
		$i->clear();
	} else {
		if ( @file_put_contents($tmp, $i, LOCK_EX) === false ) {
			return $i;
		}
		$i = '';
	}

	@shell_exec($cmd);

	if ($obj) {
		$i = Imagick($tmp);
	} else {
		$i = file_get_contents($tmp);
	}

	unlink($tmp);
	return $i;
}


?>
