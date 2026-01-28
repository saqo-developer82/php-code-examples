<?php

declare(strict_types=1);

// Usage: php parse_content.php [path_to_template]
// Default template path is content_template.txt in the same directory.

$templatePath = $argv[1] ?? __DIR__ . DIRECTORY_SEPARATOR . 'content_template.txt';

function getData($templatePath)
{
    if (!is_file($templatePath)) {
        fwrite(STDERR, "Template file not found: {$templatePath}\n");
        exit(1);
    }

    $lines = file($templatePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        fwrite(STDERR, "Failed to read file: {$templatePath}\n");
        exit(1);
    }

    $results = [];

    foreach ($lines as $line) {
        $raw = trim($line);
        if ($raw === '') {
            continue;
        }

        // Expect format: "<task text>\t<time>" or "<task text> <time>"
        // Time examples: "5m", "1h", "1h 50m", "3h:30m"
        // We'll split task and time by finding the final time pattern at end of line.

        // Normalize internal whitespace for robust matching (tabs/spaces)
        $normalized = preg_replace('/\s+/', ' ', $raw);
        if ($normalized === null) {
            $normalized = $raw; // Fallback if regex fails
        }

        // Match: capture task text (group 1), hours (group 2, optional), minutes (group 3, optional)
        // Allows optional colon or space between hours and minutes.
        $pattern = '/^(.*?)\s+(?:(\d+)\s*h)?(?:\s*:?\s*(\d+)\s*m)?$/i';

        $task = $normalized;
        $hours = 0;
        $minutes = 0;

        if (preg_match($pattern, $normalized, $m)) {
            $task = trim($m[1]);
            if (isset($m[2]) && $m[2] !== '') {
                $hours = (int) $m[2];
            }
            if (isset($m[3]) && $m[3] !== '') {
                $minutes = (int) $m[3];
            }
        } else {
            // If it doesn't match, attempt minutes-only at end (e.g., "Task 15m")
            if (preg_match('/^(.*?)\s+(\d+)\s*m$/i', $normalized, $mm)) {
                $task = trim($mm[1]);
                $minutes = (int) $mm[2];
            } else {
                // Leave as-is with zeroed time to avoid data loss
                $task = $normalized;
            }
        }

        $results[] = [
            'task' => $task,
            'hours' => $hours,
            'minutes' => $minutes,
        ];
    }

    return $results;
}

function generateSQL($data)
{
    $sql = 'INSERT INTO worked_hours (task, hours, minutes) VALUES ';
    foreach ($data as $row) {
        $valuesString = "'" . $row['task'] . "', " . $row['hours'] . ", " . $row['minutes'];
        echo "{$valuesString}\n";

        $sql .= "($valuesString),";
    }

    $sqlString = rtrim($sql, ',') . ";\n";
    
    $outputFile = __DIR__ . DIRECTORY_SEPARATOR . 'insertData.sql';
    $result = file_put_contents($outputFile, $sqlString);
    
    if ($result === false) {
        fwrite(STDERR, "Failed to write SQL to file: {$outputFile}\n");
        exit(1);
    }
    
    echo "SQL generated successfully and written to: {$outputFile}\n";
}

$data = getData($templatePath);
generateSQL($data);


