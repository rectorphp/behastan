<?php

declare (strict_types=1);
namespace Rector\Behastan\Analyzer;

use Behastan202601\Behat\Gherkin\Node\FeatureNode;
use Behastan202601\Entropy\Attributes\RelatedTest;
use Rector\Behastan\Gherkin\GherkinParser;
use Rector\Behastan\Tests\Analyzer\DuplicatedScenarioNamesAnalyzer\DuplicatedScenarioTitlesAnalyzerTest;
use Behastan202601\Symfony\Component\Finder\SplFileInfo;
final class DuplicatedScenarioTitlesAnalyzer
{
    /**
     * @readonly
     * @var \Rector\Behastan\Gherkin\GherkinParser
     */
    private $gherkinParser;
    public function __construct(GherkinParser $gherkinParser)
    {
        $this->gherkinParser = $gherkinParser;
    }
    /**
     * @param SplFileInfo[] $featureFiles
     * @return array<string, string[]>
     */
    public function analyze(array $featureFiles): array
    {
        $scenarioNamesToFiles = [];
        foreach ($featureFiles as $featureFile) {
            $featureGherkin = $this->gherkinParser->parseFile($featureFile->getRealPath());
            // @todo test and improve here
            if (!$featureGherkin instanceof FeatureNode) {
                continue;
            }
            foreach ($featureGherkin->getScenarios() as $scenario) {
                $scenarioNamesToFiles[$scenario->getTitle()][] = $featureFile->getRealPath();
            }
        }
        return array_filter($scenarioNamesToFiles, function (array $files): bool {
            return count($files) > 1;
        });
    }
}
