<?php

declare (strict_types=1);
namespace Rector\Behastan\Finder;

use Jack202512\Symfony\Component\Finder\Finder;
use Jack202512\Symfony\Component\Finder\SplFileInfo;
use Webmozart\Assert\Assert;
final class BehatMetafilesFinder
{
    /**
     * @param string[] $directories
     * @return SplFileInfo[]
     */
    public static function findContextFiles(array $directories): array
    {
        $filesFinder = self::createFinder($directories)->name('*Context.php');
        return iterator_to_array($filesFinder->getIterator());
    }
    /**
     * @param string[] $directories
     * @return SplFileInfo[]
     */
    public static function findFeatureFiles(array $directories): array
    {
        $filesFinder = self::createFinder($directories)->name('*.feature');
        return iterator_to_array($filesFinder->getIterator());
    }
    /**
     * @param string[] $directories
     */
    private static function createFinder(array $directories): Finder
    {
        Assert::allString($directories);
        Assert::allDirectory($directories);
        return Finder::create()->files()->notPath('vendor')->notPath('node_modules')->notPath('Fixture')->in($directories);
    }
}
