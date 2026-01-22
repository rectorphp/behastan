<?php

declare(strict_types=1);

namespace Rector\Behastan\ValueObject\Pattern;

use Entropy\Utils\Regex;

final class NamedPattern extends AbstractPattern
{
    private const string NAMED_MASK_REGEX = '#(\:[\W\w]+)#';

    public function getRegexPattern(): string
    {
        return '#' . Regex::replace($this->pattern, self::NAMED_MASK_REGEX, '(.*?)') . '#';
    }
}
