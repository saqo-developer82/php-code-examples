<?php

function zoom($n)
{
    if ($n == 1) {
        echo "finish \n";
        return;
    }
    zoom($n - 1);
    zoom($n - 1);
}

// time complecsity O(2^n)
zoom(4);
