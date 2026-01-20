<?php

declare(strict_types=1);

namespace Rector\Behastan\ValueObject;

use InvalidArgumentException;
use Rector\Behastan\ValueObject\Pattern\AbstractPattern;
use Rector\Behastan\ValueObject\Pattern\ExactPattern;
use Rector\Behastan\ValueObject\Pattern\RegexPattern;

final readonly class PatternCollection
{
    /**
     * @param AbstractPattern[] $patterns
     */
    public function __construct(
        private array $patterns
    ) {
    }

    /**
     * @param class-string<AbstractPattern> $type
     */
    public function countByType(string $type): int
    {
        $patternsByType = $this->byType($type);
        return count($patternsByType);
    }

    public function count(): int
    {
        return count($this->patterns);
    }

    /**
     * @return AbstractPattern[]
     */
    public function all(): array
    {
        return $this->patterns;
    }

    /**
     * @return string[]
     */
    public function exactPatternStrings(): array
    {
        $exactPatterns = $this->byType(ExactPattern::class);

        return array_map(fn (ExactPattern $exactPattern): string => $exactPattern->pattern, $exactPatterns);
    }

    /**
     * @template TPattern as AbstractPattern
     *
     * @param class-string<TPattern> $type
     * @return TPattern[]
     */
    public function byType(string $type): array
    {
        return array_filter($this->patterns, fn (AbstractPattern $pattern): bool => $pattern instanceof $type);
    }

    public function regexPatternString(): string
    {
        $regexPatterns = $this->byType(RegexPattern::class);

        $regexPatternStrings = array_map(
            fn (RegexPattern $regexPattern): string => $regexPattern->pattern,
            $regexPatterns
        );

        return $this->combineRegexes($regexPatternStrings, '#');
    }

    /**
     * @param string[] $regexes Like ['/foo/i', '~bar\d+~', '#baz#u']
     */
    private function combineRegexes(array $regexes, string $delimiter = '#'): string
    {
        $parts = [];

        foreach ($regexes as $regex) {
            // Very common case: regex is given like "/pattern/flags"
            // Parse: delimiter + pattern + delimiter + flags
            if (! preg_match('~^(.)(.*)\\1([a-zA-Z]*)$~s', $regex, $m)) {
                throw new InvalidArgumentException('Invalid regex: ' . $regex);
            }

            $pattern = $m[2];
            $flags = $m[3];

            // If you truly have mixed flags per-regex, you can't naively merge them.
            // Best practice: normalize flags beforehand (same for all).
            // We'll ignore per-regex flags here and let the caller decide final flags.
            $parts[] = '(?:' . $pattern . ')';
        }

        return $delimiter . '(?:' . implode('|', $parts) . ')' . $delimiter;
    }
}
