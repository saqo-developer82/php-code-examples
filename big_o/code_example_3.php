<?php

function foo($n)
{
    // O(3*n) = O(n)
    for ($b = 0; $b < 3; $b++) {// 3 times - O(3)
        boo($n);// n times - O(n)
    }

    for ($a = 0; $a < 10000; $a++) {// 10000 times - O(10000) = O(1)
        echo $a . "\n";
    }
    
    // finally O(n + 1) = O(n)
}

function boo($m)
{
    for ($c = 0; $c < $m; $c++) {// m times - O(m)
        echo $c . "\n";
    }
}

foo(10);
