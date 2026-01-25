<?php

declare(strict_types=1);

namespace Rector\Behastan\Tests\ValueObject\Pattern;

use PHPUnit\Framework\TestCase;
use Rector\Behastan\ValueObject\Pattern\RegexPattern;

final class RegexPatternTest extends TestCase
{
    public function test(): void
    {
        $regexPattern = new RegexPattern('/foo/', 'someFilePath', 123, 'SomeClass', 'someMethod');
        $this->assertFalse($regexPattern->isRegexPatternNeccessary());

        $regexPattern = new RegexPattern('/foo (.*) and that/', 'someFilePath', 123, 'SomeClass', 'someMethod');
        $this->assertTrue($regexPattern->isRegexPatternNeccessary());

        $regexPattern = new RegexPattern('/^I do this and that$/', 'someFilePath', 123, 'SomeClass', 'someMethod');
        $this->assertFalse($regexPattern->isRegexPatternNeccessary());

        $regexPattern = new RegexPattern('#^I do this and that$#', 'someFilePath', 123, 'SomeClass', 'someMethod');
        $this->assertFalse($regexPattern->isRegexPatternNeccessary());
    }
}
