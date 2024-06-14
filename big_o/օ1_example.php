<?php

function foo($n)
{
    $result = 0;

    for ($a = 0; $a < 5; $a++) {// 5 times - O(5)
        $result += $n;
    }
    
    return $result;
    
    // finally O(5) = O(1)
}

foo(10); // 50
