<?php

declare(strict_types=1);

namespace Rector\Behastan\Tests\ValueObject;

use PHPUnit\Framework\TestCase;
use Rector\Behastan\ValueObject\Pattern\ExactPattern;
use Rector\Behastan\ValueObject\Pattern\NamedPattern;
use Rector\Behastan\ValueObject\Pattern\RegexPattern;
use Rector\Behastan\ValueObject\PatternCollection;

final class PatternCollectionTest extends TestCase
{
    public function testExactPatterns(): void
    {
        $patternCollection = new PatternCollection([
            new ExactPattern('pattern1', 'file1.php', 10, 'SomeClass', 'someMethod'),
            new ExactPattern('pattern2', 'file2.php', 20, 'AnotherClass', 'anotherMethod'),
            new NamedPattern('this is :me', 'file1.php', 10, 'SomeClass', 'someMethod'),
        ]);

        $this->assertSame(['pattern1', 'pattern2'], $patternCollection->exactPatternStrings());
    }

    public function testRegexPatterns(): void
    {
        $patternCollection = new PatternCollection([
            new ExactPattern('pattern1', 'file1.php', 10, 'SomeClass', 'someMethod'),
            new RegexPattern('#this is it#', 'file1.php', 10, 'SomeClass', 'someMethod'),
            new RegexPattern('#here is more#', 'file1.php', 10, 'SomeClass', 'someMethod'),
        ]);

        $this->assertSame(['#this is it#', '#here is more#'], $patternCollection->regexPatternsStrings());
    }
}
