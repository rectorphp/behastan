<?php

declare (strict_types=1);
namespace Rector\Behastan\Rule;

use Rector\Behastan\Analyzer\UnusedDefinitionsAnalyzer;
use Rector\Behastan\Contract\RuleInterface;
use Rector\Behastan\Enum\RuleIdentifier;
use Rector\Behastan\ValueObject\PatternCollection;
use Rector\Behastan\ValueObject\RuleError;
use Behastan202601\Symfony\Component\Finder\SplFileInfo;
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
    public function process(array $contextFiles, array $featureFiles, PatternCollection $patternCollection, string $projectDirectory): array
    {
        $unusedMasks = $this->unusedDefinitionsAnalyzer->analyse($contextFiles, $featureFiles, $patternCollection);
        $ruleErrors = [];
        foreach ($unusedMasks as $unusedMask) {
            $errorMessage = sprintf('The mask "%s" and its definition %s::%s() is never used', $unusedMask->pattern, $unusedMask->className, $unusedMask->methodName);
            $ruleErrors[] = new RuleError($errorMessage, [$unusedMask->filePath . ':' . $unusedMask->line], $this->getIdentifier());
        }
        return $ruleErrors;
    }
    public function getIdentifier(): string
    {
        return RuleIdentifier::UNUSED_DEFINITIONS;
    }
}
