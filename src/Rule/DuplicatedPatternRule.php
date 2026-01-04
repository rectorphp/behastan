<?php

declare(strict_types=1);

namespace Rector\Behastan\Rule;

use Rector\Behastan\Analyzer\ContextDefinitionsAnalyzer;
use Rector\Behastan\Contract\RuleInterface;
use Rector\Behastan\Enum\RuleIdentifier;
use Rector\Behastan\ValueObject\ContextDefinition;
use Rector\Behastan\ValueObject\PatternCollection;
use Rector\Behastan\ValueObject\RuleError;
use Symfony\Component\Finder\SplFileInfo;

final readonly class DuplicatedPatternRule implements RuleInterface
{
    public function __construct(
        private ContextDefinitionsAnalyzer $classMethodContextDefinitionsAnalyzer
    ) {
    }

    /**
     * @param SplFileInfo[] $contextFiles
     * @param SplFileInfo[] $featureFiles
     * @return RuleError[]
     */
    public function process(
        array $contextFiles,
        array $featureFiles,
        PatternCollection $patternCollection,
        string $projectDirectory
    ): array {
        // 1. find duplicated patterns, e.g. if 2 methods have the same pattern, its a race condition problem
        $classMethodContextDefinitions = $this->classMethodContextDefinitionsAnalyzer->resolve($contextFiles);

        $groupedByPattern = [];
        foreach ($classMethodContextDefinitions as $classMethodContextDefinition) {
            $groupedByPattern[$classMethodContextDefinition->getPattern()][] = $classMethodContextDefinition;
        }

        $ruleErrors = [];

        foreach ($groupedByPattern as $pattern => $sameMaksClassMethodContextDefinitions) {
            /** @var ContextDefinition[] $sameMaksClassMethodContextDefinitions */
            if (count($sameMaksClassMethodContextDefinitions) === 1) {
                continue;
            }

            $lineFilePaths = [];
            foreach ($sameMaksClassMethodContextDefinitions as $sameMakClassMethodContextDefinition) {
                $lineFilePaths[] = $sameMakClassMethodContextDefinition->getFilePath() . ':' . $sameMakClassMethodContextDefinition->getMethodLine();
            }

            $ruleErrors[] = new RuleError(sprintf(
                'Duplicated pattern "%s"',
                $pattern
            ), $lineFilePaths, $this->getIdentifier());
        }

        return $ruleErrors;
    }

    public function getIdentifier(): string
    {
        return RuleIdentifier::DUPLICATED_PATTERNS;
    }
}
