<?php

/*
 * This file is part of the Behat Gherkin Parser.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Behastan202601\Behat\Gherkin\Loader;

use Behastan202601\Behat\Gherkin\Node\FeatureNode;
use Behastan202601\Symfony\Component\Yaml\Yaml;
/**
 * Yaml files loader.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * @extends AbstractFileLoader<string>
 *
 * @phpstan-import-type TArrayResource from ArrayLoader
 */
class YamlFileLoader extends AbstractFileLoader
{
    /**
     * @readonly
     * @var \Behat\Gherkin\Loader\LoaderInterface
     */
    private $loader;
    /**
     * @phpstan-param LoaderInterface<TArrayResource> $loader
     */
    public function __construct(?LoaderInterface $loader = null)
    {
        $loader = $loader ?? new ArrayLoader();
        $this->loader = $loader;
    }
    /**
     * @param mixed $resource
     */
    public function supports($resource)
    {
        return is_string($resource) && ($path = $this->findAbsolutePath($resource)) !== \false && is_file($path) && pathinfo($path, \PATHINFO_EXTENSION) === 'yml';
    }
    /**
     * @param mixed $resource
     */
    protected function doLoad($resource): array
    {
        $path = $this->getAbsolutePath($resource);
        $hash = Yaml::parseFile($path);
        // @phpstan-ignore argument.type
        $features = $this->loader->load($hash);
        return array_map(static function (FeatureNode $feature) use ($path) {
            return new FeatureNode($feature->getTitle(), $feature->getDescription(), $feature->getTags(), $feature->getBackground(), $feature->getScenarios(), $feature->getKeyword(), $feature->getLanguage(), $path, $feature->getLine());
        }, $features);
    }
}
