<?php
$arr1 = json_decode(file_get_contents('find_availablity_response.json'), true);
file_put_contents('find_availablity_response_.json', json_encode($arr1, JSON_PRETTY_PRINT));
