<?php

require_once 'vendor/autoload.php';

use ExcelReader\ExcelToMySQLImporter;

// ========================================
// CONFIGURATION - EDIT THESE VALUES
// ========================================

$config = [
    'db_host' => 'localhost',      // MySQL host
    'db_name' => 'excel_data',     // Database name
    'db_user' => 'root',           // MySQL username
    'db_pass' => '',               // MySQL password (set your password here)
    'db_port' => 3306,            // MySQL port
    'create_table' => true,        // Create table if it doesn't exist
    'auto_detect_types' => true,   // Automatically detect column types
    'batch_size' => 1000          // Number of rows to process in each batch
];

$excelFile = 'data.xlsx';         // Path to your Excel file
$tableName = 'imported_data';     // Name of the table to create/use
$sheetName = null;                // Sheet name (null = active sheet)
$headerRow = 1;                   // Row number containing headers

// ========================================
// SCRIPT EXECUTION
// ========================================

try {
    echo "🚀 Starting Excel to MySQL import...\n";
    echo "File: {$excelFile}\n";
    echo "Table: {$tableName}\n";
    echo "Database: {$config['db_name']}\n\n";

    // Initialize importer
    $importer = new ExcelToMySQLImporter($config);

    // Get sheet information
    echo "📊 Analyzing Excel file...\n";
    $sheetInfo = $importer->getSheetInfo($excelFile);
    
    if (!$sheetInfo['success']) {
        throw new Exception($sheetInfo['message']);
    }

    foreach ($sheetInfo['sheets'] as $sheet) {
        $status = $sheet['is_active'] ? ' (Active)' : '';
        echo "  - {$sheet['name']}: {$sheet['rows']} rows × {$sheet['columns']} columns{$status}\n";
    }

    // Preview data
    echo "\n👀 Previewing data...\n";
    $preview = $importer->previewData($excelFile, $sheetName, 3);
    
    if (!$preview['success']) {
        throw new Exception($preview['message']);
    }

    foreach ($preview['preview'] as $index => $row) {
        echo "Row " . ($index + 1) . ": " . json_encode($row) . "\n";
    }
    echo "Total rows to import: " . $preview['total_rows'] . "\n";

    // Perform import
    echo "\n📥 Importing data to MySQL...\n";
    $result = $importer->importFromFile($excelFile, $tableName, $sheetName, $headerRow);

    if ($result['success']) {
        echo "✅ SUCCESS!\n";
        echo "Message: " . $result['message'] . "\n";
        echo "Rows processed: " . $result['rows_processed'] . "\n";
        echo "Table name: " . $result['table_name'] . "\n";
        
        if (isset($result['column_types'])) {
            echo "\n📋 Column types detected:\n";
            foreach ($result['column_types'] as $column => $type) {
                echo "  - {$column}: {$type}\n";
            }
        }
    } else {
        echo "❌ FAILED!\n";
        echo "Error: " . $result['message'] . "\n";
    }

} catch (Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n🎉 Import process completed!\n"; 