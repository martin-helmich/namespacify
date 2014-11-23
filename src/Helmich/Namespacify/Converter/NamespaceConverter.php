<?php
namespace Helmich\Namespacify\Converter;


use Helmich\Namespacify\ClassRenamingListener;
use Helmich\Namespacify\File\FileInterface;
use Symfony\Component\Console\Output\OutputInterface;

interface NamespaceConverter
{



    public function setOptions($sourceNamespace, $targetNamespace, $backup = FALSE, $reverse = FALSE);



    public function convertFile(FileInterface $file, OutputInterface $out);



    public function addClassRenameListener(ClassRenamingListener $listener);


}