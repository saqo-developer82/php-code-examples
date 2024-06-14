<?php

function unique($arr)
{
    $newArr = [];
    $count = count($arr); // O(n)
    for ($i = 0; $i < $count; $i++) { // O(1)
        $num = $arr[$i];
        if (!in_array($arr[$i], $newArr)) { // O(n)
            $newArr[] = $arr[$i];
        }
    }
    
    return $newArr;
}

// time complecsity O(n)
//  space complecsity O(n)
// Array of numbers
$numbers = [1,1,2,3,3,4,5,6,6,6,7];
unique($numbers);
