<?php

declare(strict_types=1);

namespace Rector\Behastan\Rule;

use Rector\Behastan\Analyzer\ContextDefinitionsAnalyzer;
use Rector\Behastan\Contract\RuleInterface;
use Rector\Behastan\Enum\RuleIdentifier;
use Rector\Behastan\ValueObject\ContextDefinition;
use Rector\Behastan\ValueObject\MaskCollection;
use Rector\Behastan\ValueObject\RuleError;
use Symfony\Component\Finder\SplFileInfo;

final readonly class DuplicatedMaskRule implements RuleInterface
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
        MaskCollection $maskCollection,
        string $projectDirectory
    ): array {
        // 1. find duplicated masks, e.g. if 2 methods have the same mask, its a race condition problem
        $classMethodContextDefinitions = $this->classMethodContextDefinitionsAnalyzer->resolve($contextFiles);

        $groupedByMask = [];
        foreach ($classMethodContextDefinitions as $classMethodContextDefinition) {
            $groupedByMask[$classMethodContextDefinition->getMask()][] = $classMethodContextDefinition;
        }

        $ruleErrors = [];

        foreach ($groupedByMask as $mask => $sameMaksClassMethodContextDefinitions) {
            /** @var ContextDefinition[] $sameMaksClassMethodContextDefinitions */
            if (count($sameMaksClassMethodContextDefinitions) === 1) {
                continue;
            }

            $lineFilePaths = [];
            foreach ($sameMaksClassMethodContextDefinitions as $sameMakClassMethodContextDefinition) {
                $relativeFilePath = substr(
                    $sameMakClassMethodContextDefinition->getFilePath(),
                    strlen($projectDirectory) + 1
                );
                $lineFilePaths[] = $relativeFilePath . ':' . $sameMakClassMethodContextDefinition->getMethodLine();
            }

            $ruleErrors[] = new RuleError(sprintf(
                'Duplicated mask "%s"',
                $mask
            ), $lineFilePaths, $this->getIdentifier());
        }

        return $ruleErrors;
    }

    public function getIdentifier(): string
    {
        return RuleIdentifier::DUPLICATED_MASKS;
    }
}
