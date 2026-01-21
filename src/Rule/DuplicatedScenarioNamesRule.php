<?php

declare(strict_types=1);

namespace Rector\Behastan\Rule;

use Entropy\Utils\Regex;
use Rector\Behastan\Contract\RuleInterface;
use Rector\Behastan\Enum\RuleIdentifier;
use Rector\Behastan\ValueObject\PatternCollection;
use Rector\Behastan\ValueObject\RuleError;
use Symfony\Component\Finder\SplFileInfo;

final readonly class DuplicatedScenarioNamesRule implements RuleInterface
{
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
        $scenarioNamesToFiles = [];
        foreach ($featureFiles as $featureFile) {
            // match Scenario: "<name>"
            $matches = Regex::match($featureFile->getContents(), '#^\s*Scenario:\s*(["\'])(?P<name>.+?)\1#mi');
            foreach ($matches as $match) {
                $scenarioNamesToFiles[$match['name']][] = $featureFile->getRealPath();
            }
        }

        dump($scenarioNamesToFiles);
        die;

        //        $errorMessage = sprintf(
        //            'These %d definitions have different patterns, but same method body: %s%s',
        //            count($duplicatedContextDefinition),
        //            PHP_EOL,
        //            $patternStrings
        //        );
        //
        //        $ruleErrors[] = new RuleError($errorMessage, $lineFilePaths, $this->getIdentifier());

        return $ruleErrors;
    }

    public function getIdentifier(): string
    {
        return RuleIdentifier::DUPLICATED_SCENARIO_NAMES;
    }
}
