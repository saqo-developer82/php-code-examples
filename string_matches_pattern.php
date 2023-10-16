<?php
/*
 * Have the function StringChallenge(str) read str which will contain two strings separated by a space. The first string will consist of the following sets of characters: +, *, $, and {N} which is optional. The plus (+) character represents a single alphabetic character, the ($) character represents a number between 1-9, and the asterisk (*) represents a sequence of the same character of length 3 unless it is followed by {N} which represents how many characters should appear in the sequence where N will be at least 1. Your goal is to determine if the second string exactly matches the pattern of the first string in the input.
For example: if str is "++*{5} jtggggg" then the second string in this case does match the pattern, so your program should return the string true. If the second string does not match the pattern your program should return the string false.
 */

function StringChallenge($str) {
    list($pattern, $string) = explode(' ', $str);

    $pattern = str_replace(['$', '+'], ['[1-9]', '[a-zA-Z]'], $pattern);
    $pattern = preg_replace('/\*\{/', '(.){', $pattern);
    $pattern = str_replace('*', '(.){3}', $pattern);

    if(preg_match('/^' . $pattern . '$/', $string, $matches)) {
        return 'true';
    }

    return 'false';

}

// keep this function call here
$str = "++*{5} jtggggg";
echo StringChallenge($str);
