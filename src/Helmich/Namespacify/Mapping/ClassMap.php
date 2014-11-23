<?php
namespace Helmich\Namespacify\Mapping;

/*
 * This file is part of namespacify.
 * https://github.com/martin-helmich/namespacify
 *
 * (C) 2014 Martin Helmich <kontakt@martin-helmich.de>
 *
 * For license information, view the LICENSE.md file.
 */

use Helmich\Namespacify\ClassRenamingListener;

/**
 * Mapping of old class names to new class names.
 *
 * @author     Martin Helmich <kontakt@martin-helmich.de>
 * @license    The MIT License
 * @package    Helmich\Namespacify
 * @subpackage Mapping
 */
class ClassMap implements ClassRenamingListener
{


    /** @var array */
    private $classMap = [];



    /**
     * Adds a new class name mapping.
     *
     * @param string $oldClassName The old class name.
     * @param string $newClassName The new class name.
     * @return void
     */
    public function onClassRename($oldClassName, $newClassName)
    {
        $this->classMap[$oldClassName] = $newClassName;
    }



    /**
     * Gets all registered class name aliases.
     *
     * @return array All registered class name aliases.
     */
    public function getClassNames()
    {
        return $this->classMap;
    }
}