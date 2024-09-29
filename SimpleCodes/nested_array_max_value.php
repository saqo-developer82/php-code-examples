<?php
declare(strict_types=1);

/**
 * Finds and returns the maximum integer value in a nested array.
 *
 * @param array $xs The array to search for the maximum integer value.
 * @return int The maximum integer value found in the nested array.
 */
function my_max(array $xs): int
{
    $max = PHP_INT_MIN; // Initialize with the smallest possible integer

    // Define a recursive function to process nested arrays
    $findMaxValue = function($array) use (&$max, &$findMaxValue) {
        foreach ($array as $value) {
            if (is_array($value)) {
                $findMaxValue($value);
            } elseif(is_int($value) && $value > $max) {
                $max = $value;
            }
        }
    };

    $findMaxValue($xs);

    return $max;
}

echo my_max([1, [2, 3]]) . "\n"; // 3
echo my_max([10, [1,2, [3,11, 100, 100.5, [4, [20,78]]]], [5,6]]) . "\n"; // 100
