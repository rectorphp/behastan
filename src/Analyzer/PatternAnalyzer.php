<?php

declare (strict_types=1);
namespace Rector\Behastan\Analyzer;

final class PatternAnalyzer
{
    /**
     * @var string
     */
    private const PATTERN_REGEX = '#(\:[\W\w]+)#';
    public static function isRegex(string $rawPattern): bool
    {
        if (strncmp($rawPattern, '/', strlen('/')) === 0) {
            return \true;
        }
        return substr_compare($rawPattern, '#', -strlen('#')) === 0;
    }
    public static function isValuePattern(string $rawPattern): bool
    {
        preg_match(self::PATTERN_REGEX, $rawPattern, $match);
        return $match !== [];
    }
}
