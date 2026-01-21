<?php

declare(strict_types=1);

namespace Rector\Behastan\Analyzer;

use Nette\Utils\Strings;
use Rector\Behastan\UsedInstructionResolver;
use Rector\Behastan\ValueObject\Pattern\AbstractPattern;
use Rector\Behastan\ValueObject\Pattern\ExactPattern;
use Rector\Behastan\ValueObject\Pattern\NamedPattern;
use Rector\Behastan\ValueObject\Pattern\RegexPattern;
use Rector\Behastan\ValueObject\Pattern\SkippedPattern;
use Rector\Behastan\ValueObject\PatternCollection;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;

/**
 * @see \Rector\Behastan\Tests\Analyzer\UnusedDefinitionsAnalyzer\UnusedDefinitionsAnalyzerTest
 */
final readonly class UnusedDefinitionsAnalyzer
{
    private const string PATTERN_VALUE_REGEX = '#(\:[\W\w]+)#';

    public function __construct(
        private UsedInstructionResolver $usedInstructionResolver,
    ) {
    }

    /**
     * @param SplFileInfo[] $featureFiles
     *
     * @return AbstractPattern[]
     */
    public function analyse(array $featureFiles, PatternCollection $patternCollection): array
    {
        foreach ($featureFiles as $featureFile) {
            Assert::endsWith($featureFile->getFilename(), '.feature');
        }

        $featureInstructions = $this->usedInstructionResolver->resolveInstructionsFromFeatureFiles($featureFiles);

        $unusedPatterns = [];
        foreach ($patternCollection->all() as $pattern) {
            if ($this->isPatternUsed($pattern, $featureInstructions)) {
                continue;
            }

            $unusedPatterns[] = $pattern;
        }

        return $unusedPatterns;
    }

    /**
     * @param string[] $featureInstructions
     */
    private function isRegexDefinitionUsed(string $regexBehatDefinition, array $featureInstructions): bool
    {
        foreach ($featureInstructions as $featureInstruction) {
            if (Strings::match($featureInstruction, $regexBehatDefinition)) {
                // it is used!
                return true;
            }
        }

        return false;
    }

    /**
     * @param string[] $featureInstructions
     */
    private function isPatternUsed(AbstractPattern $pattern, array $featureInstructions): bool
    {
        if ($pattern instanceof SkippedPattern) {
            return true;
        }

        // is used?
        if ($pattern instanceof ExactPattern && in_array($pattern->pattern, $featureInstructions, true)) {
            return true;
        }

        // is used?
        if ($pattern instanceof RegexPattern && $this->isRegexDefinitionUsed($pattern->pattern, $featureInstructions)) {
            return true;
        }

        if ($pattern instanceof NamedPattern) {
            // normalize :pattern definition to regex
            $regexPattern = '#' . Strings::replace($pattern->pattern, self::PATTERN_VALUE_REGEX, '(.*?)') . '#';

            if ($this->isRegexDefinitionUsed($regexPattern, $featureInstructions)) {
                return true;
            }
        }

        return false;
    }
}
