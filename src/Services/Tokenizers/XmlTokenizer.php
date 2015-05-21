<?php
namespace Janitor\Services\Tokenizers;

use SimpleXMLElement;

class XmlTokenizer extends JsonTokenizer
{
    /**
     * @type array
     */
    protected $ignored = ['@attributes'];

    /**
     * Tokenize a file.
     *
     * @param string $file
     *
     * @return string[]
     */
    public function tokenize($file)
    {
        $file = new SimpleXMLElement($file);
        $file = json_encode($file);

        return parent::tokenize($file);
    }
}
