<?php

declare (strict_types=1);
namespace Behastan202601\PhpParser\Node\Expr\BinaryOp;

use Behastan202601\PhpParser\Node\Expr\BinaryOp;
class GreaterOrEqual extends BinaryOp
{
    public function getOperatorSigil(): string
    {
        return '>=';
    }
    public function getType(): string
    {
        return 'Expr_BinaryOp_GreaterOrEqual';
    }
}
