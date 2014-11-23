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

use Helmich\Namespacify\Converter\NamespaceConverter;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Helper class that handles alias map generation.
 *
 * @author     Martin Helmich <kontakt@martin-helmich.de>
 * @license    The MIT License
 * @package    Helmich\Namespacify
 * @subpackage Mapping
 */
class ClassMappingConcern
{



    /** @var NamespaceConverter */
    private $namespaceConverter;


    /** @var string */
    private $targetFile;


    /** @var ClassMap */
    private $classMap;



    /**
     * Creates a new instance of this concern.
     *
     * @param NamespaceConverter $namespaceConverter The namespace converter.
     * @param string             $targetFile         The target file name.
     */
    public function __construct(NamespaceConverter $namespaceConverter, $targetFile)
    {
        $this->classMap           = new ClassMap();
        $this->namespaceConverter = $namespaceConverter;
        $this->targetFile         = $targetFile;
    }



    /**
     * Registers required listeners.
     *
     * @return void
     */
    public function register()
    {
        $this->namespaceConverter->addClassRenameListener($this->classMap);
    }



    /**
     * Writes the actual alias map file. Should be called after all files have been converted.
     *
     * @param OutputInterface $output The output interface.
     * @return void
     */
    public function writeClassMap(OutputInterface $output)
    {
        $output->writeln('Writing alias definitions to <comment>' . $this->targetFile . '</comment>.');
        $content = "<?php\n";

        foreach ($this->classMap->getClassNames() as $old => $new)
        {
            $content .= 'class_alias(' . var_export($new, TRUE) . ', ' . var_export($old, TRUE) . ');' . "\n";
        }

        file_put_contents($this->targetFile, $content);
    }

}