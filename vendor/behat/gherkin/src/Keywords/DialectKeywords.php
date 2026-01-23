<?php

/*
 * This file is part of the Behat Gherkin Parser.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Behastan202601\Behat\Gherkin\Keywords;

use Behastan202601\Behat\Gherkin\Dialect\DialectProviderInterface;
use Behastan202601\Behat\Gherkin\Dialect\GherkinDialect;
/**
 * An adapter around a DialectProviderInterface to be able to use it with the KeywordsDumper.
 *
 * TODO add support for dumping an example feature for a dialect directly instead.
 *
 * @internal
 */
final class DialectKeywords implements KeywordsInterface
{
    /**
     * @readonly
     * @var \Behat\Gherkin\Dialect\DialectProviderInterface
     */
    private $dialectProvider;
    /**
     * @var \Behat\Gherkin\Dialect\GherkinDialect
     */
    private $currentDialect;
    public function __construct(DialectProviderInterface $dialectProvider)
    {
        $this->dialectProvider = $dialectProvider;
        $this->currentDialect = $this->dialectProvider->getDefaultDialect();
    }
    public function setLanguage(string $language): void
    {
        if ($language === '') {
            throw new \InvalidArgumentException('Language cannot be empty');
        }
        $this->currentDialect = $this->dialectProvider->getDialect($language);
    }
    public function getFeatureKeywords(): string
    {
        return $this->getKeywordString($this->currentDialect->getFeatureKeywords());
    }
    public function getBackgroundKeywords(): string
    {
        return $this->getKeywordString($this->currentDialect->getBackgroundKeywords());
    }
    public function getScenarioKeywords(): string
    {
        return $this->getKeywordString($this->currentDialect->getScenarioKeywords());
    }
    public function getOutlineKeywords(): string
    {
        return $this->getKeywordString($this->currentDialect->getScenarioOutlineKeywords());
    }
    public function getExamplesKeywords(): string
    {
        return $this->getKeywordString($this->currentDialect->getExamplesKeywords());
    }
    public function getGivenKeywords(): string
    {
        return $this->getStepKeywordString($this->currentDialect->getGivenKeywords());
    }
    public function getWhenKeywords(): string
    {
        return $this->getStepKeywordString($this->currentDialect->getWhenKeywords());
    }
    public function getThenKeywords(): string
    {
        return $this->getStepKeywordString($this->currentDialect->getThenKeywords());
    }
    public function getAndKeywords(): string
    {
        return $this->getStepKeywordString($this->currentDialect->getAndKeywords());
    }
    public function getButKeywords(): string
    {
        return $this->getStepKeywordString($this->currentDialect->getButKeywords());
    }
    public function getStepKeywords(): string
    {
        return $this->getStepKeywordString($this->currentDialect->getStepKeywords());
    }
    /**
     * @param list<string> $keywords
     */
    private function getKeywordString(array $keywords): string
    {
        return implode('|', $keywords);
    }
    /**
     * @param list<string> $keywords
     */
    private function getStepKeywordString(array $keywords): string
    {
        $legacyKeywords = [];
        foreach ($keywords as $keyword) {
            if (substr_compare($keyword, ' ', -strlen(' ')) === 0) {
                $legacyKeywords[] = (string) substr($keyword, 0, -1);
            } else {
                $legacyKeywords[] = $keyword . '<';
            }
        }
        return implode('|', $legacyKeywords);
    }
}
