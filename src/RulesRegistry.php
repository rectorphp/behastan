<?php

declare(strict_types=1);

namespace Rector\Behastan;

use Rector\Behastan\Contract\RuleInterface;
use Webmozart\Assert\Assert;

final readonly class RulesRegistry
{
    /**
     * @param RuleInterface[] $rules
     */
    public function __construct(
        private array $rules
    ) {
        Assert::allObject($rules);
        Assert::allIsInstanceOf($rules, RuleInterface::class);
        Assert::greaterThan(count($rules), 3);
    }

    /**
     * @return RuleInterface[]
     */
    public function all(): array
    {
        return $this->rules;
    }
}
