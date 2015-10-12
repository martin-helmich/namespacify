<?php

use Behat\Behat\Context\ClosuredContextInterface,
    Behat\Behat\Context\TranslatedContextInterface,
    Behat\Behat\Context\BehatContext,
    Behat\Behat\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\Gherkin\Node\PyStringNode,
    Behat\Gherkin\Node\TableNode;

/**
 * Features context.
 */
class FeatureContext implements Context
{
    private $filename = NULL;

    /**
     * @Given /^(?:I |)have a file in the directory "([^"]*)" with the following content:$/
     */
    public function doCreateFile($directory, PyStringNode $content)
    {
        shell_exec('rm -rf build');

        mkdir($directory, 0777, TRUE);
        $this->filename = $directory . '/' . uniqid() . '.php';

        $file = fopen($this->filename, 'w');
        fwrite($file, "<?php\n\n" . $content);
        fclose($file);
    }

    /**
     * @When /^(?:I |)run "([^"]*)"$/
     */
    public function doRun($command)
    {
        $output = shell_exec($command);
        echo($output);
    }

    /**
     * @Then /^the file should have the following content:$/
     */
    public function assertFileHasContent(PyStringNode $content)
    {
        $actualContent = $this->normalizeFileContent(file_get_contents($this->filename));
        $content       = $this->normalizeFileContent("<?php\n\n" . trim($content));

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

    /**
     * @Then /^the file "([^"]*)" should have the following content:$/
     */
    public function assertNamedFileHasContent($file, PyStringNode $content)
    {
        $actualContent = $this->normalizeFileContent(file_get_contents($file));
        $content       = $this->normalizeFileContent("<?php\n\n" . trim($content));

        if ($content !== $actualContent)
        {
            $tempFilename = $file . '.should';
            $tempFile = fopen($tempFilename, 'w');
            fwrite($tempFile, $content);
            fclose($tempFile);

            $cmd = '/usr/bin/diff -u ' . $tempFilename . ' ' . $file;
            $content = shell_exec($cmd);

            throw new Exception('File contents did not match!' . PHP_EOL . $content);
        }
    }

    private function normalizeFileContent($content)
    {
        $lines = explode("\n", trim($content));
        $lines = array_map('trim', $lines);
        $lines = array_filter($lines, function($line) { return strlen($line) > 0; });

        $content = implode("\n", $lines);
        $content = trim($content);

        return $content;
    }

}
