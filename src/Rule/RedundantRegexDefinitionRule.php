<?php

declare(strict_types=1);

namespace Rector\Behastan\Rule;

use Rector\Behastan\Contract\RuleInterface;
use Rector\Behastan\Enum\RuleIdentifier;
use Rector\Behastan\ValueObject\Pattern\RegexPattern;
use Rector\Behastan\ValueObject\PatternCollection;
use Rector\Behastan\ValueObject\RuleError;
use Symfony\Component\Finder\SplFileInfo;

final readonly class RedundantRegexDefinitionRule implements RuleInterface
{
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
        $ruleErrors = [];

        /** @var RegexPattern[] $regexPatterns */
        $regexPatterns = $patternCollection->byType(RegexPattern::class);

        foreach ($regexPatterns as $regexPattern) {
            // is regex pattern more then just exact start + end?
            if ($regexPattern->isRegexPatternNeccessary()) {
                continue;
            }

            $errorMessage = sprintf(
                'The regex pattern "%s" is redundant. Remove start + end and use plain string exact string instead.',
                $regexPattern->pattern,
            );

            $ruleErrors[] = new RuleError(
                $errorMessage,
                [$regexPattern->filePath . ':' . $regexPattern->line],
                $this->getIdentifier()
            );
        }

        return $ruleErrors;
    }

    public function getIdentifier(): string
    {
        return RuleIdentifier::REDUNDANT_REGEX_DEFINITION;
    }
}
