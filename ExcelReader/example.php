<?php

require_once 'vendor/autoload.php';

use ExcelReader\ExcelToMySQLImporter;

// Configuration
$config = [
    'db_host' => 'localhost',
    'db_name' => 'excel_data',
    'db_user' => 'root',
    'db_pass' => '', // Set your MySQL password here
    'db_port' => 3306,
    'create_table' => true,
    'auto_detect_types' => true,
    'batch_size' => 1000
];

// Initialize the importer
$importer = new ExcelToMySQLImporter($config);

// Example 1: Basic import
echo "=== Example 1: Basic Import ===\n";
$result = $importer->importFromFile(
    'sample_data.xlsx',  // Your Excel file path
    'employees',         // Table name
    'Sheet1',           // Sheet name (optional)
    1                   // Header row number
);

if ($result['success']) {
    echo "✅ " . $result['message'] . "\n";
    echo "Rows processed: " . $result['rows_processed'] . "\n";
    echo "Table created: " . $result['table_name'] . "\n";
} else {
    echo "❌ " . $result['message'] . "\n";
}

echo "\n";

// Example 2: Get sheet information
echo "=== Example 2: Sheet Information ===\n";
$sheetInfo = $importer->getSheetInfo('sample_data.xlsx');

if ($sheetInfo['success']) {
    echo "📊 Sheets found:\n";
    foreach ($sheetInfo['sheets'] as $sheet) {
        $status = $sheet['is_active'] ? ' (Active)' : '';
        echo "  - {$sheet['name']}: {$sheet['rows']} rows × {$sheet['columns']} columns{$status}\n";
    }
} else {
    echo "❌ " . $sheetInfo['message'] . "\n";
}

echo "\n";

// Example 3: Preview data before import
echo "=== Example 3: Data Preview ===\n";
$preview = $importer->previewData('sample_data.xlsx', 'Sheet1', 5);

if ($preview['success']) {
    echo "👀 Preview of first 5 rows:\n";
    foreach ($preview['preview'] as $index => $row) {
        echo "Row " . ($index + 1) . ": " . json_encode($row) . "\n";
    }
    echo "Total rows in file: " . $preview['total_rows'] . "\n";
} else {
    echo "❌ " . $preview['message'] . "\n";
}

echo "\n";

// Example 4: Import with custom column types
echo "=== Example 4: Custom Column Types ===\n";
$customTypes = [
    'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
    'name' => 'VARCHAR(100)',
    'email' => 'VARCHAR(255)',
    'salary' => 'DECIMAL(10,2)',
    'hire_date' => 'DATE',
    'department' => 'VARCHAR(50)'
];

$result = $importer->importFromFile(
    'sample_data.xlsx',
    'employees_custom',
    'Sheet1',
    1,
    $customTypes
);

if ($result['success']) {
    echo "✅ " . $result['message'] . "\n";
} else {
    echo "❌ " . $result['message'] . "\n";
}

echo "\n";

// Example 5: Error handling
echo "=== Example 5: Error Handling ===\n";
$result = $importer->importFromFile(
    'non_existent_file.xlsx',  // This file doesn't exist
    'test_table'
);

if ($result['success']) {
    echo "✅ " . $result['message'] . "\n";
} else {
    echo "❌ " . $result['message'] . "\n";
} 