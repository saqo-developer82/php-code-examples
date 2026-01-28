<?php

function sortItemsByName(array $items): array
{
    usort($items, function ($a, $b) {
        return strcmp($a['name'], $b['name']);
    });

    foreach ($items as &$item) {
        if (isset($item['item']) && is_array($item['item'])) {
            $item['item'] = sortItemsByName($item['item']); // recursive sort
        }
    }

    return $items;
}

$inputFile = __DIR__ . '/postman_collection.json';     // your input JSON file
$outputFile = __DIR__ . '/postman_sorted.json';   // destination file for sorted JSON

// Step 1: Load and decode the input file
$json = file_get_contents($inputFile);
$data = json_decode($json, true);

// Step 2: Recursively sort items by name
if (isset($data['item']) && is_array($data['item'])) {
    $data['item'] = sortItemsByName($data['item']);
}

// Step 3: Save sorted JSON to a new file
file_put_contents($outputFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

echo "✅ Sorted collection saved to {$outputFile}\n";
