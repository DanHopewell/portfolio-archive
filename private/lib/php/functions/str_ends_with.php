<?php

function str_ends_with($haystack, $needle)
{
    if (strlen($needle) == 0) {
        return true;
    }
    return substr($haystack, -strlen($needle)) === $needle;
}

?>