<?php
namespace Helmich\Namespacify\Converter;

use Helmich\Namespacify\File\File;
use Helmich\Namespacify\File\FileInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ForwardNamespaceConverter implements NamespaceConverter
{
    private $sourceNamespace;
    private $targetNamespace;
    private $backup;

    public function setOptions($sourceNamespace, $targetNamespace, $backup = FALSE)
    {
        $this->sourceNamespace = $sourceNamespace;
        $this->targetNamespace = $targetNamespace;
        $this->backup          = $backup;
    }

    public function convertFile(FileInterface $file, OutputInterface $out)
    {
        $self      = $this;
        $tokens    = $file->getTokens();
        $namespace = NULL;

        $count = count($tokens);
        for ($i = 0; $i < $count; ++$i)
        {
            $token = $tokens[$i];
            if (!is_array($token))
            {
                continue;
            }

            if ($token[0] === T_NAMESPACE)
            {
                echo "File is already namespaced." . PHP_EOL;
                echo "Done." . PHP_EOL;
                return;
            }

            if ($token[0] === T_CLASS || $token[0] === T_INTERFACE)
            {
                for (++$i; $tokens[$i][0] === T_WHITESPACE; ++$i)
                {
                    ;
                }

                $class = $oldClass = $tokens[$i][1];

                if (strpos($class, $this->sourceNamespace) === 0)
                {
                    $class = str_replace($this->sourceNamespace, $this->targetNamespace, $class);
                    $class = str_replace('_', '\\', $class);

                    $components = explode('\\', $class);
                    $realClass  = array_pop($components);
                    $namespace  = implode('\\', $components);

                    $tokens[$i][1] = $realClass;
                }
            }

            if ($token[0] === T_CONSTANT_ENCAPSED_STRING)
            {
                if (strpos($token[1], $this->sourceNamespace) !== FALSE)
                {
                    $tokens[$i][1] = preg_replace_callback(
                        ',(' . $this->sourceNamespace . '[a-zA-Z_]+),',
                        function ($matches) use ($self)
                        {
                            return addslashes($self->convertClassName($matches[1]));
                        },
                        $tokens[$i][1]
                    );
                }
            }

            if ($token[0] === T_STRING && strpos($token[1], $this->sourceNamespace) === 0)
            {
                $class = $this->convertClassName($token[1]);

                if ($namespace !== NULL && $namespace === $this->getNamespace($class))
                {
                    $tokens[$i][1] = str_replace($namespace . '\\', '', $class);
                }
                else
                {
                    $tokens[$i][1] = '\\' . $class;
                }
            }

            if ($token[0] === T_DOC_COMMENT)
            {
                $tokens[$i][1] = preg_replace_callback(
                    ',(' . $this->sourceNamespace . '[a-zA-Z_]+),',
                    function ($matches) use ($self)
                    {
                        return '\\' . $self->convertClassName($matches[1]);
                    },
                    $tokens[$i][1]
                );
            }
        }

        if ($namespace !== NULL)
        {
            $namespaceTokens = [
                [T_NAMESPACE, 'namespace'],
                [T_WHITESPACE, ' '],
                [T_STRING, $namespace],
                ';',
                [T_WHITESPACE, "\n\n"]
            ];

            for ($i = 0; $i < $count && (is_string($tokens[$i]) || $tokens[$i][0] !== T_OPEN_TAG); ++$i)
            {
                ;
            }

            $firstPart  = array_slice($tokens, 0, $i + 1);
            $secondPart = array_slice($tokens, $i + 1);

            $tokens = array_merge($firstPart, $namespaceTokens, $secondPart);
        }

        $content = $this->printFile($tokens);

        if ($this->backup)
        {
            copy($file->getFilename(), $file->getFilename() . '.bak');
        }

        file_put_contents($file->getFilename(), $content);
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