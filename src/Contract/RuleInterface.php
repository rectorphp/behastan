<?php

declare (strict_types=1);
namespace Rector\Behastan\Contract;

use Rector\Behastan\Enum\RuleIdentifier;
use Rector\Behastan\ValueObject\MaskCollection;
use Rector\Behastan\ValueObject\RuleError;
use Behastan202512\Symfony\Component\Finder\SplFileInfo;
interface RuleInterface
{
    /**
     * @param SplFileInfo[] $contextFiles
     * @param SplFileInfo[] $featureFiles
     *
     * @return RuleError[]
     */
    public function process(array $contextFiles, array $featureFiles, MaskCollection $maskCollection, string $projectDirectory): array;
    /**
     * @return RuleIdentifier::*
     */
    public function getIdentifier(): string;
}
