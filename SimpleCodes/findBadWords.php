<?php

function findBadWords($text) {
    // List of bad words
    $badWords = array("bad", "inappropriate", "offensive");

    // Convert text to lowercase for case-insensitive matching
    $lowercaseText = strtolower($text);

    // Initialize an array to store found bad words
    $foundBadWords = array();

    // Check if each bad word exists in the text
    foreach ($badWords as $word) {
        if (strpos($lowercaseText, $word) !== false) {
            $foundBadWords[] = $word;
        }
    }

    // Return the found bad words
    return $foundBadWords;
}

// Example usage
$text = "This is a bad example sentence.";
$foundWords = findBadWords($text);

if (count($foundWords) > 0) {
    echo "The following bad words were found: " . implode(", ", $foundWords);
} else {
    echo "No bad words were found.";
}
?>
