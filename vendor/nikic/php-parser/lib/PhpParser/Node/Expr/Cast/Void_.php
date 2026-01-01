<?php

declare (strict_types=1);
namespace Behastan202601\PhpParser\Node\Expr\Cast;

use Behastan202601\PhpParser\Node\Expr\Cast;
class Void_ extends Cast
{
    public function getType(): string
    {
        return 'Expr_Cast_Void';
    }
}
