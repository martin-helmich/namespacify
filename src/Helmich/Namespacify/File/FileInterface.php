<?php

namespace Helmich\Namespacify\File;


interface FileInterface
{
    /**
     * @return string
     */
    public function getFilename();

    /**
     * @return array
     */
    public function getTokens();
} 