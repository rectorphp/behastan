<?php

declare (strict_types=1);
namespace Rector\Behastan\Analyzer;

use Jack202512\Entropy\Console\Output\OutputPrinter;
use Jack202512\Nette\Utils\Strings;
use Rector\Behastan\DefinitionMasksExtractor;
use Rector\Behastan\UsedInstructionResolver;
use Rector\Behastan\ValueObject\Mask\AbstractMask;
use Rector\Behastan\ValueObject\Mask\ExactMask;
use Rector\Behastan\ValueObject\Mask\NamedMask;
use Rector\Behastan\ValueObject\Mask\RegexMask;
use Rector\Behastan\ValueObject\Mask\SkippedMask;
use Rector\Behastan\ValueObject\MaskCollection;
use Jack202512\Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;
/**
 * @see \Rector\Behastan\Tests\Analyzer\UnusedDefinitionsAnalyzer\UnusedDefinitionsAnalyzerTest
 */
final class UnusedDefinitionsAnalyzer
{
    /**
     * @readonly
     * @var \Rector\Behastan\UsedInstructionResolver
     */
    private $usedInstructionResolver;
    /**
     * @readonly
     * @var \Rector\Behastan\DefinitionMasksExtractor
     */
    private $definitionMasksExtractor;
    /**
     * @var string
     */
    private const MASK_VALUE_REGEX = '#(\:[\W\w]+)#';
    public function __construct(UsedInstructionResolver $usedInstructionResolver, DefinitionMasksExtractor $definitionMasksExtractor)
    {
        $this->usedInstructionResolver = $usedInstructionResolver;
        $this->definitionMasksExtractor = $definitionMasksExtractor;
    }
    /**
     * @param SplFileInfo[] $contextFiles
     * @param SplFileInfo[] $featureFiles
     *
     * @return AbstractMask[]
     */
    public function analyse(array $contextFiles, array $featureFiles, MaskCollection $maskCollection): array
    {
        Assert::allIsInstanceOf($contextFiles, SplFileInfo::class);
        foreach ($contextFiles as $contextFile) {
            Assert::endsWith($contextFile->getFilename(), '.php');
        }
        Assert::allIsInstanceOf($featureFiles, SplFileInfo::class);
        foreach ($featureFiles as $featureFile) {
            Assert::endsWith($featureFile->getFilename(), '.feature');
        }
        $maskCollection = $this->definitionMasksExtractor->extract($contextFiles);
        $featureInstructions = $this->usedInstructionResolver->resolveInstructionsFromFeatureFiles($featureFiles);
        //$maskProgressBar = $this->outputPrinter->createProgressBar($maskCollection->count());
        $unusedMasks = [];
        foreach ($maskCollection->all() as $mask) {
            //            $maskProgressBar->advance();
            if ($this->isMaskUsed($mask, $featureInstructions)) {
                continue;
            }
            $unusedMasks[] = $mask;
        }
        //        $maskProgressBar->finish();
        return $unusedMasks;
    }
    /**
     * @param string[] $featureInstructions
     */
    private function isRegexDefinitionUsed(string $regexBehatDefinition, array $featureInstructions): bool
    {
        foreach ($featureInstructions as $featureInstruction) {
            if (Strings::match($featureInstruction, $regexBehatDefinition)) {
                // it is used!
                return \true;
            }
        }
        return \false;
    }
    /**
     * @param string[] $featureInstructions
     */
    private function isMaskUsed(AbstractMask $mask, array $featureInstructions): bool
    {
        if ($mask instanceof SkippedMask) {
            return \true;
        }
        // is used?
        if ($mask instanceof ExactMask && in_array($mask->mask, $featureInstructions, \true)) {
            return \true;
        }
        // is used?
        if ($mask instanceof RegexMask && $this->isRegexDefinitionUsed($mask->mask, $featureInstructions)) {
            return \true;
        }
        if ($mask instanceof NamedMask) {
            // normalize :mask definition to regex
            $regexMask = '#' . Strings::replace($mask->mask, self::MASK_VALUE_REGEX, '(.*?)') . '#';
            if ($this->isRegexDefinitionUsed($regexMask, $featureInstructions)) {
                return \true;
            }
        }
        return \false;
    }
}
