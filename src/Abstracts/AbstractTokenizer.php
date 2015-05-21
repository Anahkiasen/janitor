<?php
namespace Janitor\Abstracts;

class AbstractTokenizer
{
    /**
     * Tokens to ignore.
     *
     * @type array
     */
    protected $ignored = [];

    /**
     * Transform a variable into a PHP file.
     *
     * @param array|string $variable
     *
     * @return string
     */
    protected function toPhpFile($variable)
    {
        return '<?php'.PHP_EOL.var_export($variable, true).';';
    }

    /**
     * @param array $tokens
     *
     * @return array
     */
    protected function filterTokens(array $tokens)
    {
        return array_filter($tokens, function ($token) {
            return !in_array($token, $this->ignored, true);
        });
    }
}
