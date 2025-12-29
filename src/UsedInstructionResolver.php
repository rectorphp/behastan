<?php

declare (strict_types=1);
namespace Rector\Behastan;

use Behastan202512\Nette\Utils\Strings;
use RuntimeException;
use Behastan202512\Symfony\Component\Finder\SplFileInfo;
/**
 * @see \Rector\Behastan\Tests\UsedInstructionResolver\UsedInstructionResolverTest
 */
final class UsedInstructionResolver
{
    /**
     * @param SplFileInfo[] $featureFileInfos
     * @return string[]
     */
    public function resolveInstructionsFromFeatureFiles(array $featureFileInfos): array
    {
        $instructions = [];
        foreach ($featureFileInfos as $featureFileInfo) {
            $matches = Strings::matchAll(
                // newline is needed, as file can end with no \n
                $featureFileInfo->getContents() . \PHP_EOL,
                '#\s+(Given|When|And|Then)\s+(?<instruction>.*?)\n#m'
            );
            if ($matches === []) {
                // there should be at least one instruction in each feature file
                throw new RuntimeException(sprintf('Unable to extract instructions from %s file', $featureFileInfo->getRealPath()));
            }
            foreach ($matches as $match) {
                $instructions[] = trim((string) $match['instruction']);
            }
        }
        return $instructions;
    }
}
