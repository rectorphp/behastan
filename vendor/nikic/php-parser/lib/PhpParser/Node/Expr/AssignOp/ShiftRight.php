<?php

declare (strict_types=1);
namespace Behastan202601\PhpParser\Node\Expr\AssignOp;

use Behastan202601\PhpParser\Node\Expr\AssignOp;
class ShiftRight extends AssignOp
{
    public function getType(): string
    {
        return 'Expr_AssignOp_ShiftRight';
    }
}
