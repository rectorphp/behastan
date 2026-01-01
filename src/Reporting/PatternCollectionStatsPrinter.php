<?php

declare(strict_types=1);

namespace Rector\Behastan\Reporting;

use Entropy\Console\Output\OutputPrinter;
use Rector\Behastan\ValueObject\Pattern\ExactPattern;
use Rector\Behastan\ValueObject\Pattern\NamedPattern;
use Rector\Behastan\ValueObject\Pattern\RegexPattern;
use Rector\Behastan\ValueObject\Pattern\SkippedPattern;
use Rector\Behastan\ValueObject\PatternCollection;

final readonly class PatternCollectionStatsPrinter
{
    public function __construct(
        private OutputPrinter $outputPrinter
    ) {
    }

    public function print(PatternCollection $patternCollection): void
    {
        $this->outputPrinter->writeln(sprintf('Found %d patterns:', $patternCollection->count()));
        $this->outputPrinter->writeln(sprintf(' * %d exact', $patternCollection->countByType(ExactPattern::class)));
        $this->outputPrinter->writeln(sprintf(' * %d /regex/', $patternCollection->countByType(RegexPattern::class)));
        $this->outputPrinter->writeln(sprintf(' * %d :named', $patternCollection->countByType(NamedPattern::class)));

        $this->printSkippedMasks($patternCollection);
    }

    private function printSkippedMasks(PatternCollection $patternCollection): void
    {
        $skippedMasks = $patternCollection->byType(SkippedPattern::class);
        if ($skippedMasks === []) {
            return;
        }

        $skippedMasksValues = [];
        foreach ($skippedMasks as $skippedMask) {
            $skippedMasksValues[] = $skippedMask->pattern;
        }

        $skippedMasksString = implode('", "', $skippedMasksValues);

        $this->outputPrinter->writeln(sprintf(
            ' * %d skipped ("%s")',
            $patternCollection->countByType(SkippedPattern::class),
            $skippedMasksString
        ));
    }
}
