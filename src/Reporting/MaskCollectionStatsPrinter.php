<?php

declare (strict_types=1);
namespace Rector\Behastan\Reporting;

use Behastan202512\Entropy\Console\Output\OutputPrinter;
use Rector\Behastan\ValueObject\Mask\ExactMask;
use Rector\Behastan\ValueObject\Mask\NamedMask;
use Rector\Behastan\ValueObject\Mask\RegexMask;
use Rector\Behastan\ValueObject\Mask\SkippedMask;
use Rector\Behastan\ValueObject\MaskCollection;
final class MaskCollectionStatsPrinter
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
    public function print(MaskCollection $maskCollection): void
    {
        $this->outputPrinter->writeln(sprintf('Found %d masks:', $maskCollection->count()));
        $this->outputPrinter->writeln(sprintf(' * %d exact', $maskCollection->countByType(ExactMask::class)));
        $this->outputPrinter->writeln(sprintf(' * %d /regex/', $maskCollection->countByType(RegexMask::class)));
        $this->outputPrinter->writeln(sprintf(' * %d :named', $maskCollection->countByType(NamedMask::class)));
        $this->printSkippedMasks($maskCollection);
    }
    private function printSkippedMasks(MaskCollection $maskCollection): void
    {
        $skippedMasks = $maskCollection->byType(SkippedMask::class);
        if ($skippedMasks === []) {
            return;
        }
        $skippedMasksValues = [];
        foreach ($skippedMasks as $skippedMask) {
            $skippedMasksValues[] = $skippedMask->mask;
        }
        $skippedMasksString = implode('", "', $skippedMasksValues);
        $this->outputPrinter->writeln(sprintf(' * %d skipped ("%s")', $maskCollection->countByType(SkippedMask::class), $skippedMasksString));
    }
}
