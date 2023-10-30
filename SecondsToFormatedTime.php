<?php

function convertToFormatedTime($seconds)
{
    return gmdate("H:i:s", $seconds);
}

echo convertToFormatedTime(22242);
