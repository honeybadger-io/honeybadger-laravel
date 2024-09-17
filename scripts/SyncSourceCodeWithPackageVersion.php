<?php

// Check if version input is provided
if ($argc < 2) {
    echo "Usage: php SyncSourceCodeWithPackageVersion.php <pr-title>\n";
    exit(1);
}

// Given the title of the PR, we can extract the version from it
// Example: chore(master): release 4.2.0
$prTitle = $argv[1];
$versionInput = trim(preg_replace('/^chore\(master\): release /', '', $prTitle));
$filePath = 'src/HoneybadgerLaravel.php';

// Read the content of the file
$fileContent = file_get_contents($filePath);
if ($fileContent === false) {
    echo "Error reading file: {$filePath}\n";
    exit(1);
}

// Replace the version line
$updatedContent = preg_replace(
    '/const VERSION = \'.*?\';/',
    "const VERSION = '{$versionInput}';",
    $fileContent
);

// Check if replacement was successful
if ($updatedContent === null) {
    echo "Error updating version.\n";
    exit(1);
}

// Write the updated content back to the file
$result = file_put_contents($filePath, $updatedContent);
if ($result === false) {
    echo "Error writing updated content back to file.\n";
    exit(1);
}

echo "Version updated to {$versionInput} in {$filePath}.\n";
