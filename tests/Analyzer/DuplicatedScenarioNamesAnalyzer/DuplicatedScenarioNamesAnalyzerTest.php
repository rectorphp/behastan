<?php

declare(strict_types=1);

namespace Rector\Behastan\Tests\Analyzer\DuplicatedScenarioNamesAnalyzer;

use Rector\Behastan\Analyzer\DuplicatedScenarioNamesAnalyzer;
use Rector\Behastan\Finder\BehatMetafilesFinder;
use Rector\Behastan\Tests\AbstractTestCase;

final class DuplicatedScenarioNamesAnalyzerTest extends AbstractTestCase
{
    private DuplicatedScenarioNamesAnalyzer $duplicatedScenarioNamesAnalyzer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->duplicatedScenarioNamesAnalyzer = $this->make(DuplicatedScenarioNamesAnalyzer::class);
    }

    public function testSpot(): void
    {
        $featureFiles = BehatMetafilesFinder::findFeatureFiles([__DIR__ . '/Fixture/simple']);
        $this->assertCount(2, $featureFiles);

        $duplicatedScenarioNamesToFiles = $this->duplicatedScenarioNamesAnalyzer->analyze($featureFiles);

        $this->assertCount(1, $duplicatedScenarioNamesToFiles);
        $this->assertArrayHasKey('Same scenario name', $duplicatedScenarioNamesToFiles);

        $givenFiles = $duplicatedScenarioNamesToFiles['Same scenario name'];

        $this->assertSame([__DIR__ . '/Fixture/simple/some.feature', __DIR__ . '/Fixture/simple/another.feature'], $givenFiles);
    }

    public function testSkipSecondLineDifferent(): void
    {
        $featureFiles = BehatMetafilesFinder::findFeatureFiles([__DIR__ . '/Fixture/no-multi-line']);
        $this->assertCount(2, $featureFiles);

        $duplicatedScenarioNamesToFiles = $this->duplicatedScenarioNamesAnalyzer->analyze($featureFiles);

        $this->assertCount(0, $duplicatedScenarioNamesToFiles);
    }
}
