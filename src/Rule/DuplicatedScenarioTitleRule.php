<?php

declare (strict_types=1);
namespace Rector\Behastan\Rule;

use Rector\Behastan\Analyzer\DuplicatedScenarioTitlesAnalyzer;
use Rector\Behastan\Contract\RuleInterface;
use Rector\Behastan\Enum\RuleIdentifier;
use Rector\Behastan\ValueObject\PatternCollection;
use Rector\Behastan\ValueObject\RuleError;
use Behastan202601\Symfony\Component\Finder\SplFileInfo;
final class DuplicatedScenarioTitleRule implements RuleInterface
{
    /**
     * @readonly
     * @var \Rector\Behastan\Analyzer\DuplicatedScenarioTitlesAnalyzer
     */
    private $duplicatedScenarioTitlesAnalyzer;
    public function __construct(DuplicatedScenarioTitlesAnalyzer $duplicatedScenarioTitlesAnalyzer)
    {
        $this->duplicatedScenarioTitlesAnalyzer = $duplicatedScenarioTitlesAnalyzer;
    }
    /**
     * @param SplFileInfo[] $contextFiles
     * @param SplFileInfo[] $featureFiles
     *
     * @return RuleError[]
     */
    public function process(array $contextFiles, array $featureFiles, PatternCollection $patternCollection, string $projectDirectory): array
    {
        $scenarioNamesToFiles = $this->duplicatedScenarioTitlesAnalyzer->analyze($featureFiles);
        $ruleErrors = [];
        foreach ($scenarioNamesToFiles as $scenarioName => $files) {
            // it can be used multiple times in single file
            $uniqueFiles = array_unique($files);
            $errorMessage = sprintf('Scenario name "%s" is duplicated %d-times', $scenarioName, count($files));
            $ruleErrors[] = new RuleError($errorMessage, $uniqueFiles, $this->getIdentifier());
        }
        return $ruleErrors;
    }
    public function getIdentifier(): string
    {
        return RuleIdentifier::DUPLICATED_SCENARIO_TITLES;
    }
}
