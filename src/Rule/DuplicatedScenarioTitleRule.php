<?php

declare(strict_types=1);

namespace Rector\Behastan\Rule;

use Rector\Behastan\Analyzer\DuplicatedScenarioTitlesAnalyzer;
use Rector\Behastan\Contract\RuleInterface;
use Rector\Behastan\Enum\RuleIdentifier;
use Rector\Behastan\ValueObject\PatternCollection;
use Rector\Behastan\ValueObject\RuleError;
use Symfony\Component\Finder\SplFileInfo;

final readonly class DuplicatedScenarioTitleRule implements RuleInterface
{
    public function __construct(
        private DuplicatedScenarioTitlesAnalyzer $duplicatedScenarioNamesAnalyzer
    ) {
    }

    /**
     * @param SplFileInfo[] $contextFiles
     * @param SplFileInfo[] $featureFiles
     *
     * @return RuleError[]
     */
    public function process(
        array $contextFiles,
        array $featureFiles,
        PatternCollection $patternCollection,
        string $projectDirectory
    ): array {
        $scenarioNamesToFiles = $this->duplicatedScenarioNamesAnalyzer->analyze($featureFiles);

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
