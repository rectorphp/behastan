<?php

declare(strict_types=1);

namespace Rector\Behastan\ValueObject;

use Rector\Behastan\Enum\RuleIdentifier;

final readonly class RuleError
{
    /**
     * @param string[] $lineFilePaths
     * @param RuleIdentifier::* $identifier
     */
    public function __construct(
        private string $message,
        private array $lineFilePaths,
        private string $identifier,
    ) {
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
