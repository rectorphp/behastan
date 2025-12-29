<?php

declare (strict_types=1);
namespace Rector\Behastan\Rule;

use Rector\Behastan\Analyzer\UnusedDefinitionsAnalyzer;
use Rector\Behastan\Contract\RuleInterface;
use Rector\Behastan\Enum\RuleIdentifier;
use Rector\Behastan\ValueObject\MaskCollection;
use Rector\Behastan\ValueObject\RuleError;
use Jack202512\Symfony\Component\Finder\SplFileInfo;
final class UnusedContextDefinitionsRule implements RuleInterface
{
    /**
     * @readonly
     * @var \Rector\Behastan\Analyzer\UnusedDefinitionsAnalyzer
     */
    private $unusedDefinitionsAnalyzer;
    public function __construct(UnusedDefinitionsAnalyzer $unusedDefinitionsAnalyzer)
    {
        $this->unusedDefinitionsAnalyzer = $unusedDefinitionsAnalyzer;
    }
    /**
     * @param SplFileInfo[] $contextFiles
     * @param SplFileInfo[] $featureFiles
     * @return RuleError[]
     */
    public function process(array $contextFiles, array $featureFiles, MaskCollection $maskCollection, string $projectDirectory): array
    {
        $unusedMasks = $this->unusedDefinitionsAnalyzer->analyse($contextFiles, $featureFiles, $maskCollection);
        $ruleErrors = [];
        foreach ($unusedMasks as $unusedMask) {
            $ruleErrors[] = new RuleError(sprintf('The mask "%s" and its definition %s::%s() is never used', $unusedMask->mask, $unusedMask->className, $unusedMask->methodName), [$unusedMask->filePath . ':' . $unusedMask->line]);
        }
        return $ruleErrors;
    }
    public function getIdentifier(): string
    {
        return RuleIdentifier::UNUSED_DEFINITIONS;
    }
}
