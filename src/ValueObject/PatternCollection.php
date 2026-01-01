<?php

declare (strict_types=1);
namespace Rector\Behastan\ValueObject;

use Rector\Behastan\ValueObject\Pattern\AbstractPattern;
final class PatternCollection
{
    /**
     * @var AbstractPattern[]
     * @readonly
     */
    private $masks;
    /**
     * @param AbstractPattern[] $masks
     */
    public function __construct(array $masks)
    {
        $this->masks = $masks;
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
        return array_filter($this->masks, function (AbstractPattern $pattern) use ($type): bool {
            return $pattern instanceof $type;
        });
    }
}
