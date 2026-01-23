<?php

/*
 * This file is part of the Behat Gherkin Parser.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Behastan202601\Behat\Gherkin;

class GherkinCompatibilityMode
{
    public const LEGACY = 'legacy';
    public const GHERKIN_32 = 'gherkin-32';
    /**
     * @internal
     */
    public function shouldRemoveStepKeywordSpace(): bool
    {
        switch ($this) {
            case self::LEGACY:
                return \true;
            default:
                return \false;
        }
    }
    /**
     * @internal
     */
    public function shouldRemoveDescriptionPadding(): bool
    {
        switch ($this) {
            case self::LEGACY:
                return \true;
            default:
                return \false;
        }
    }
    /**
     * @internal
     */
    public function allowAllNodeDescriptions(): bool
    {
        switch ($this) {
            case self::LEGACY:
                return \false;
            default:
                return \true;
        }
    }
    /**
     * @internal
     */
    public function shouldUseNewTableCellParsing(): bool
    {
        switch ($this) {
            case self::LEGACY:
                return \false;
            default:
                return \true;
        }
    }
    /**
     * @internal
     */
    public function shouldUnespaceDocStringDelimiters(): bool
    {
        switch ($this) {
            case self::LEGACY:
                return \false;
            default:
                return \true;
        }
    }
    /**
     * @internal
     */
    public function shouldIgnoreInvalidLanguage(): bool
    {
        switch ($this) {
            case self::LEGACY:
                return \true;
            default:
                return \false;
        }
    }
    /**
     * @internal
     */
    public function allowWhitespaceInLanguageTag(): bool
    {
        switch ($this) {
            case self::LEGACY:
                return \false;
            default:
                return \true;
        }
    }
    /**
     * @internal
     */
    public function shouldRemoveTagPrefixChar(): bool
    {
        switch ($this) {
            case self::LEGACY:
                return \true;
            default:
                return \false;
        }
    }
    /**
     * @internal
     */
    public function shouldThrowOnWhitespaceInTag(): bool
    {
        switch ($this) {
            case self::LEGACY:
                return \false;
            default:
                return \true;
        }
    }
}
