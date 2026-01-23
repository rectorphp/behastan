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
 * Abstract filter class.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class ComplexFilter implements ComplexFilterInterface
{
    /**
     * Filters feature according to the filter.
     *
     * @return FeatureNode
     */
    public function filterFeature(FeatureNode $feature)
    {
        $scenarios = $feature->getScenarios();
        $filteredScenarios = array_filter($scenarios, function (ScenarioInterface $scenario) use ($feature) {
            return $this->isScenarioMatch($feature, $scenario);
        });
        return $scenarios === $filteredScenarios ? $feature : $feature->withScenarios($filteredScenarios);
    }
}
