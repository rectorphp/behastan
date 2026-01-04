<?php

declare(strict_types=1);

namespace Rector\Behastan\Rule;

use Rector\Behastan\Analyzer\UnusedDefinitionsAnalyzer;
use Rector\Behastan\Contract\RuleInterface;
use Rector\Behastan\Enum\RuleIdentifier;
use Rector\Behastan\ValueObject\PatternCollection;
use Rector\Behastan\ValueObject\RuleError;
use Symfony\Component\Finder\SplFileInfo;

final readonly class UnusedContextDefinitionsRule implements RuleInterface
{
    public function __construct(
        private UnusedDefinitionsAnalyzer $unusedDefinitionsAnalyzer
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
        $unusedPatterns = $this->unusedDefinitionsAnalyzer->analyse($contextFiles, $featureFiles, $patternCollection);

        $ruleErrors = [];

        foreach ($unusedPatterns as $unusedPattern) {
            $errorMessage = sprintf(
                'The pattern "%s" and its definition %s::%s() is never used',
                $unusedPattern->pattern,
                $unusedPattern->className,
                $unusedPattern->methodName
            );

            $ruleErrors[] = new RuleError(
                $errorMessage,
                [$unusedPattern->filePath . ':' . $unusedPattern->line],
                $this->getIdentifier()
            );
        }

        return $ruleErrors;
    }

    public function getIdentifier(): string
    {
        return RuleIdentifier::UNUSED_DEFINITIONS;
    }
}
