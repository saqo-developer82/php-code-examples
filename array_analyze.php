<?php
function analyzeArray($numbers) {
    if (empty($numbers)) {
        echo "Array is empty. Please provide an array of numbers.\n";
        return;
    }

    // Use array_map to apply is_numeric to each element in the array
    $numericChecks = array_map('is_numeric', $numbers);

    // If all elements are numeric, array_product will return 1
    if (array_product($numericChecks) !== 1) {
        echo "Array should contain only numeric values. Please provide an array of numbers.\n";
        return;
    }

    // Calculate minimum and maximum
    $min = min($numbers);
    $max = max($numbers);

    // Calculate the number of odd numbers
    $oddNumbersCnt = array_reduce($numbers, function($cnt, $number) {
        return $number % 2 !== 0 ? ++$cnt : $cnt;
    }, 0);

    // Display the results
    echo "Minimum number is: $min\n";
    echo "Maximum number is: $max\n";
    echo "Number of Odd Numbers: $oddNumbersCnt\n";
}

// Example usage:
//$numbers = [];
//$numbers = [1, 2, 3, '4', 5.5];
$numbers = [1, 'two', 3, 'four', 5];
analyzeArray($numbers);
