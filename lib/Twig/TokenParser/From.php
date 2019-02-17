<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Twig\TokenParser\AbstractTokenParser;
use Twig\Node\ImportNode;
use Twig\Node\Expression\AssignNameExpression;
use Twig\Error\SyntaxError;
use Twig\Token;

/**
 * Imports macros.
 *
 *   {% from 'forms.html' import forms %}
 *
 * @final
 */
class Twig_TokenParser_From extends AbstractTokenParser
{
    public function parse(Token $token)
    {
        $macro = $this->parser->getExpressionParser()->parseExpression();
        $stream = $this->parser->getStream();
        $stream->expect('import');

        $targets = [];
        do {
            $name = $stream->expect(Token::NAME_TYPE)->getValue();

            $alias = $name;
            if ($stream->nextIf('as')) {
                $alias = $stream->expect(Token::NAME_TYPE)->getValue();
            }

            $targets[$name] = $alias;

            if (!$stream->nextIf(Token::PUNCTUATION_TYPE, ',')) {
                break;
            }
        } while (true);

        $stream->expect(Token::BLOCK_END_TYPE);

        $node = new ImportNode($macro, new AssignNameExpression($this->parser->getVarName(), $token->getLine()), $token->getLine(), $this->getTag());

        foreach ($targets as $name => $alias) {
            if ($this->parser->isReservedMacroName($name)) {
                throw new SyntaxError(sprintf('"%s" cannot be an imported macro as it is a reserved keyword.', $name), $token->getLine(), $stream->getSourceContext());
            }

            $this->parser->addImportedSymbol('function', $alias, 'get'.$name, $node->getNode('var'));
        }

        return $node;
    }

    public function getTag()
    {
        return 'from';
    }
}

class_alias('Twig_TokenParser_From', 'Twig\TokenParser\FromTokenParser', false);
