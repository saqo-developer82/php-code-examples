<?php

function zoom($n)
{
    if ($n < 1) {
        echo "finish \n";
        return;
    }

    echo "$n \n";
    zoom($n - 2);
    // n/2 times - O(n)
}
// time complecsity O(n)
//  space complecsity O(n)
zoom(10);
//10
//8
//6
//4 ...
