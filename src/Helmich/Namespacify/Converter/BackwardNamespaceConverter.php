<?php
namespace Helmich\Namespacify\Converter;

use Helmich\Namespacify\Ast\NodeVisitor\NamespaceConverterVisitor;
use Helmich\Namespacify\File\FileInterface;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\Parser;
use PhpParser\PrettyPrinter\Standard;
use Symfony\Component\Console\Output\OutputInterface;

class BackwardNamespaceConverter implements NamespaceConverter
{
    private $sourceNamespace;
    private $targetNamespace;
    private $backup;
    /** @var \PhpParser\Parser */
    private $parser;

    public function __construct(Parser $parser)
    {
        $this->parser = $parser;
    }

    public function setOptions($sourceNamespace, $targetNamespace, $backup = FALSE)
    {
        $this->sourceNamespace = $sourceNamespace;
        $this->targetNamespace = $targetNamespace;
        $this->backup          = $backup;
    }

    public function convertFile(FileInterface $file, OutputInterface $out)
    {
        $content = $file->getContent();
        $ast = $this->parser->parse($content);

        $traverser = new NodeTraverser();
        $traverser->addVisitor(new NameResolver());
        $traverser->addVisitor(new NamespaceConverterVisitor());

        $ast = $traverser->traverse($ast);

        $printer = new Standard();
        $printedCode = '<?php' . PHP_EOL . PHP_EOL . $printer->prettyPrint($ast);

        file_put_contents($file->getFilename(), $printedCode);
    }

    private function convertClassName($oldClassName)
    {
        $class = str_replace($this->sourceNamespace, $this->targetNamespace, $oldClassName);
        $class = str_replace('_', '\\', $class);

        return $class;
    }

    private function getNamespace($namespacedClassName)
    {
        $components = explode('\\', $namespacedClassName);
        array_pop($components);
        return implode('\\', $components);
    }

    private function printFile(array $tokens)
    {
        $content = '';
        foreach ($tokens as $token)
        {
            if (is_array($token))
            {
                $content .= $token[1];
            }
            else
            {
                $content .= $token;
            }
        }
        return $content;
    }

} 