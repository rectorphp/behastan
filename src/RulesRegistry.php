<?php

declare (strict_types=1);
namespace Rector\Behastan;

use Rector\Behastan\Contract\RuleInterface;
use Webmozart\Assert\Assert;
final class RulesRegistry
{
    /**
     * @var RuleInterface[]
     * @readonly
     */
    private $rules;
    /**
     * @param RuleInterface[] $rules
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
        Assert::allObject($rules);
        Assert::allIsInstanceOf($rules, RuleInterface::class);
        Assert::greaterThan(count($rules), 2);
    }
    /**
     * @return RuleInterface[]
     */
    public function all(): array
    {
        return $this->rules;
    }
}
