<?php

namespace Helmich\Namespacify\File;


interface FileLocatorInterface
{
    /**
     * @param $directory
     * @return \Helmich\Namespacify\File\FileInterface[]
     */
    public function getPhpFiles($directory);
}