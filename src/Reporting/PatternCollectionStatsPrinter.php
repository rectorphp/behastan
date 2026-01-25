<?php

declare (strict_types=1);
namespace Rector\Behastan\Reporting;

use Behastan202601\Entropy\Console\Output\OutputPrinter;
use Rector\Behastan\ValueObject\Pattern\ExactPattern;
use Rector\Behastan\ValueObject\Pattern\NamedPattern;
use Rector\Behastan\ValueObject\Pattern\RegexPattern;
use Rector\Behastan\ValueObject\Pattern\SkippedPattern;
use Rector\Behastan\ValueObject\PatternCollection;
final class PatternCollectionStatsPrinter
{
    /**
     * @readonly
     * @var \Entropy\Console\Output\OutputPrinter
     */
    private $outputPrinter;
    public function __construct(OutputPrinter $outputPrinter)
    {
        $this->outputPrinter = $outputPrinter;
    }
    public function print(PatternCollection $patternCollection): void
    {
        $this->outputPrinter->writeln(sprintf('Found %d patterns:', $patternCollection->count()));
        $this->outputPrinter->writeln(sprintf(' * %d exact', $patternCollection->countByType(ExactPattern::class)));
        $this->outputPrinter->writeln(sprintf(' * %d /regex/', $patternCollection->countByType(RegexPattern::class)));
        $this->outputPrinter->writeln(sprintf(' * %d :named', $patternCollection->countByType(NamedPattern::class)));
        $this->printSkippedPatterns($patternCollection);
    }
    private function printSkippedPatterns(PatternCollection $patternCollection): void
    {
        $skippedPatterns = $patternCollection->byType(SkippedPattern::class);
        if ($skippedPatterns === []) {
            return;
        }
        $skippedPatternsValues = [];
        foreach ($skippedPatterns as $skippedPattern) {
            $skippedPatternsValues[] = $skippedPattern->pattern;
        }
        $skippedPatternsString = implode('", "', $skippedPatternsValues);
        $this->outputPrinter->writeln(sprintf(' * %d skipped ("%s")', $patternCollection->countByType(SkippedPattern::class), $skippedPatternsString));
    }
}
