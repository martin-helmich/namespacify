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
        return token_get_all($this->getContent());
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return file_get_contents($this->filename);
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }
}