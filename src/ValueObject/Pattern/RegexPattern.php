<?php

declare(strict_types=1);

namespace Rector\Behastan\ValueObject\Pattern;

use Entropy\Utils\Regex;

final class RegexPattern extends AbstractPattern
{
    public function isRegexPatternNeccessary(): bool
    {
        $matches = Regex::match($this->pattern, '~^/(?<body>(?:\\\\/|[^/])*)/$~');
        if ($matches === []) {
            return true;
        }

        $body = $matches['body'];

        // ignore ^ at start and $ at end
        $body = preg_replace('~^\^~', '', $body);
        $body = preg_replace('~(?<!\\\\)\$$~', '', $body);

        // any unescaped regex meta char => needs regex
        return (bool) preg_match('~(?<!\\\\)[.*+?()\\[\\]{}|\\\\]~', $body);
    }
}
