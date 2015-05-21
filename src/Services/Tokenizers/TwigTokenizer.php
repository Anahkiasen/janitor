<?php
namespace Janitor\Services\Tokenizers;

use Janitor\Interfaces\TokenizerInterface;
use Twig_Environment;
use Twig_Error_Syntax;
use Twig_Token;

class TwigTokenizer implements TokenizerInterface
{
    /**
     * Tokenize a file.
     *
     * @param string $file
     *
     * @return string[]
     */
    public function tokenize($file)
    {
        $twig = new Twig_Environment();

        $tokens = [];
        $stream = $twig->tokenize(trim($file));
        while ($token = $stream->getCurrent()) {
            if ($token->getType() === Twig_Token::STRING_TYPE) {
                $tokens[] = $token->getValue();
            }

            try {
                $stream->next();
            } catch (Twig_Error_Syntax $exception) {
                break;
            }
        }

        return $tokens;
    }
}
