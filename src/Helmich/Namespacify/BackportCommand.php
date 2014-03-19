<?php
namespace Helmich\Namespacify;


use Helmich\Namespacify\Converter\NamespaceConverter;
use Helmich\Namespacify\File\FileLocatorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BackportCommand extends Command
{
    /** @var FileLocatorInterface */
    private $fileLocator = NULL;
    /** @var NamespaceConverter */
    private $namespaceConverter = NULL;

    public function setFileLocator(FileLocatorInterface $loader)
    {
        $this->fileLocator = $loader;
    }

    public function setNamespaceConverter(NamespaceConverter $namespaceConverter)
    {
        $this->namespaceConverter = $namespaceConverter;
    }

    protected function configure()
    {
        $this
            ->setName('backport')
            ->setDescription('Backport all PHP classes in target directory from namespaces to old-school pseodo namespaces.')
            ->addArgument('source-namespace', InputArgument::REQUIRED, 'Source (real) namespace.')
            ->addArgument('target-namespace', InputArgument::REQUIRED, 'Target (pseudo) namespace.')
            ->addArgument('directory', InputArgument::OPTIONAL, 'Directory to parse.', '.')
            ->addOption('backup', 'b', InputOption::VALUE_NONE, 'Backup files before writing back.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('directory');
        $files     = $this->fileLocator->getPhpFiles($directory);

        $this->namespaceConverter->setOptions(
            $input->getArgument('source-namespace'),
            $input->getArgument('target-namespace'),
            $input->getOption('backup') ? TRUE : FALSE
        );

        $output->writeln('<comment>' . count($files) . '</comment> PHP files found in directory <comment>' . $directory . '</comment>');

        foreach ($files as $file)
        {
            $this->namespaceConverter->convertFile($file, $output);
        }

        $output->writeln(sprintf('<info>Converted <comment>%d</comment> files.', count($files)));
    }
} 