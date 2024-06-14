<?php

// Array of numbers
$numbers = [2, 4, 6, 8, 10, 12, 14, 16, 18, 20];

// Start time
$start_time = microtime(true);

// Calculate sum using a for loop
$sum = 0;
$count = count($numbers);
for ($i = 0; $i < $count; $i++) {
    $sum += $numbers[$i];
}

// End time
$end_time = microtime(true);

// Execution time
$execution_time = ($end_time - $start_time) * 1000; // in milliseconds

// Output sum and execution time
echo "Sum: $sum\n";
echo "Execution Time: $execution_time milliseconds\n";
