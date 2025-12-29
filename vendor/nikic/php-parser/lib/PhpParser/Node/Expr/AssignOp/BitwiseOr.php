<?php

declare (strict_types=1);
namespace Jack202512\PhpParser\Node\Expr\AssignOp;

use Jack202512\PhpParser\Node\Expr\AssignOp;
class BitwiseOr extends AssignOp
{
    public function getType(): string
    {
        return 'Expr_AssignOp_BitwiseOr';
    }
}
