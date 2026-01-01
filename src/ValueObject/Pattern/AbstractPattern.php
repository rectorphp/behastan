<?php

declare(strict_types=1);

namespace Rector\Behastan\ValueObject\Pattern;

use Rector\Behastan\Contract\PatternInterface;

abstract class AbstractPattern implements PatternInterface
{
    public function __construct(
        public readonly string $pattern,
        public readonly string $filePath,
        public readonly int $line,
        public readonly string $className,
        public readonly string $methodName,
    ) {
    }
}
