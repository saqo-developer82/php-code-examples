<?php

/**
 * Reverses a given string.
 *
 * @param string $str The input string to be reversed.
 * @return string The reversed string.
 */
function stringReverse($str = '')
{
    $len = strlen($str);
    $res = '';

    if ($len > 0) {
        for ($i = $len - 1; $i >= 0; $i--) {
            $res .= $str[$i];
        }    
    }

    return $res;
}

echo stringReverse('abcdefytytu') . "\n";
