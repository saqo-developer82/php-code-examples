<?php

namespace ExcelReader\Excel;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelReader
{
    private Spreadsheet $spreadsheet;
    private string $filePath;

    public function __construct(string $filePath)
    {
        $this->filePath = $filePath;
        $this->validateFile();
    }

    private function validateFile(): void
    {
        if (!file_exists($this->filePath)) {
            throw new \Exception("Excel file not found: {$this->filePath}");
        }

        $extension = strtolower(pathinfo($this->filePath, PATHINFO_EXTENSION));
        if (!in_array($extension, ['xlsx', 'xls'])) {
            throw new \Exception("Unsupported file format. Only XLSX and XLS files are supported.");
        }
    }

    public function loadFile(): void
    {
        try {
            $this->spreadsheet = IOFactory::load($this->filePath);
        } catch (\Exception $e) {
            throw new \Exception("Failed to load Excel file: " . $e->getMessage());
        }
    }

    public function getSheetNames(): array
    {
        return $this->spreadsheet->getSheetNames();
    }

    public function getActiveSheet(): Worksheet
    {
        return $this->spreadsheet->getActiveSheet();
    }

    public function getSheetByName(string $sheetName): ?Worksheet
    {
        return $this->spreadsheet->getSheetByName($sheetName);
    }

    public function readSheetData(string $sheetName = null, int $startRow = 1, int $endRow = null): array
    {
        $sheet = $sheetName ? $this->getSheetByName($sheetName) : $this->getActiveSheet();
        
        if (!$sheet) {
            throw new \Exception("Sheet '{$sheetName}' not found.");
        }

        $highestRow = $endRow ?? $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        $data = [];
        
        for ($row = $startRow; $row <= $highestRow; $row++) {
            $rowData = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                $rowData[] = $cellValue;
            }
            $data[] = $rowData;
        }

        return $data;
    }

    public function readSheetDataWithHeaders(string $sheetName = null, int $headerRow = 1): array
    {
        $sheet = $sheetName ? $this->getSheetByName($sheetName) : $this->getActiveSheet();
        
        if (!$sheet) {
            throw new \Exception("Sheet '{$sheetName}' not found.");
        }

        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        // Read headers
        $headers = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $headerValue = $sheet->getCellByColumnAndRow($col, $headerRow)->getValue();
            $headers[] = $this->sanitizeColumnName($headerValue);
        }

        // Read data
        $data = [];
        for ($row = $headerRow + 1; $row <= $highestRow; $row++) {
            $rowData = [];
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellValue = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                $rowData[$headers[$col - 1]] = $cellValue;
            }
            $data[] = $rowData;
        }

        return $data;
    }

    private function sanitizeColumnName(string $columnName): string
    {
        // Remove special characters and replace spaces with underscores
        $sanitized = preg_replace('/[^a-zA-Z0-9_\s]/', '', $columnName);
        $sanitized = preg_replace('/\s+/', '_', trim($sanitized));
        
        // Ensure it starts with a letter or underscore
        if (!preg_match('/^[a-zA-Z_]/', $sanitized)) {
            $sanitized = 'col_' . $sanitized;
        }
        
        return strtolower($sanitized);
    }

    public function getColumnTypes(array $data): array
    {
        if (empty($data)) {
            return [];
        }

        $columns = array_keys($data[0]);
        $types = [];

        foreach ($columns as $column) {
            $type = $this->determineColumnType($data, $column);
            $types[$column] = $type;
        }

        return $types;
    }

    private function determineColumnType(array $data, string $column): string
    {
        $hasString = false;
        $hasNumber = false;
        $hasDate = false;
        $maxLength = 0;

        foreach ($data as $row) {
            $value = $row[$column];
            
            if (is_null($value) || $value === '') {
                continue;
            }

            if (is_numeric($value)) {
                $hasNumber = true;
            } elseif (is_string($value)) {
                $hasString = true;
                $maxLength = max($maxLength, strlen($value));
                
                // Check if it's a date
                if (strtotime($value) !== false) {
                    $hasDate = true;
                }
            }
        }

        if ($hasDate && !$hasNumber) {
            return 'DATE';
        } elseif ($hasNumber && !$hasString) {
            return 'DECIMAL(10,2)';
        } else {
            if ($maxLength > 255) {
                return 'TEXT';
            } else {
                return "VARCHAR({$maxLength})";
            }
        }
    }

    public function close(): void
    {
        if (isset($this->spreadsheet)) {
            $this->spreadsheet->disconnectWorksheets();
            unset($this->spreadsheet);
        }
    }
} 