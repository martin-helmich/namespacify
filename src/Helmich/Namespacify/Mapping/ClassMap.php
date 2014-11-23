<?php
namespace Helmich\Namespacify\Mapping;


use Helmich\Namespacify\ClassRenamingListener;


class ClassMap implements ClassRenamingListener
{



    private $classMap = [];



    public function onClassRename($oldClassName, $newClassName)
    {
        $this->classMap[$oldClassName] = $newClassName;
    }



    public function getClassNames()
    {
        return $this->classMap;
    }
}