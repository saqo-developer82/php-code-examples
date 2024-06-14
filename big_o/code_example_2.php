<?php

function foo($n)
{
    // O(3*n) = O(n)
    for ($b = 0; $b < 3; $b++) {// 3 times - O(3)
        for ($c = 0; $c < $n; $c++) {// n times - O(n)
            echo $b . " , " . $c . "\n";
        }
    }

    for ($a = 0; $a < 10000; $a++) {// 10000 times - O(10000) = O(1)
        echo $a . "\n";
    }
    
    // finally O(n + 1) = O(n)
}

foo(10);
