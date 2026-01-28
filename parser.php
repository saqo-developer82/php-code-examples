<?php
// Check if PHP version, need use 7+
if (phpversion() < 7) {
    die("PHP 7+ is required to run this script.");
}

// Parse command-line arguments
$options = getopt("f:u:");

if (empty($options['f']) || empty($options['u'])) {
    die("Usage: parser.php -f 'input file' -u 'combination file name'\n");
}

// Check if the input file exists
if (! file_exists('examples/' . $options['f'])) {
    die("Input file not found: " . $options['f'] ."\n");
}

try {
    require_once 'Parsers/ParserFactory.php';

    $parser = ParserFactory::createParser('examples/' . $options['f']);
    $data = $parser->parse();
} catch (\Exception $e) {
    die($e->getMessage());
}

try {
    require_once 'Creators/CreatorFactory.php';

    if (! is_dir('results')) {
        if (mkdir('results', 0755, true)) {
            echo "Directory created successfully.";
        } else {
            echo "Failed to create the directory.";
        }
    } else {
        echo "Directory already exists.";
    }

    $creator = CreatorFactory::createCreator('results/' . $options['u'], $data);
    $creator->create();
} catch (\Exception $e) {
    die($e->getMessage());
}

echo "DONE\n";
