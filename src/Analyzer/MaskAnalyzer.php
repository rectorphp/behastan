<?php

declare (strict_types=1);
namespace Rector\Behastan\Analyzer;

final class MaskAnalyzer
{
    /**
     * @var string
     */
    private const MASK_REGEX = '#(\:[\W\w]+)#';
    public static function isRegex(string $rawMask): bool
    {
        if (strncmp($rawMask, '/', strlen('/')) === 0) {
            return \true;
        }
        return substr_compare($rawMask, '#', -strlen('#')) === 0;
    }
    public static function isValueMask(string $rawMask): bool
    {
        preg_match(self::MASK_REGEX, $rawMask, $match);
        return $match !== [];
    }
}
