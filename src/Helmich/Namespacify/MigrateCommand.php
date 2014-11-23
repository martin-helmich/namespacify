<?php
namespace Helmich\Namespacify;

/*
 * This file is part of namespacify.
 * https://github.com/martin-helmich/namespacify
 *
 * (C) 2014 Martin Helmich <kontakt@martin-helmich.de>
 *
 * For license information, view the LICENSE.md file.
 */

use Helmich\Namespacify\Converter\NamespaceConverter;
use Helmich\Namespacify\File\FileLocatorInterface;
use Helmich\Namespacify\Mapping\ClassMappingConcern;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for triggering the namespace conversion process.
 *
 * @author  Martin Helmich <kontakt@martin-helmich.de>
 * @license The MIT License
 * @package Helmich\Namespacify
 */
class MigrateCommand extends Command
{



    /** @var FileLocatorInterface */
    private $fileLocator = NULL;


    /** @var NamespaceConverter */
    private $namespaceConverter = NULL;



    /**
     * Sets the file locator object.
     *
     * @internal
     *
     * @param \Helmich\Namespacify\File\FileLocatorInterface $locator The file locator.
     * @return void
     */
    public function setFileLocator(FileLocatorInterface $locator)
    {
        $this->fileLocator = $locator;
    }



    /**
     * Sets the namespace converter object.
     *
     * @internal
     *
     * @param \Helmich\Namespacify\Converter\NamespaceConverter $namespaceConverter The namespace converter.
     * @return void
     */
    public function setNamespaceConverter(NamespaceConverter $namespaceConverter)
    {
        $this->namespaceConverter = $namespaceConverter;
    }



    /**
     * Configures the command (name, description and arguments).
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('migrate')
            ->setDescription('Migrate all PHP classes in target directory to namespaces.')
            ->addArgument('source-namespace', InputArgument::REQUIRED, 'Source (pseudo) namespace.')
            ->addArgument('target-namespace', InputArgument::REQUIRED, 'Target (real) namespace.')
            ->addArgument('directory', InputArgument::OPTIONAL, 'Directory to parse.', '.')
            ->addOption('alias-file', 'a', InputArgument::OPTIONAL, 'File to write alias definitions to.')
            ->addOption('backup', 'b', InputOption::VALUE_NONE, 'Backup files before writing back.')
            ->addOption('reverse', 'r', InputOption::VALUE_NONE, 'Convert namespaced to pseudo-namespaced instead.');
    }



    /**
     * Executes the command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input  Input data.
     * @param \Symfony\Component\Console\Output\OutputInterface $output Output stream.
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directory = $input->getArgument('directory');
        $files     = $this->fileLocator->getPhpFiles($directory);

        $this->namespaceConverter->setOptions(
            $input->getArgument('source-namespace'),
            $input->getArgument('target-namespace'),
            $input->getOption('backup') ? TRUE : FALSE,
            $input->getOption('reverse') ? TRUE : FALSE
        );

        $finishers = [];

        if ($input->getOption('alias-file'))
        {
            $classMappingConcern = new ClassMappingConcern($this->namespaceConverter, $input->getOption('alias-file'));
            $classMappingConcern->register();

            $finishers[] = function () use ($classMappingConcern, $output)
            {
                $classMappingConcern->writeClassMap($output);
            };
        }

        $output->writeln(
            sprintf(
                '<comment>%d</comment> PHP files found in directory <comment>%s</comment>.',
                count($files),
                $directory
            )
        );

        foreach ($files as $file)
        {
            $output->write(sprintf('Converting file <comment>%s</comment>... ', $file->getFilename()));
            $this->namespaceConverter->convertFile($file, $output);
            $output->writeln('<info>Done.</info>');
        }

        $output->writeln(sprintf('<info>Converted <comment>%d</comment> files.', count($files)));

        foreach ($finishers as $finisher)
        {
            $finisher();
        }
    }
} 