<?php
namespace Janitor\Interfaces;

interface TokenizerInterface
{
    /**
     * Tokenize a file.
     *
     * @param string $file
     *
     * @return string[]
     */
    public function tokenize($file);
}
