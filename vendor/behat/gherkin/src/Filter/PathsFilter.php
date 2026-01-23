<?php

/*
 * This file is part of the Behat Gherkin Parser.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Behastan202601\Behat\Gherkin\Filter;

use Behastan202601\Behat\Gherkin\Exception\FilesystemException;
use Behastan202601\Behat\Gherkin\Filesystem;
use Behastan202601\Behat\Gherkin\Node\FeatureNode;
use Behastan202601\Behat\Gherkin\Node\ScenarioInterface;
/**
 * Filters features by their paths.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class PathsFilter extends SimpleFilter
{
    /**
     * @var list<string>
     */
    protected $filterPaths = [];
    /**
     * Initializes filter.
     *
     * @param array<array-key, string> $paths List of approved paths
     */
    public function __construct(array $paths)
    {
        foreach ($paths as $path) {
            try {
                $realpath = Filesystem::getRealPath($path);
            } catch (FilesystemException $exception) {
                continue;
            }
            $this->filterPaths[] = rtrim($realpath, \DIRECTORY_SEPARATOR) . (is_dir($realpath) ? \DIRECTORY_SEPARATOR : '');
        }
    }
    public function isFeatureMatch(FeatureNode $feature)
    {
        if (($filePath = $feature->getFile()) === null) {
            return \false;
        }
        $realFeatureFilePath = Filesystem::getRealPath($filePath);
        foreach ($this->filterPaths as $filterPath) {
            if (strncmp($realFeatureFilePath, $filterPath, strlen($filterPath)) === 0) {
                return \true;
            }
        }
        return \false;
    }
    public function isScenarioMatch(ScenarioInterface $scenario)
    {
        // This filter does not apply to scenarios.
        return \false;
    }
}
