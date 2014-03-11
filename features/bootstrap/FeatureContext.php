<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

/**
 * Features context.
 */
class FeatureContext extends BehatContext
{
    private $filename = NULL;

    /**
     * Initializes context.
     * Every scenario gets its own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
        // Initialize your context here
    }

    /**
     * @Given /^(?:I |)have a file in the directory "([^"]*)" with the following content:$/
     */
    public function doCreateFile($directory, PyStringNode $content)
    {
        shell_exec('rm -rf build');

        mkdir($directory, 0777, TRUE);
        $this->filename = $directory . '/' . uniqid() . '.php';

        $file = fopen($this->filename, 'w');
        fwrite($file, $content);
        fclose($file);
    }

    /**
     * @When /^(?:I |)run "([^"]*)"$/
     */
    public function doRun($command)
    {
        shell_exec($command);
    }

    /**
     * @Then /^the file should have the following content:$/
     */
    public function assertFileHasContent(PyStringNode $content)
    {
        $actualContent = trim(file_get_contents($this->filename));
        $content       = trim($content);

        if ($content !== $actualContent)
        {
            $tempFilename = $this->filename . '.should';
            $tempFile = fopen($tempFilename, 'w');
            fwrite($tempFile, $content);
            fclose($tempFile);

            $cmd = '/usr/bin/diff -u ' . $tempFilename . ' ' . $this->filename;
            $content = shell_exec($cmd);

            throw new Exception('File contents did not match!' . PHP_EOL . $content);
        }
    }

}
