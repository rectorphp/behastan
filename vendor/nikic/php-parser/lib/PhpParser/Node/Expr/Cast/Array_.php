<?php

declare (strict_types=1);
namespace Jack202512\PhpParser\Node\Expr\Cast;

use Jack202512\PhpParser\Node\Expr\Cast;
class Array_ extends Cast
{
    public function getType(): string
    {
        return 'Expr_Cast_Array';
    }
}
