<?php

declare (strict_types=1);
namespace Behastan202601;

use Rector\Behastan\DependencyInjection\BehastanContainerFactory;
$possibleAutoloadPaths = [
    // dependency
    __DIR__ . '/../../../autoload.php',
    // after split package
    __DIR__ . '/../vendor/autoload.php',
    // monorepo
    __DIR__ . '/../../../vendor/autoload.php',
];
foreach ($possibleAutoloadPaths as $possibleAutoloadPath) {
    if (\file_exists($possibleAutoloadPath)) {
        require_once $possibleAutoloadPath;
        break;
    }
}
$scoperAutoloadFilepath = __DIR__ . '/../vendor/scoper-autoload.php';
if (\file_exists($scoperAutoloadFilepath)) {
    require_once $scoperAutoloadFilepath;
}
$container = BehastanContainerFactory::create();
$consoleApplication = $container->make(\Behastan202601\Entropy\Console\ConsoleApplication::class);
$exitCode = $consoleApplication->run($argv);
exit($exitCode);
