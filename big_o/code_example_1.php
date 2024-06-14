<?php

function foo($n)
{    
    for ($a = 0; $a < $n / 2; $a++) {// n/2 times - O(n)
        echo $a . "\n";
    }

    // O(n*n)
    for ($b = 0; $b < $n; $b++) {// n times - O(n)
        for ($c = 0; $c < $n; $c++) {// n times - O(n)
            echo $b . " , " . $c . "\n";
        }
    }
    
    // finally O(n + n*n) = O(n*n)
}

foo(10);
