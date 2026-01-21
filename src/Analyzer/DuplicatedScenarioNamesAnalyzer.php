<?php

declare (strict_types=1);
namespace Rector\Behastan\Analyzer;

use Behastan202601\Entropy\Attributes\RelatedTest;
use Behastan202601\Entropy\Utils\Regex;
use Rector\Behastan\Tests\Analyzer\DuplicatedScenarioNamesAnalyzer\DuplicatedScenarioNamesAnalyzerTest;
use Behastan202601\Symfony\Component\Finder\SplFileInfo;
final class DuplicatedScenarioNamesAnalyzer
{
    /**
     * @var string
     */
    private const SCENARIO_NAME_REGEX = '#\s+Scenario:\s+(?<name>.*?)\n#';
    /**
     * @param SplFileInfo[] $featureFiles
     * @return array<string, string[]>
     */
    public function analyze(array $featureFiles): array
    {
        $scenarioNamesToFiles = [];
        foreach ($featureFiles as $featureFile) {
            // match Scenario: "<name>"
            $matches = Regex::matchAll($featureFile->getContents(), self::SCENARIO_NAME_REGEX);
            foreach ($matches as $match) {
                $scenarioName = $match['name'];
                $scenarioNamesToFiles[$scenarioName][] = $featureFile->getRealPath();
            }
        }
        return array_filter($scenarioNamesToFiles, function (array $files): bool {
            return count($files) > 1;
        });
    }
}
