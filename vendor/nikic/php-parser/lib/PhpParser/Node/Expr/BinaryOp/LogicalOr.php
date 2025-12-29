<?php

declare (strict_types=1);
namespace Jack202512\PhpParser\Node\Expr\BinaryOp;

use Jack202512\PhpParser\Node\Expr\BinaryOp;
class LogicalOr extends BinaryOp
{
    public function getOperatorSigil(): string
    {
        return 'or';
    }
    public function getType(): string
    {
        return 'Expr_BinaryOp_LogicalOr';
    }
}
