<?php
namespace Helmich\Namespacify\Converter;

use Helmich\Namespacify\Ast\NodeVisitor\BackwardNamespaceConverterVisitor;
use Helmich\Namespacify\Ast\NodeVisitor\ForwardNamespaceConverterVisitor;
use Helmich\Namespacify\Ast\Printer\StandardPrinter;
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



    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
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

        if ($this->reverse === FALSE)
        {
            $traverser->addVisitor(
                new ForwardNamespaceConverterVisitor($this->sourceNamespace, $this->targetNamespace)
            );
        }
        else
        {
            $traverser->addVisitor(
                new BackwardNamespaceConverterVisitor($this->sourceNamespace, $this->targetNamespace)
            );
        }

        $ast = $traverser->traverse($ast);

        $printer = new StandardPrinter();
        $printedCode = $printer->prettyPrintFile($ast);

        file_put_contents($file->getFilename(), $printedCode);
    }
}