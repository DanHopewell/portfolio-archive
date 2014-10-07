<?php

function widont($str = '')
{
	$str = rtrim($str);
	$space = strrpos($str, ' ');
	if ( ($space !== false) && (str_word_count($str) > 3) )
	{
		$str = substr($str, 0, $space).'&nbsp;'.substr($str, $space + 1);
	}
	return $str;
}

?>