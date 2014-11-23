<?php
namespace Helmich\Namespacify;


interface ClassRenamingListener
{



    public function onClassRename($oldClassName, $newClassName);

}