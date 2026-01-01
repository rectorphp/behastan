<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return RectorConfig::configure()
    ->withPaths([__DIR__ . '/src', __DIR__ . '/tests'])
    ->withPhpSets()
    ->withPreparedSets(
        codeQuality: true,
        deadCode: true,
        typeDeclarations: true,
        privatization: true,
        earlyReturn: true,
        codingStyle: true,
        instanceOf: true,
        phpunitCodeQuality: true,
        naming: true,
        rectorPreset: true,
    )
    ->withSkip([
        \Rector\Php55\Rector\String_\StringClassNameToClassConstantRector::class => [
            // keep string class names
            __DIR__ . '/src/Resolver/ClassMethodPatternResolver.php',
        ],
    ])
    ->withImportNames()
    ->withSkip(['*/scoper.php', '*/Source/*', '*/Fixture/*']);
