<?php
namespace Helmich\Namespacify\File;


class FileLocator implements FileLocatorInterface
{

    /**
     * @param $directory
     * @return \Helmich\Namespacify\File\FileInterface[]
     */
    public function getPhpFiles($directory)
    {
        $directoryIterator = new \RecursiveDirectoryIterator($directory);
        $iterator          = new \RecursiveIteratorIterator($directoryIterator);
        $regexIterator     = new \RegexIterator($iterator, '/^.+\.php$/', \RecursiveRegexIterator::GET_MATCH);

        $results = array();
        foreach ($regexIterator as $value)
        {
            $results[] = new File($value[0]);
        }

        return $results;
    }
}