<?php

declare (strict_types=1);
namespace Jack202512\PhpParser\Node\Stmt;

use Jack202512\PhpParser\Node;
abstract class TraitUseAdaptation extends Node\Stmt
{
    /** @var Node\Name|null Trait name */
    public $trait;
    /** @var Node\Identifier Method name */
    public $method;
}
