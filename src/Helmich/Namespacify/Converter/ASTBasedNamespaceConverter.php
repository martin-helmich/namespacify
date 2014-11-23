<?php
namespace Helmich\Namespacify\Converter;

use Helmich\Namespacify\Ast\NodeVisitor\AbstractNamespaceConverterVisitor;
use Helmich\Namespacify\Ast\NodeVisitor\BackwardNamespaceConverterVisitor;
use Helmich\Namespacify\Ast\NodeVisitor\ForwardNamespaceConverterVisitor;
use Helmich\Namespacify\Ast\Printer\StandardPrinter;
use Helmich\Namespacify\ClassRenamingListener;
use Helmich\Namespacify\ConversionListener;
use Helmich\Namespacify\File\FileInterface;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use Symfony\Component\Console\Output\OutputInterface;

class ASTBasedNamespaceConverter implements NamespaceConverter
{



    /** @var string */
    private $sourceNamespace;


    /** @var string */
    private $targetNamespace;


    /** @var bool */
    private $backup;


    /** @var bool */
    private $reverse;


    /** @var \PhpParser\Parser */
    private $parser;


    /** @var \SplObjectStorage<ClassRenamingListener> */
    private $renameListeners = [];



    public function __construct(Parser $parser)
    {
        $this->parser          = $parser;
        $this->renameListeners = new \SplObjectStorage();
    }



    public function setOptions($sourceNamespace, $targetNamespace, $backup = FALSE, $reverse = FALSE)
    {
        $this->sourceNamespace = $sourceNamespace;
        $this->targetNamespace = $targetNamespace;
        $this->backup          = $backup;
        $this->reverse         = $reverse;
    }



    public function convertFile(FileInterface $file, OutputInterface $out)
    {
        $content = $file->getContent();
        $ast     = $this->parser->parse($content);

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor($this->buildConverterVisitor());

        $ast = $traverser->traverse($ast);

        $printer     = new StandardPrinter();
        $printedCode = $printer->prettyPrintFile($ast);

        file_put_contents($file->getFilename(), $printedCode);
    }



    public function addClassRenameListener(ClassRenamingListener $listener)
    {
        $this->renameListeners->attach($listener);
    }



    /**
     * @return AbstractNamespaceConverterVisitor
     */
    private function buildConverterVisitor()
    {
        if ($this->reverse === FALSE)
        {
            $visitor = new ForwardNamespaceConverterVisitor($this->sourceNamespace, $this->targetNamespace);
        }
        else
        {
            $visitor = new BackwardNamespaceConverterVisitor($this->sourceNamespace, $this->targetNamespace);
        }

        foreach ($this->renameListeners as $renameListener)
        {
            $visitor->addRenameObserver($renameListener);
        }
        return $visitor;
    }


}