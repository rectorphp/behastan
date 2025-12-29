<?php

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

$nowDateTime = new DateTime('now');
$timestamp = $nowDateTime->format('Ym');

// @see https://github.com/humbug/php-scoper/blob/master/docs/further-reading.md

// see https://github.com/humbug/php-scoper
return [
    'prefix' => 'Jack' . $timestamp,
    // unprefix "Behat\Step" namespace
    'exclude-namespaces' => ['#^Rector\\\\Behastan#', '#^Webmozart\\\\#', '#^Behat\\\\Step#'],
];
