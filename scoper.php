<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$nowDateTime = new DateTime('now');
$timestamp = $nowDateTime->format('Ym');

// @see https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md

// see https://github.com/humbug/php-scoper
return [
    'prefix' => 'Behastan' . $timestamp,
    'exclude-namespaces' => ['#^Rector\\\\Behastan#', '#^Webmozart\\\\#'],
    'patches' => [
        // unprefix "Behat\Step" string names
        function (string $filePath, string $prefix, string $contents): string {
            return str_replace(
                $prefix . '\\Behat\\Step',
                'Behat\\Step',
                $contents
            );
        },
    ],
];
