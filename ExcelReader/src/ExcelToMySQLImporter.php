<?php

namespace ExcelReader;

use ExcelReader\Database\MySQLConnection;
use ExcelReader\Excel\ExcelReader;

class ExcelToMySQLImporter
{
    private ExcelReader $excelReader;
    private MySQLConnection $dbConnection;
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'db_host' => 'localhost',
            'db_name' => 'excel_data',
            'db_user' => 'root',
            'db_pass' => '',
            'db_port' => 3306,
            'create_table' => true,
            'auto_detect_types' => true,
            'batch_size' => 1000
        ], $config);

        $this->dbConnection = new MySQLConnection(
            $this->config['db_host'],
            $this->config['db_name'],
            $this->config['db_user'],
            $this->config['db_pass'],
            $this->config['db_port']
        );
    }

    public function importFromFile(
        string $filePath,
        string $tableName,
        string $sheetName = null,
        int $headerRow = 1,
        array $columnTypes = []
    ): array {
        try {
            // Initialize Excel reader
            $this->excelReader = new ExcelReader($filePath);
            $this->excelReader->loadFile();

            // Read data from Excel
            $data = $this->excelReader->readSheetDataWithHeaders($sheetName, $headerRow);
            
            if (empty($data)) {
                return [
                    'success' => false,
                    'message' => 'No data found in the Excel file',
                    'rows_processed' => 0
                ];
            }

            // Determine column types if not provided
            if (empty($columnTypes) && $this->config['auto_detect_types']) {
                $columnTypes = $this->excelReader->getColumnTypes($data);
            }

            // Create table if needed
            if ($this->config['create_table']) {
                $this->dbConnection->createTable($tableName, $columnTypes);
            }

            // Insert data in batches
            $totalRows = count($data);
            $processedRows = 0;
            $batches = array_chunk($data, $this->config['batch_size']);

            foreach ($batches as $batch) {
                $this->dbConnection->insertData($tableName, $batch);
                $processedRows += count($batch);
            }

            // Clean up
            $this->excelReader->close();
            $this->dbConnection->close();

            return [
                'success' => true,
                'message' => "Successfully imported {$processedRows} rows to table '{$tableName}'",
                'rows_processed' => $processedRows,
                'table_name' => $tableName,
                'column_types' => $columnTypes
            ];

        } catch (\Exception $e) {
            // Clean up on error
            if (isset($this->excelReader)) {
                $this->excelReader->close();
            }
            if (isset($this->dbConnection)) {
                $this->dbConnection->close();
            }

            return [
                'success' => false,
                'message' => 'Import failed: ' . $e->getMessage(),
                'rows_processed' => 0
            ];
        }
    }

    public function getSheetInfo(string $filePath): array
    {
        try {
            $this->excelReader = new ExcelReader($filePath);
            $this->excelReader->loadFile();

            $sheets = $this->excelReader->getSheetNames();
            $sheetInfo = [];

            foreach ($sheets as $sheetName) {
                $sheet = $this->excelReader->getSheetByName($sheetName);
                $highestRow = $sheet->getHighestRow();
                $highestColumn = $sheet->getHighestColumn();

                $sheetInfo[] = [
                    'name' => $sheetName,
                    'rows' => $highestRow,
                    'columns' => $highestColumn,
                    'is_active' => $sheetName === $this->excelReader->getActiveSheet()->getTitle()
                ];
            }

            $this->excelReader->close();

            return [
                'success' => true,
                'sheets' => $sheetInfo
            ];

        } catch (\Exception $e) {
            if (isset($this->excelReader)) {
                $this->excelReader->close();
            }

            return [
                'success' => false,
                'message' => 'Failed to get sheet info: ' . $e->getMessage()
            ];
        }
    }

    public function previewData(string $filePath, string $sheetName = null, int $limit = 10): array
    {
        try {
            $this->excelReader = new ExcelReader($filePath);
            $this->excelReader->loadFile();

            $data = $this->excelReader->readSheetDataWithHeaders($sheetName);
            $preview = array_slice($data, 0, $limit);

            $this->excelReader->close();

            return [
                'success' => true,
                'preview' => $preview,
                'total_rows' => count($data),
                'preview_rows' => count($preview)
            ];

        } catch (\Exception $e) {
            if (isset($this->excelReader)) {
                $this->excelReader->close();
            }

            return [
                'success' => false,
                'message' => 'Failed to preview data: ' . $e->getMessage()
            ];
        }
    }

    public function setConfig(array $config): void
    {
        $this->config = array_merge($this->config, $config);
    }

    public function getConfig(): array
    {
        return $this->config;
    }
} 