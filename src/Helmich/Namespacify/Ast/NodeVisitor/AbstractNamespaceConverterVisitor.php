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

use Helmich\Namespacify\ClassRenamingListener;
use PhpParser\NodeVisitorAbstract;

/**
 * Abstract base class for class-renaming visitors.
 *
 * @author     Martin Helmich <kontakt@martin-helmich.de>
 * @license    The MIT License
 * @package    Helmich\Namespacify
 * @subpackage Ast\NodeVisitor
 */
abstract class AbstractNamespaceConverterVisitor extends NodeVisitorAbstract
{



    /**
     * @var ClassRenamingListener[]
     */
    private $renameObservers = [];



    /**
     * Adds a new listener that listens for renamed classes.
     *
     * @param ClassRenamingListener $listener The listener class.
     * @return void
     */
    public function addRenameObserver(ClassRenamingListener $listener)
    {
        $this->renameObservers[spl_object_hash($listener)] = $listener;
    }



    /**
     * Notifies class rename listeners about a renamed class.
     *
     * @param string $oldClassName The old class name.
     * @param string $newClassName The new class name.
     * @return void
     */
    protected function notifyRenameObservers($oldClassName, $newClassName)
    {
        foreach ($this->renameObservers as $observer)
        {
            $observer->onClassRename($oldClassName, $newClassName);
        }
    }


}