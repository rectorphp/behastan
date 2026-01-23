<?php

declare (strict_types=1);
namespace Rector\Behastan\ValueObject\Pattern;

use Behastan202601\Entropy\Utils\Regex;
final class RegexPattern extends \Rector\Behastan\ValueObject\Pattern\AbstractPattern
{
    public function isRegexPatternNeccessary(): bool
    {
        // simple exact match regexes are redundant
        if (Regex::match($this->pattern, '/^\/[^\^\$\.\*\+\?\|\(\)\[\]\{\}\\\\]+\/$/')) {
            return \false;
        }
        return \true;
    }
}
