<?php
namespace Helmich\Namespacify\Converter;


use Helmich\Namespacify\File\FileInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface NamespaceConverter
{

    public function setOptions($sourceNamespace, $targetNamespace, $backup = FALSE);

    public function convertFile(FileInterface $file, OutputInterface $out);
} 