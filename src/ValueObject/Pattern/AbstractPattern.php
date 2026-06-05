<?php

declare (strict_types=1);
namespace Rector\Behastan\ValueObject\Pattern;

use Rector\Behastan\Contract\PatternInterface;
abstract class AbstractPattern implements PatternInterface
{
    /**
     * @readonly
     * @var string
     */
    public $pattern;
    /**
     * @readonly
     * @var string
     */
    public $filePath;
    /**
     * @readonly
     * @var int
     */
    public $line;
    /**
     * @readonly
     * @var string
     */
    public $className;
    /**
     * @readonly
     * @var string
     */
    public $methodName;
    public function __construct(string $pattern, string $filePath, int $line, string $className, string $methodName)
    {
        $this->pattern = $pattern;
        $this->filePath = $filePath;
        $this->line = $line;
        $this->className = $className;
        $this->methodName = $methodName;
    }
}
