<?php
namespace Helmich\Namespacify\Ast\Printer;


use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\PrettyPrinter\Standard;


class StandardPrinter extends Standard
{



    public function pStmt_ClassMethod(ClassMethod $node)
    {
        if ($node->stmts !== NULL && count($node->stmts) > 0)
        {
            $content = "\n" . $this->pStmts($node->stmts) . "\n";
        }
        else
        {
            $content = "";
        }

        return $this->pModifiers($node->type)
        . 'function ' . ($node->byRef ? '&' : '') . $node->name
        . '(' . $this->pCommaSeparated($node->params) . ')'
        . (NULL !== $node->stmts
            ? "\n" . '{' . $content . '}'
            : ';');
    }



    public function pStmt_Function(Function_ $node)
    {
        if (count($node->stmts) > 0)
        {
            $content = "\n" . $this->pStmts($node->stmts) . "\n";
        }
        else
        {
            $content = "";
        }

        return 'function ' . ($node->byRef ? '&' : '') . $node->name
        . '(' . $this->pCommaSeparated($node->params) . ')'
        . "\n" . '{' . $content . '}';
    }

}