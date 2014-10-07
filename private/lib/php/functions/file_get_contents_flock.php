<?php

function file_get_contents_flock($filename)
{
	$return = false;
	
	if ( ($fp = @fopen($filename, 'r')) ) {
		if (flock($fp, LOCK_SH)) {
			if ( is_bool($read = @file_get_contents($filename)) === false ) {
				$return = $read;
			}
			flock($fp, LOCK_UN);
		}
		fclose($fp);
	}
	return $return;

}

?>