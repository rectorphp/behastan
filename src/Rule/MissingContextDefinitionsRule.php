<?php

declare(strict_types=1);

namespace Rector\Behastan\Rule;

use Behat\Config\Config;
use Rector\Behastan\Contract\RuleInterface;
use Rector\Behastan\DefinitionPatternsExtractor;
use Rector\Behastan\Enum\RuleIdentifier;
use Rector\Behastan\Finder\BehatMetafilesFinder;
use Rector\Behastan\UsedInstructionResolver;
use Rector\Behastan\ValueObject\Pattern\NamedPattern;
use Rector\Behastan\ValueObject\PatternCollection;
use Rector\Behastan\ValueObject\RuleError;
use Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;

// WIP
/**
 * @todo extract service and test heavily
 */
final readonly class MissingContextDefinitionsRule implements RuleInterface
{
    public function __construct(
        private UsedInstructionResolver $usedInstructionResolver,
        private DefinitionPatternsExtractor $definitionPatternsExtractor,
    ) {
    }

    /**
     * @param SplFileInfo[] $contextFiles
     * @param SplFileInfo[] $featureFiles
     *
     * @return RuleError[]
     */
    public function process(
        array $contextFiles,
        array $featureFiles,
        PatternCollection $patternCollection,
        string $projectDirectory
    ): array {
        $behatConfigFile = getcwd() . '/behat.php';

        // nothing to analyse
        if (! file_exists($behatConfigFile)) {
            return [];
        }

        /** @var Config $behatConfiguration */
        $behatConfiguration = require_once $behatConfigFile;

        $suites = $behatConfiguration->toArray()['profile']['suites'] ?? [];

        // most likely a bug, as at least one suite is expected
        if ($suites === []) {
            return [];
        }

        $featureInstructionsWithoutDefinitions = [];

        foreach ($suites as $suiteName => $suiteConfiguration) {
            // 1. find definitions in paths
            $suiteFeatureFiles = BehatMetafilesFinder::findFeatureFiles($suiteConfiguration['paths']);
            Assert::notEmpty($suiteFeatureFiles);

            $suiteContextFilePaths = $this->resolveClassesFilePaths($suiteConfiguration['contexts']);
            Assert::notEmpty($suiteContextFilePaths);

            $suiteFeatureInstructions = $this->usedInstructionResolver->resolveInstructionsFromFeatureFiles(
                $suiteFeatureFiles
            );

            // feature-used instructions
            Assert::notEmpty($suiteFeatureInstructions);

            // definitions-provided instructions
            $suitePatternCollection = $this->definitionPatternsExtractor->extract($suiteContextFilePaths);

            $featureInstructionsWithoutDefinitions = [];
            foreach ($suiteFeatureInstructions as $featureInstruction) {
                if ($this->isFeatureFoundInDefinitionPatterns($featureInstruction, $suitePatternCollection)) {
                    continue;
                }

                $featureInstructionsWithoutDefinitions[$suiteName][] = $featureInstruction;
            }
        }

        $ruleErrors = [];
        foreach ($featureInstructionsWithoutDefinitions as $suiteName => $featureInstructions) {
            // WIP @todo
            $ruleErrors[] = new RuleError(
                sprintf(
                    'Suite %s is missing Context definitions for %d feature instructions: %s',
                    $suiteName,
                    count($featureInstructions),
                    implode(PHP_EOL . ' * ', $featureInstructions)
                ),
                [],
                $this->getIdentifier()
            );
        }

        return $ruleErrors;
    }

    public function getIdentifier(): string
    {
        return RuleIdentifier::MISSING_CONTEXT_DEFINITIONS;
    }

    /**
     * @param class-string[] $contextClasses
     * @return SplFileInfo[]
     */
    private function resolveClassesFilePaths(array $contextClasses): array
    {
        $contextFilePaths = [];

        foreach ($contextClasses as $contextClass) {
            $reflectionClass = new \ReflectionClass($contextClass);
            $fileName = $reflectionClass->getFileName();
            if ($fileName === false) {
                continue;
            }

            $contextFilePaths[] = new SplFileInfo($fileName, '', '');
        }

        return $contextFilePaths;
    }

    private function isFeatureFoundInDefinitionPatterns(
        string $featureInstruction,
        PatternCollection $suitePatternCollection
    ): bool {
        // 1. is feature used in exact pattern?
        if (in_array($featureInstruction, $suitePatternCollection->exactPatternStrings())) {
            return true;
        }

        // 2. is feature used in named pattern?
        $namedPatterns = $suitePatternCollection->byType(NamedPattern::class);
        foreach ($namedPatterns as $namedPattern) {
            if (\Nette\Utils\Strings::match($featureInstruction, $namedPattern->getRegexPattern())) {
                return true;
            }
        }

        // 3. is feature used in regex pattern?
        foreach ($suitePatternCollection->regexPatternsStrings() as $regexPatternString) {
            if (\Nette\Utils\Strings::match($featureInstruction, $regexPatternString)) {
                return true;
            }
        }

        return false;
    }
}
