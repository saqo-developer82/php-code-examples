<?php

function zoom($n)
{
    if ($n == 1) {
        echo "finish \n";
        return;
    }
    for ($i = 0; $i < $n; $i++) {
        zoom($n - 1);
    }
}

// time complecsity O(n!)
zoom(4);
