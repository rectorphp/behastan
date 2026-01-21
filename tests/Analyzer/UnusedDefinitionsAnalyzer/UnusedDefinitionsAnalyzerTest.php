<?php

declare(strict_types=1);

namespace Rector\Behastan\Tests\Analyzer\UnusedDefinitionsAnalyzer;

use Rector\Behastan\Analyzer\UnusedDefinitionsAnalyzer;
use Rector\Behastan\DefinitionPatternsExtractor;
use Rector\Behastan\Finder\BehatMetafilesFinder;
use Rector\Behastan\Tests\AbstractTestCase;
use Rector\Behastan\ValueObject\Pattern\AbstractPattern;

final class UnusedDefinitionsAnalyzerTest extends AbstractTestCase
{
    private UnusedDefinitionsAnalyzer $unusedDefinitionsAnalyzer;

    private DefinitionPatternsExtractor $definitionPatternsExtractor;

    protected function setUp(): void
    {
        parent::setUp();

        $this->unusedDefinitionsAnalyzer = $this->make(UnusedDefinitionsAnalyzer::class);
        $this->definitionPatternsExtractor = $this->make(DefinitionPatternsExtractor::class);
    }

    public function testEverythingUsed(): void
    {
        $featureFiles = BehatMetafilesFinder::findFeatureFiles([__DIR__ . '/Fixture/EverythingUsed']);
        $contextFiles = BehatMetafilesFinder::findContextFiles([__DIR__ . '/Fixture/EverythingUsed']);

        $this->assertCount(1, $featureFiles);
        $this->assertCount(1, $contextFiles);

        $patternCollection = $this->definitionPatternsExtractor->extract($contextFiles);

        $unusedDefinitions = $this->unusedDefinitionsAnalyzer->analyse($featureFiles, $patternCollection);

        $this->assertCount(0, $unusedDefinitions);
    }

    public function testFoundPattern(): void
    {
        $featureFiles = BehatMetafilesFinder::findFeatureFiles([__DIR__ . '/Fixture/UnusedPattern']);
        $contextFiles = BehatMetafilesFinder::findContextFiles([__DIR__ . '/Fixture/UnusedPattern']);

        $this->assertCount(1, $featureFiles);
        $this->assertCount(1, $contextFiles);

        $patternCollection = $this->definitionPatternsExtractor->extract($contextFiles);

        $unusedPatterns = $this->unusedDefinitionsAnalyzer->analyse($featureFiles, $patternCollection);

        $this->assertCount(1, $unusedPatterns);
        $this->assertContainsOnlyInstancesOf(AbstractPattern::class, $unusedPatterns);

        /** @var AbstractPattern $unusedPattern */
        $unusedPattern = $unusedPatterns[0];
        $this->assertSame(__DIR__ . '/Fixture/UnusedPattern/BehatContext.php', $unusedPattern->filePath);
        $this->assertSame('never used', $unusedPattern->pattern);
    }
}
