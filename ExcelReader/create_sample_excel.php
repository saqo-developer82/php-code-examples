<?php

require_once 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();

// Get the active sheet
$sheet = $spreadsheet->getActiveSheet();

// Set headers
$headers = ['ID', 'Name', 'Email', 'Department', 'Salary', 'Hire Date'];
$col = 1;
foreach ($headers as $header) {
    $sheet->setCellValueByColumnAndRow($col, 1, $header);
    $col++;
}

// Sample data
$data = [
    [1, 'John Doe', 'john.doe@company.com', 'Engineering', 75000, '2023-01-15'],
    [2, 'Jane Smith', 'jane.smith@company.com', 'Marketing', 65000, '2023-02-20'],
    [3, 'Bob Johnson', 'bob.johnson@company.com', 'Sales', 70000, '2023-03-10'],
    [4, 'Alice Brown', 'alice.brown@company.com', 'HR', 60000, '2023-04-05'],
    [5, 'Charlie Wilson', 'charlie.wilson@company.com', 'Engineering', 80000, '2023-05-12'],
    [6, 'Diana Davis', 'diana.davis@company.com', 'Finance', 72000, '2023-06-18'],
    [7, 'Edward Miller', 'edward.miller@company.com', 'IT', 78000, '2023-07-22'],
    [8, 'Fiona Garcia', 'fiona.garcia@company.com', 'Marketing', 68000, '2023-08-30'],
    [9, 'George Martinez', 'george.martinez@company.com', 'Sales', 73000, '2023-09-14'],
    [10, 'Helen Taylor', 'helen.taylor@company.com', 'Engineering', 76000, '2023-10-25']
];

// Add data rows
$row = 2;
foreach ($data as $rowData) {
    $col = 1;
    foreach ($rowData as $value) {
        $sheet->setCellValueByColumnAndRow($col, $row, $value);
        $col++;
    }
    $row++;
}

// Auto-size columns
foreach (range(1, count($headers)) as $col) {
    $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
}

// Create the Excel file
$writer = new Xlsx($spreadsheet);
$writer->save('sample_data.xlsx');

echo "✅ Sample Excel file 'sample_data.xlsx' created successfully!\n";
echo "📊 File contains " . count($data) . " rows of employee data.\n";
echo "📋 Columns: " . implode(', ', $headers) . "\n";
echo "\nYou can now test the importer with this file.\n"; 