<?php
namespace Helmich\Namespacify\Ast\NodeVisitor;


use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class NamespaceConverterVisitor extends NodeVisitorAbstract
{
    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Name)
        {
            return new Node\Name($node->toString('_'));
        }
        elseif ($node instanceof Node\Stmt\Class_
            || $node instanceof Node\Stmt\Interface_
            || $node instanceof Node\Stmt\Function_
        )
        {
            $node->name = $node->namespacedName->toString('_');
        }
        elseif ($node instanceof Node\Stmt\Const_)
        {
            foreach ($node->consts as $const)
            {
                $const->name = $const->namespacedName->toString('_');
            }
        }
        elseif ($node instanceof Node\Stmt\Namespace_)
        {
            // returning an array merges is into the parent array
            return $node->stmts;
        }
        elseif ($node instanceof Node\Stmt\Use_)
        {
            // returning false removed the node altogether
            return FALSE;
        }
    }
} 