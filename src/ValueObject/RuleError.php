<?php

declare (strict_types=1);
namespace Rector\Behastan\ValueObject;

final class RuleError
{
    /**
     * @readonly
     * @var string
     */
    private $message;
    /**
     * @var string[]
     * @readonly
     */
    private $lineFilePaths;
    /**
     * @param string[] $lineFilePaths
     */
    public function __construct(string $message, array $lineFilePaths)
    {
        $this->message = $message;
        $this->lineFilePaths = $lineFilePaths;
    }
    public function getMessage(): string
    {
        return $this->message;
    }
    /**
     * @return string[]
     */
    public function getLineFilePaths(): array
    {
        return $this->lineFilePaths;
    }
}
