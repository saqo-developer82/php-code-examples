# Excel to MySQL Importer

A pure PHP script that reads data from XLSX files and inserts it into MySQL database tables. Built using OOP principles with no framework dependencies.

## Features

- ✅ Read XLSX and XLS files
- ✅ Automatic column type detection
- ✅ Batch processing for large files
- ✅ Multiple sheet support
- ✅ Data preview functionality
- ✅ Error handling and validation
- ✅ Pure PHP with OOP design
- ✅ No framework dependencies

## Requirements

- PHP 7.4 or higher
- MySQL/MariaDB
- Composer

## Installation

1. **Clone or download this project**
   ```bash
   git clone <repository-url>
   cd ExcelReader
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Create MySQL database**
   ```sql
   CREATE DATABASE excel_data;
   ```

## Quick Start

1. **Edit the configuration** in `simple_import.php`:
   ```php
   $config = [
       'db_host' => 'localhost',
       'db_name' => 'excel_data',
       'db_user' => 'root',
       'db_pass' => 'your_password',  // Set your MySQL password
       'db_port' => 3306,
       'create_table' => true,
       'auto_detect_types' => true,
       'batch_size' => 1000
   ];
   ```

2. **Place your Excel file** in the project directory (e.g., `data.xlsx`)

3. **Run the import script**:
   ```bash
   php simple_import.php
   ```

## Usage Examples

### Basic Import

```php
<?php
require_once 'vendor/autoload.php';

use ExcelReader\ExcelToMySQLImporter;

$config = [
    'db_host' => 'localhost',
    'db_name' => 'excel_data',
    'db_user' => 'root',
    'db_pass' => 'your_password',
    'create_table' => true,
    'auto_detect_types' => true
];

$importer = new ExcelToMySQLImporter($config);

$result = $importer->importFromFile(
    'data.xlsx',        // Excel file path
    'employees',         // Table name
    'Sheet1',           // Sheet name (optional)
    1                   // Header row number
);

if ($result['success']) {
    echo "Imported {$result['rows_processed']} rows successfully!";
} else {
    echo "Error: {$result['message']}";
}
```

### Preview Data Before Import

```php
$preview = $importer->previewData('data.xlsx', 'Sheet1', 5);
if ($preview['success']) {
    foreach ($preview['preview'] as $row) {
        print_r($row);
    }
}
```

### Get Sheet Information

```php
$sheetInfo = $importer->getSheetInfo('data.xlsx');
if ($sheetInfo['success']) {
    foreach ($sheetInfo['sheets'] as $sheet) {
        echo "Sheet: {$sheet['name']}, Rows: {$sheet['rows']}, Columns: {$sheet['columns']}\n";
    }
}
```

### Custom Column Types

```php
$customTypes = [
    'id' => 'INT AUTO_INCREMENT PRIMARY KEY',
    'name' => 'VARCHAR(100)',
    'email' => 'VARCHAR(255)',
    'salary' => 'DECIMAL(10,2)',
    'hire_date' => 'DATE'
];

$result = $importer->importFromFile(
    'data.xlsx',
    'employees',
    'Sheet1',
    1,
    $customTypes
);
```

## Configuration Options

| Option | Default | Description |
|--------|---------|-------------|
| `db_host` | `localhost` | MySQL host address |
| `db_name` | `excel_data` | Database name |
| `db_user` | `root` | MySQL username |
| `db_pass` | `''` | MySQL password |
| `db_port` | `3306` | MySQL port |
| `create_table` | `true` | Create table if it doesn't exist |
| `auto_detect_types` | `true` | Automatically detect column types |
| `batch_size` | `1000` | Number of rows to process in each batch |

## File Structure

```
ExcelReader/
├── src/
│   ├── Database/
│   │   └── MySQLConnection.php
│   ├── Excel/
│   │   └── ExcelReader.php
│   └── ExcelToMySQLImporter.php
├── composer.json
├── example.php
├── simple_import.php
└── README.md
```

## Class Documentation

### ExcelToMySQLImporter

Main class that orchestrates the import process.

**Methods:**
- `importFromFile()` - Import data from Excel to MySQL
- `getSheetInfo()` - Get information about Excel sheets
- `previewData()` - Preview data before import
- `setConfig()` - Update configuration
- `getConfig()` - Get current configuration

### ExcelReader

Handles Excel file reading operations.

**Methods:**
- `loadFile()` - Load Excel file
- `readSheetDataWithHeaders()` - Read data with column headers
- `getColumnTypes()` - Determine appropriate MySQL column types
- `getSheetNames()` - Get list of sheet names

### MySQLConnection

Manages MySQL database connections and operations.

**Methods:**
- `connect()` - Establish database connection
- `createTable()` - Create table with specified columns
- `insertData()` - Insert data into table
- `close()` - Close database connection

## Error Handling

The script includes comprehensive error handling:

- File validation (existence, format)
- Database connection errors
- Data type validation
- Memory management for large files
- Graceful cleanup on errors

## Performance Tips

1. **Batch Processing**: Large files are processed in batches (default: 1000 rows)
2. **Memory Management**: Files are properly closed after processing
3. **Column Type Detection**: Automatic detection reduces manual configuration
4. **Prepared Statements**: Uses PDO prepared statements for security and performance

## Troubleshooting

### Common Issues

1. **"Database connection failed"**
   - Check MySQL credentials in configuration
   - Ensure MySQL service is running
   - Verify database exists

2. **"Excel file not found"**
   - Check file path is correct
   - Ensure file has .xlsx or .xls extension

3. **"Memory limit exceeded"**
   - Reduce batch size in configuration
   - Process smaller files
   - Increase PHP memory limit

4. **"Column type detection failed"**
   - Use custom column types
   - Check Excel file format
   - Ensure headers are in the correct row

### Debug Mode

Enable debug output by modifying the configuration:

```php
$config['debug'] = true;
```

## License

This project is open source and available under the MIT License.

## Contributing

Contributions are welcome! Please feel free to submit pull requests or open issues for bugs and feature requests. 