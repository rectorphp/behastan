<?php

declare (strict_types=1);
namespace Jack202512\PhpParser\Node;

use Jack202512\PhpParser\Node;
class NullableType extends ComplexType
{
    /** @var Identifier|Name Type */
    public $type;
    /**
     * Constructs a nullable type (wrapping another type).
     *
     * @param Identifier|Name $type Type
     * @param array<string, mixed> $attributes Additional attributes
     */
    public function __construct(Node $type, array $attributes = [])
    {
        $this->attributes = $attributes;
        $this->type = $type;
    }
    public function getSubNodeNames(): array
    {
        return ['type'];
    }
    public function getType(): string
    {
        return 'NullableType';
    }
}
