<?php
namespace Helmich\Namespacify;

/*
 * This file is part of namespacify.
 * https://github.com/martin-helmich/namespacify
 *
 * (C) 2014 Martin Helmich <kontakt@martin-helmich.de>
 *
 * For license information, view the LICENSE.md file.
 */

/**
 * Interface definition for a listener that listens for classes being renamed.
 *
 * @author  Martin Helmich <kontakt@martin-helmich.de>
 * @license The MIT License
 * @package Helmich\Namespacify
 */
interface ClassRenamingListener
{



    public function onClassRename($oldClassName, $newClassName);

}