<?php
namespace Helmich\Namespacify\File;


class File implements FileInterface
{
    /** @var string */
    private $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    public function getTokens()
    {
        $content = file_get_contents($this->filename);
        return token_get_all($content);
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }
}