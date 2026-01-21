<?php

declare(strict_types=1);

namespace Rector\Behastan\Gherkin;

use Behat\Gherkin\Keywords\ArrayKeywords;
use Behat\Gherkin\Lexer;
use Behat\Gherkin\Node\FeatureNode;
use Behat\Gherkin\Parser;
use Webmozart\Assert\Assert;

final readonly class GherkinParser
{
    private Parser $parser;

    public function __construct()
    {
        $arrayKeywords = new ArrayKeywords([
            'en' => [
                'feature' => 'Feature',
                'background' => 'Background',
                'scenario' => 'Scenario',
                'scenario_outline' => 'Scenario Outline|Scenario Template',
                'examples' => 'Examples|Scenarios',
                'given' => 'Given',
                'when' => 'When',
                'then' => 'Then',
                'and' => 'And',
                'but' => 'But',
            ],
        ]);

        $this->parser = new Parser(new Lexer($arrayKeywords));
    }

    public function parseFile(string $filePath): FeatureNode
    {
        $featureNode = $this->parser->parseFile($filePath);
        Assert::isInstanceOf($featureNode, FeatureNode::class);

        return $featureNode;
    }
}
