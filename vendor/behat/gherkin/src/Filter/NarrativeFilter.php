<?php

/*
 * This file is part of the Behat Gherkin Parser.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Behastan202601\Behat\Gherkin\Filter;

use Behastan202601\Behat\Gherkin\Node\FeatureNode;
use Behastan202601\Behat\Gherkin\Node\ScenarioInterface;
/**
 * Filters features by their narrative using regular expression.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class NarrativeFilter extends SimpleFilter
{
    /**
     * @readonly
     * @var string
     */
    private $regex;
    public function __construct(string $regex)
    {
        $this->regex = $regex;
    }
    public function isFeatureMatch(FeatureNode $feature)
    {
        return (bool) preg_match($this->regex, $feature->getDescription() ?? '');
    }
    public function isScenarioMatch(ScenarioInterface $scenario)
    {
        // This filter does not apply to scenarios.
        return \false;
    }
}
