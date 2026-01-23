<?php

/*
 * This file is part of the Behat Gherkin Parser.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Behastan202601\Behat\Gherkin\Exception;

use Behastan202601\Behat\Gherkin\Lexer;
/**
 * @phpstan-import-type TToken from Lexer
 */
class UnexpectedTaggedNodeException extends ParserException
{
    /**
     * @readonly
     * @var mixed[]
     */
    public $taggedToken;
    /**
     * @readonly
     * @var string|null
     */
    public $sourceFile;
    /**
     * @phpstan-param TToken $taggedToken
     */
    public function __construct(array $taggedToken, ?string $sourceFile)
    {
        $this->taggedToken = $taggedToken;
        $this->sourceFile = $sourceFile;
        switch ($this->taggedToken['type']) {
            case 'EOS':
                $msg = 'Unexpected end of file after tags';
                break;
            default:
                $msg = sprintf('%s can not be tagged, but it is', $taggedToken['type']);
                break;
        }
        parent::__construct(sprintf('%s on line: %d%s', $msg, $taggedToken['line'], $this->sourceFile ? " in file: {$this->sourceFile}" : ''));
    }
}
