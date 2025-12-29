<?php

declare (strict_types=1);
namespace Jack202512\PhpParser\Node\Expr\Cast;

use Jack202512\PhpParser\Node\Expr\Cast;
class Bool_ extends Cast
{
    // For use in "kind" attribute
    public const KIND_BOOL = 1;
    // "bool" syntax
    public const KIND_BOOLEAN = 2;
    // "boolean" syntax
    public function getType(): string
    {
        return 'Expr_Cast_Bool';
    }
}
