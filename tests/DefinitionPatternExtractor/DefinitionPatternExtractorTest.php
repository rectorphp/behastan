<?php

declare(strict_types=1);

namespace Rector\Behastan\Tests\DefinitionPatternExtractor;

use Rector\Behastan\DefinitionPatternsExtractor;
use Rector\Behastan\Finder\BehatMetafilesFinder;
use Rector\Behastan\Tests\AbstractTestCase;
use Rector\Behastan\Tests\DefinitionPatternExtractor\Fixture\AnotherBehatContext;
use Rector\Behastan\ValueObject\Pattern\ExactPattern;

final class DefinitionPatternExtractorTest extends AbstractTestCase
{
    private DefinitionPatternsExtractor $definitionPatternsExtractor;

    private BehatMetafilesFinder $behatMetafilesFinder;

    protected function setUp(): void
    {
        parent::setUp();

        $this->definitionPatternsExtractor = $this->make(DefinitionPatternsExtractor::class);
        $this->behatMetafilesFinder = $this->make(BehatMetafilesFinder::class);
    }

    public function test(): void
    {
        $contextFileInfos = $this->behatMetafilesFinder->findContextFiles([__DIR__ . '/Fixture']);
        $patternCollection = $this->definitionPatternsExtractor->extract($contextFileInfos);

        $this->assertCount(3, $patternCollection->all());

        $exactMasks = $patternCollection->byType(ExactPattern::class);
        $this->assertCount(3, $exactMasks);
        $this->assertContainsOnlyInstancesOf(ExactPattern::class, $exactMasks);

        $firstExactMask = $exactMasks[0];

        $this->assertSame('I click homepage', $firstExactMask->pattern);
        $this->assertSame(AnotherBehatContext::class, $firstExactMask->className);
        $this->assertSame(__DIR__ . '/Fixture/AnotherBehatContext.php', $firstExactMask->filePath);

        $slashMask = $exactMasks[2];

        $this->assertSame('Do this and / that', $slashMask->pattern);
    }
}
