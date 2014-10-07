<?php

function str_lreplace($n, $r, $h) {
	if($p = strrpos($h, $n)) {
	    $h = substr_replace($h, $r, $p, strlen($n));
	}
    return $h;
}

?>