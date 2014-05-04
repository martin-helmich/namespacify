<?php
namespace Helmich\Namespacify\Ast\NodeVisitor;


use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;

class BackwardNamespaceConverterVisitor extends NodeVisitorAbstract
{



    /** @var string */
    private $sourceNamespace;


    /** @var string */
    private $targetNamespace;



    public function __construct($sourceNamespace, $targetNamespace)
    {
        $this->sourceNamespace = trim($sourceNamespace, '\\');
        $this->targetNamespace = $targetNamespace;
    }



    private function isClassMatchingSourceNamespace($className)
    {
        return TRUE;
    }



    private function convertClassName($oldClassName)
    {
        $oldClassName = trim("" . $oldClassName, '\\');
        $newClassName = $oldClassName;

        if ($this->isClassMatchingSourceNamespace($oldClassName))
        {
            $newClassName = str_replace($this->sourceNamespace, $this->targetNamespace, $newClassName);
            $newClassName = str_replace('\\', '_', $newClassName);

            $newClassName = trim($newClassName, '_');
        }

        return $newClassName;
    }



    public function leaveNode(Node $node)
    {
        if ($node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Interface_ || $node instanceof Node\Stmt\Function_)
        {
            $node->name = new Node\Name($this->convertClassName($node->namespacedName->toString()));
        }
        elseif ($node instanceof Node\Name)
        {
            return new Node\Name($this->convertClassName($node->toString()));
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

        return NULL;
    }
}