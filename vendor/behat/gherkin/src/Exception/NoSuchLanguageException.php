<?php

/*
 * This file is part of the Behat Gherkin Parser.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Behastan202601\Behat\Gherkin\Exception;

final class NoSuchLanguageException extends ParserException
{
    /**
     * @readonly
     * @var string
     */
    public $language;
    public function __construct(string $language)
    {
        $this->language = $language;
        parent::__construct('Language not supported: ' . $language);
    }
}
