<?php
namespace Helmich\Namespacify\Ast\NodeVisitor;

/*
 * This file is part of namespacify.
 * https://github.com/martin-helmich/namespacify
 *
 * (C) 2014 Martin Helmich <kontakt@martin-helmich.de>
 *
 * For license information, view the LICENSE.md file.
 */

use PhpParser\Comment;
use PhpParser\Node;
use PhpParser\NodeAbstract;
use PhpParser\NodeVisitorAbstract;

/**
 * A node visitor that transforms pseudo-namespaced class names into namespaced class names.
 **
 * @author     Martin Helmich <kontakt@martin-helmich.de>
 * @license    The MIT License
 * @package    Helmich\Namespacify
 * @subpackage Ast\NodeVisitor
 */
class ForwardNamespaceConverterVisitor extends NodeVisitorAbstract
{



    /** @var string */
    private $sourceNamespace;


    /** @var string */
    private $targetNamespace;


    /** @var \PhpParser\Node\Stmt\Namespace_[] */
    private $namespaceNodes = [];


    private $imports = [];



    public function __construct($sourceNamespace, $targetNamespace)
    {
        $this->sourceNamespace = $sourceNamespace;
        $this->targetNamespace = $targetNamespace;
    }



    private function isClassMatchingSourceNamespace($className)
    {
        return strpos($className, $this->sourceNamespace) === 0;
    }



    private function convertClassName($oldClassName)
    {
        $newClassName = "" . $oldClassName;
        if ($this->isClassMatchingSourceNamespace($oldClassName))
        {
            $newClassName = str_replace($this->sourceNamespace, $this->targetNamespace, $newClassName);
            $newClassName = str_replace('_', '\\', $newClassName);

            $newClassName = trim($newClassName, '\\');
        }
        return $newClassName;
    }



    private function splitClassAndNamespace($className)
    {
        $parts = explode('\\', $className);
        $uqcn  = array_pop($parts);

        return [implode('\\', $parts), $uqcn];
    }



    public function afterTraverse(array $nodes)
    {
        if (count($this->imports) > 0)
        {
            $use = new Node\Stmt\Use_(
                array_map(
                    function ($className)
                    {
                        return new Node\Stmt\UseUse($className);
                    },
                    $this->imports
                )
            );

            if (count($this->namespaceNodes) > 0)
            {
                foreach ($this->namespaceNodes as $namespaceNode)
                {
                    $namespaceNode->stmts = array_merge([$use], $namespaceNode->stmts);
                }
            }
            else
            {
                $nodes = array_merge([$use], $nodes);
            }
        }
        return $nodes;
    }



    private function replaceClassNamesInString($string, $leadingSlash = TRUE)
    {
        $exp  = ',(' . preg_quote($this->sourceNamespace) . '[a-zA-Z0-9_]+),';
        $self = $this;

        $string = preg_replace_callback(
            $exp,
            function (array $matches) use ($self, $leadingSlash)
            {
                $newClassName = $self->convertClassName($matches[1]);
                return ($leadingSlash ? '\\' : '') . $newClassName;
            },
            $string
        );

        return $string;
    }



    public function leaveNode(Node $node)
    {
        if ($node instanceof NodeAbstract && $node->getDocComment() !== NULL)
        {
            $comment = $node->getDocComment();

            $string = $comment->getReformattedText();
            $string = $this->replaceClassNamesInString($string);

            $comment->setText($string);
        }

        if ($node instanceof Node\Stmt\Class_ || $node instanceof Node\Stmt\Interface_ || $node instanceof Node\Stmt\Function_)
        {
            $newClassName = $this->convertClassName($node->name);
            list($namespace, $className) = $this->splitClassAndNamespace($newClassName);

            if ($namespace)
            {
                $newNode    = new Node\Stmt\Namespace_(new Node\Name($namespace), [$node]);
                $node->name = $className;

                $this->namespaceNodes[] = $newNode;

                return $newNode;
            }
            else
            {
                return $node;
            }
        }
        else if ($node instanceof Node\Name\FullyQualified && $this->isClassMatchingSourceNamespace($node))
        {
            $newClassName = $this->convertClassName($node);
            /** @noinspection PhpUnusedLocalVariableInspection */
            list($_, $className) = $this->splitClassAndNamespace($newClassName);

            // TODO: Handle import with duplicate aliases!
            $this->imports[$className] = new Node\Name\FullyQualified($newClassName);

            return new Node\Name($className);
        }
        else if ($node instanceof Node\Scalar\String)
        {
            $node->value = $this->replaceClassNamesInString($node->value, FALSE);
        }

        return NULL;
    }
} 