<?php

function modified_since_header($modTime) {
	if ( isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && (strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) >= $modTime) ) {
		// Client's cache is current, respond '304 Not Modified'
		header( 'Last-Modified: ' . gmdate('D, d M Y H:i:s', $modTime) . ' GMT', true, 304 );
		return false;
	} else {
		// File not cached or cache outdated, respond '200 OK'
		header( 'Last-Modified: ' . gmdate('D, d M Y H:i:s', $modTime) . ' GMT', true, 200 );
		return true;
	}
}

?>