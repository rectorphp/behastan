<?php

declare(strict_types=1);

namespace Rector\Behastan\Analyzer;

final class PatternAnalyzer
{
    private const string PATTERN_REGEX = '#(\:[\W\w]+)#';

    public static function isRegex(string $rawPattern): bool
    {
        if (str_starts_with($rawPattern, '/')) {
            return true;
        }

        return str_ends_with($rawPattern, '#');
    }

    public static function isValuePattern(string $rawPattern): bool
    {
        preg_match(self::PATTERN_REGEX, $rawPattern, $match);

        return $match !== [];
    }
}
