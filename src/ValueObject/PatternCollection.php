<?php

declare(strict_types=1);

namespace Rector\Behastan\ValueObject;

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

    /**
     * @return string[]
     */
    public function regexPatternsStrings(): array
    {
        $regexPatterns = $this->byType(RegexPattern::class);

        $regexPatternStrings = array_map(function (RegexPattern $regexPattern): string {
            return $regexPattern->pattern;
        }, $regexPatterns);

        return array_values($regexPatternStrings);
    }
}
