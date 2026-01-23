<?php

/*
 * This file is part of the Behat Gherkin Parser.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Behastan202601\Behat\Gherkin\Exception;

use Behastan202601\Behat\Gherkin\Node\NodeInterface;
class UnexpectedParserNodeException extends ParserException
{
    /**
     * @readonly
     * @var string
     */
    public $expectation;
    /**
     * @readonly
     * @var string|\Behat\Gherkin\Node\NodeInterface
     */
    public $node;
    /**
     * @readonly
     * @var string|null
     */
    public $sourceFile;
    /**
     * @param string|\Behat\Gherkin\Node\NodeInterface $node
     */
    public function __construct(string $expectation, $node, ?string $sourceFile)
    {
        $this->expectation = $expectation;
        $this->node = $node;
        $this->sourceFile = $sourceFile;
        parent::__construct(sprintf('Expected %s, but got %s%s', $expectation, is_string($node) ? "text: \"{$node}\"" : "{$node->getNodeType()} on line: {$node->getLine()}", $sourceFile ? " in file: {$sourceFile}" : ''));
    }
}
