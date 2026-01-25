<?php

declare (strict_types=1);
namespace Rector\Behastan\ValueObject\Pattern;

use Behastan202601\Entropy\Attributes\RelatedTest;
use Rector\Behastan\Tests\ValueObject\Pattern\RegexPatternTest;
final class RegexPattern extends \Rector\Behastan\ValueObject\Pattern\AbstractPattern
{
    public function isRegexPatternNeccessary(): bool
    {
        // match <delim>body<delim> with same delimiter, no modifiers
        if (!preg_match('~^(?<d>.)(?<body>(?:\\\\.|(?!\k<d>).)*)\k<d>$~', $this->pattern, $matches)) {
            return \true;
        }
        $body = $matches['body'];
        // ignore ^ at start and $ at end
        /** @var string $body */
        $body = preg_replace('~^\^~', '', $body);
        /** @var string $body */
        $body = preg_replace('~(?<!\\\\)\$$~', '', $body);
        // any unescaped regex meta char => needs regex
        return (bool) preg_match('~(?<!\\\\)[.*+?()\[\]{}|\\\\]~', $body);
    }
}
