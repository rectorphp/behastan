<?php

declare (strict_types=1);
namespace Behastan202601\Entropy\Attributes;

use Attribute;
use Behastan202601\PHPUnit\Framework\TestCase;
#[Attribute(Attribute::TARGET_CLASS)]
final class RelatedTest
{
    /**
     * @param class-string<TestCase> $testClass
     */
    public function __construct(string $testClass)
    {
    }
}
