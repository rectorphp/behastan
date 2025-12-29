<?php

declare (strict_types=1);
namespace Rector\Behastan\ValueObject\Mask;

use Rector\Behastan\Contract\MaskInterface;
abstract class AbstractMask implements MaskInterface
{
    /**
     * @readonly
     * @var string
     */
    public $mask;
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
    public function __construct(string $mask, string $filePath, int $line, string $className, string $methodName)
    {
        $this->mask = $mask;
        $this->filePath = $filePath;
        $this->line = $line;
        $this->className = $className;
        $this->methodName = $methodName;
    }
}
