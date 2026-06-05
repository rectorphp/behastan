<?php

declare (strict_types=1);
namespace Rector\Behastan\ValueObject;

use Rector\Behastan\Enum\RuleIdentifier;
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
     * @var RuleIdentifier::*
     * @readonly
     */
    private $identifier;
    /**
     * @param string[] $lineFilePaths
     * @param RuleIdentifier::* $identifier
     */
    public function __construct(string $message, array $lineFilePaths, string $identifier)
    {
        $this->message = $message;
        $this->lineFilePaths = $lineFilePaths;
        $this->identifier = $identifier;
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
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
}
