<?php

function str_freplace($n, $r, $h) {
	if($p = strpos($h, $n)) {
	    $h = substr_replace($h, $r, $p, strlen($n));
	}
    return $h;
}

?>