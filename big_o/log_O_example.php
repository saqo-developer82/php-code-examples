<?php

function foo($n)
{
    while ($n > 1) {
        echo $n . "\n";
        $n /= 2;
    }

    // finally O(log(n))
}

foo(10);
