<?php

function str_starts_with($haystack, $needle)
{
     $length = strlen($needle);
     return substr($haystack, 0, $length) === $needle;
}

?>