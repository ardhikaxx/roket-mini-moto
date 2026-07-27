<?php
$vendor = __DIR__ . '/../vendor';

$map = [
    'dompdf/dompdf' => 'dompdf',
    'barryvdh/laravel-dompdf' => 'barryvdh',
    'sabberworm/php-css-parser' => 'sabberworm',
    'masterminds/html5' => 'masterminds'
];

foreach ($map as $pkg => $folder) {
    $dir = "$vendor/$pkg";
    if (file_exists($dir)) {
        $subdirs = glob("$dir/*", GLOB_ONLYDIR);
        if (count($subdirs) === 1 && str_contains(basename($subdirs[0]), '-')) {
            $inner = $subdirs[0];
            echo "Organizing $pkg from $inner...\n";
            $items = glob("$inner/*");
            foreach ($items as $item) {
                rename($item, $dir . '/' . basename($item));
            }
            @rmdir($inner);
        }
    }
}
echo "Done organizing!\n";
