<?php

declare(strict_types=1);

namespace Rector\Behastan\ValueObject;

use Rector\Behastan\ValueObject\Pattern\AbstractPattern;

final readonly class PatternCollection
{
    /**
     * @param AbstractPattern[] $masks
     */
    public function __construct(
        private array $masks
    ) {
    }

    /**
     * @param class-string<AbstractPattern> $type
     */
    public function countByType(string $type): int
    {
        $masksByType = $this->byType($type);
        return count($masksByType);
    }

    public function count(): int
    {
        return count($this->masks);
    }

    /**
     * @return AbstractPattern[]
     */
    public function all(): array
    {
        return $this->masks;
    }

    /**
     * @template TMask as AbstractPattern
     *
     * @param class-string<TMask> $type
     * @return TMask[]
     */
    public function byType(string $type): array
    {
        return array_filter($this->masks, fn (AbstractPattern $pattern): bool => $pattern instanceof $type);
    }
}
