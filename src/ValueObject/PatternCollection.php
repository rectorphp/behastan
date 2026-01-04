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
    private $patterns;
    /**
     * @param AbstractPattern[] $patterns
     */
    public function __construct(array $patterns)
    {
        $this->patterns = $patterns;
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
        return array_filter($this->patterns, function (AbstractPattern $pattern) use ($type): bool {
            return $pattern instanceof $type;
        });
    }
}
