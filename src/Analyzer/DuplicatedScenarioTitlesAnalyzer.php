<?php

declare(strict_types=1);

namespace Rector\Behastan\Analyzer;

use Behat\Gherkin\Node\FeatureNode;
use Entropy\Attributes\RelatedTest;
use Rector\Behastan\Gherkin\GherkinParser;
use Rector\Behastan\Tests\Analyzer\DuplicatedScenarioNamesAnalyzer\DuplicatedScenarioTitlesAnalyzerTest;
use Symfony\Component\Finder\SplFileInfo;

#[RelatedTest(DuplicatedScenarioTitlesAnalyzerTest::class)]
final readonly class DuplicatedScenarioTitlesAnalyzer
{
    public function __construct(
        private GherkinParser $gherkinParser
    ) {
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
            if (! $featureGherkin instanceof FeatureNode) {
                continue;
            }

            foreach ($featureGherkin->getScenarios() as $scenario) {
                $scenarioNamesToFiles[$scenario->getTitle()][] = $featureFile->getRealPath();
            }
        }

        return array_filter($scenarioNamesToFiles, fn (array $files): bool => count($files) > 1);
    }
}
