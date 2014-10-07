<?php

# Ampersand-encoding based entirely on Nat Irons's Amputator
# MT plugin: <http://bumppo.net/projects/amputator/>
# Pilfered via http://michelf.com/projects/php-markdown/

function amputator($text) {
	return preg_replace('/&(?!#?[xX]?(?:[0-9a-fA-F]+|\w+);)/','&amp;', $text);
}

?>