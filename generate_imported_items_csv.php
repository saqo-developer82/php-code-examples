<?php
declare(strict_types=1);

$outputFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'imported_items.csv';

$fileHandle = fopen($outputFilePath, 'w');
if ($fileHandle === false) {
    fwrite(STDERR, "Failed to open file for writing: {$outputFilePath}\n");
    exit(1);
}

for ($i = 1; $i <= 2000; $i++) {
    $text = "Imported Item #{$i}";
    fputcsv($fileHandle, [$text]);
}

fclose($fileHandle);

echo "Wrote 2000 rows to {$outputFilePath}\n";
?>


