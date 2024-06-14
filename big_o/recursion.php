<?php

function zoom($n)
{
    if ($n <= 0) {
        echo "finish \n";
        return;
    }

    echo "$n \n";
    zoom($n - 1);
    // n times - O(n)
}

// time complecsity O(n)
//  space complecsity O(n)
zoom(10);
//10
//9
//8
//7 ...
