<?php

declare (strict_types=1);
namespace Rector\Behastan\Gherkin;

use Behastan202601\Behat\Gherkin\Keywords\ArrayKeywords;
use Behastan202601\Behat\Gherkin\Lexer;
use Behastan202601\Behat\Gherkin\Node\FeatureNode;
use Behastan202601\Behat\Gherkin\Parser;
use Behastan202601\Entropy\Utils\FileSystem;
final class GherkinParser
{
    /**
     * @readonly
     * @var \Behat\Gherkin\Parser
     */
    private $parser;
    public function __construct()
    {
        $arrayKeywords = new ArrayKeywords(['en' => ['feature' => 'Feature', 'background' => 'Background', 'scenario' => 'Scenario', 'scenario_outline' => 'Scenario Outline|Scenario Template', 'examples' => 'Examples|Scenarios', 'given' => 'Given', 'when' => 'When', 'then' => 'Then', 'and' => 'And', 'but' => 'But']]);
        $this->parser = new Parser(new Lexer($arrayKeywords));
    }
    public function parseFile(string $filePath): ?FeatureNode
    {
        $fileContents = FileSystem::read($filePath);
        return $this->parser->parse($fileContents);
    }
}
