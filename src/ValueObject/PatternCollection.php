<?php

declare(strict_types=1);

namespace Rector\Behastan\ValueObject;

use Rector\Behastan\ValueObject\Pattern\AbstractPattern;

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
     * @template TPattern as AbstractPattern
     *
     * @param class-string<TPattern> $type
     * @return TPattern[]
     */
    public function byType(string $type): array
    {
        return array_filter($this->patterns, fn (AbstractPattern $pattern): bool => $pattern instanceof $type);
    }
}
