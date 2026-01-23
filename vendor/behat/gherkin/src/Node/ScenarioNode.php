<?php

/*
 * This file is part of the Behat Gherkin Parser.
 * (c) Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Behastan202601\Behat\Gherkin\Node;

/**
 * Represents Gherkin Scenario.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * @final since 4.15.0
 */
class ScenarioNode implements ScenarioInterface, NamedScenarioInterface, DescribableNodeInterface
{
    /**
     * @readonly
     * @var string|null
     */
    private $title;
    /**
     * @var list<string>
     * @readonly
     */
    private $tags;
    /**
     * @var StepNode[]
     * @readonly
     */
    private $steps;
    /**
     * @readonly
     * @var string
     */
    private $keyword;
    /**
     * @readonly
     * @var int
     */
    private $line;
    /**
     * @readonly
     * @var string|null
     */
    private $description;
    use TaggedNodeTrait;
    /**
     * @param StepNode[] $steps
     * @param list<string> $tags
     */
    public function __construct(?string $title, array $tags, array $steps, string $keyword, int $line, ?string $description = null)
    {
        $this->title = $title;
        $this->tags = $tags;
        $this->steps = $steps;
        $this->keyword = $keyword;
        $this->line = $line;
        $this->description = $description;
    }
    /**
     * Returns node type string.
     *
     * @return string
     */
    public function getNodeType()
    {
        return 'Scenario';
    }
    /**
     * Returns scenario title.
     *
     * @return string|null
     *
     * @deprecated you should use {@see self::getName()} instead as this method will be removed in the next
     *             major version
     */
    public function getTitle()
    {
        return $this->title;
    }
    public function getName(): ?string
    {
        return $this->title;
    }
    public function getDescription(): ?string
    {
        return $this->description;
    }
    public function getTags()
    {
        return $this->tags;
    }
    /**
     * Checks if scenario has steps.
     *
     * @return bool
     */
    public function hasSteps()
    {
        return count($this->steps) > 0;
    }
    /**
     * Returns scenario steps.
     *
     * @return StepNode[]
     */
    public function getSteps()
    {
        return $this->steps;
    }
    /**
     * Returns scenario keyword.
     *
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }
    /**
     * Returns scenario declaration line number.
     *
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }
}
