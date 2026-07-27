<?php
$appData = getenv('LOCALAPPDATA');
$baseCache = "$appData/Composer/files";
$vendor = __DIR__ . '/../vendor';

$zips = [
    'dompdf/dompdf' => "$baseCache/dompdf/dompdf/3004b2fffa75420ec98f9bdf7ea5f3d189c40227.zip",
    'barryvdh/laravel-dompdf' => "$baseCache/barryvdh/laravel-dompdf/fde077fa32d79396a3315a785779ca065d4a0eb6.zip",
    'sabberworm/php-css-parser' => "$baseCache/sabberworm/php-css-parser/ea6b8ee425ee437d2f72fffe17b8fba000063269.zip",
    'masterminds/html5' => "$baseCache/masterminds/html5/44913a9ee280f210fdc46e066668ffb2e25a247d.zip",
];

foreach ($zips as $pkg => $zipPath) {
    if (!file_exists($zipPath)) {
        echo "Zip not found: $zipPath\n";
        continue;
    }

    $targetDir = "$vendor/$pkg";
    if (file_exists($targetDir)) {
        // clean directory
        exec("rmdir /s /q \"" . str_replace('/', '\\', $targetDir) . "\"");
    }
    mkdir($targetDir, 0777, true);

    $zip = new ZipArchive();
    if ($zip->open($zipPath) === true) {
        $tempDir = "$targetDir/temp_extract";
        mkdir($tempDir, 0777, true);
        $zip->extractTo($tempDir);
        $zip->close();

        // Get inner folder
        $inner = glob("$tempDir/*", GLOB_ONLYDIR);
        if (count($inner) > 0) {
            $innerFolder = $inner[0];
            $files = array_diff(scandir($innerFolder), ['.', '..']);
            foreach ($files as $file) {
                rename("$innerFolder/$file", "$targetDir/$file");
            }
        }
        exec("rmdir /s /q \"" . str_replace('/', '\\', $tempDir) . "\"");
        echo "Successfully unpacked $pkg!\n";
    } else {
        echo "Failed to open zip for $pkg\n";
    }
}
