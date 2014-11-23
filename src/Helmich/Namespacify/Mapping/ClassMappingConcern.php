<?php
namespace Helmich\Namespacify\Mapping;


use Helmich\Namespacify\Converter\NamespaceConverter;
use Symfony\Component\Console\Output\OutputInterface;

class ClassMappingConcern
{



    /** @var NamespaceConverter */
    private $namespaceConverter;


    /** @var string */
    private $targetFile;


    /** @var ClassMap */
    private $classMap;



    public function __construct(NamespaceConverter $namespaceConverter, $targetFile)
    {
        $this->classMap           = new ClassMap();
        $this->namespaceConverter = $namespaceConverter;
        $this->targetFile         = $targetFile;
    }



    public function register()
    {
        $this->namespaceConverter->addClassRenameListener($this->classMap);
    }



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