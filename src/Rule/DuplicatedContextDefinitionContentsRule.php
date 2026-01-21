<?php

declare (strict_types=1);
namespace Rector\Behastan\Rule;

use Rector\Behastan\Analyzer\ContextDefinitionsAnalyzer;
use Rector\Behastan\Contract\RuleInterface;
use Rector\Behastan\Enum\RuleIdentifier;
use Rector\Behastan\ValueObject\PatternCollection;
use Rector\Behastan\ValueObject\RuleError;
use Behastan202601\Symfony\Component\Finder\SplFileInfo;
final class DuplicatedContextDefinitionContentsRule implements RuleInterface
{
    /**
     * @readonly
     * @var \Rector\Behastan\Analyzer\ContextDefinitionsAnalyzer
     */
    private $contextDefinitionsAnalyzer;
    public function __construct(ContextDefinitionsAnalyzer $contextDefinitionsAnalyzer)
    {
        $this->contextDefinitionsAnalyzer = $contextDefinitionsAnalyzer;
    }
    /**
     * @param SplFileInfo[] $contextFiles
     * @param SplFileInfo[] $featureFiles
     *
     * @return RuleError[]
     */
    public function process(array $contextFiles, array $featureFiles, PatternCollection $patternCollection, string $projectDirectory): array
    {
        $contextDefinitionByContentHash = $this->contextDefinitionsAnalyzer->resolveAndGroupByContentHash($contextFiles);
        $ruleErrors = [];
        // keep only duplicated
        $duplicatedContextDefinitionByContentsHash = $this->filterOutNotDuplicated($contextDefinitionByContentHash);
        foreach ($duplicatedContextDefinitionByContentsHash as $duplicatedContextDefinition) {
            $patternStrings = '';
            $lineFilePaths = [];
            foreach ($duplicatedContextDefinition as $contextDefinition) {
                $patternStrings .= ' * ' . $contextDefinition->getPattern() . "\n";
                $lineFilePaths[] = $contextDefinition->getFilePath() . ':' . $contextDefinition->getMethodLine();
            }
            // standardize order
            sort($lineFilePaths);
            $errorMessage = sprintf('These %d definitions have different patterns, but same method body: %s%s', count($duplicatedContextDefinition), \PHP_EOL, $patternStrings);
            $ruleErrors[] = new RuleError($errorMessage, $lineFilePaths, $this->getIdentifier());
        }
        return $ruleErrors;
    }
    public function getIdentifier(): string
    {
        return RuleIdentifier::DUPLICATED_CONTENTS;
    }
    /**
     * @template TItem as object
     *
     * @param array<string, TItem[]> $items
     * @return array<string, TItem[]>
     */
    private function filterOutNotDuplicated(array $items): array
    {
        foreach ($items as $hash => $classAndMethods) {
            if (count($classAndMethods) < 2) {
                unset($items[$hash]);
            }
        }
        return $items;
    }
}
