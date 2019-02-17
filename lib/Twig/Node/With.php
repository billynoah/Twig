<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Twig\Node\Node;
use Twig\Error\RuntimeError;
use Twig\Compiler;

/**
 * Represents a nested "with" scope.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class Twig_Node_With extends Node
{
    public function __construct(Node $body, Node $variables = null, $only = false, $lineno, $tag = null)
    {
        $nodes = ['body' => $body];
        if (null !== $variables) {
            $nodes['variables'] = $variables;
        }

        parent::__construct($nodes, ['only' => (bool) $only], $lineno, $tag);
    }

    public function compile(Compiler $compiler)
    {
        $compiler->addDebugInfo($this);

        if ($this->hasNode('variables')) {
            $varsName = $compiler->getVarName();
            $compiler
                ->write(sprintf('$%s = ', $varsName))
                ->subcompile($this->getNode('variables'))
                ->raw(";\n")
                ->write(sprintf("if (!is_array(\$%s)) {\n", $varsName))
                ->indent()
                ->write("throw new RuntimeError('Variables passed to the \"with\" tag must be a hash.');\n")
                ->outdent()
                ->write("}\n")
            ;

            if ($this->getAttribute('only')) {
                $compiler->write("\$context = ['_parent' => \$context];\n");
            } else {
                $compiler->write("\$context['_parent'] = \$context;\n");
            }

            $compiler->write(sprintf("\$context = array_merge(\$context, \$%s);\n", $varsName));
        } else {
            $compiler->write("\$context['_parent'] = \$context;\n");
        }

        $compiler
            ->subcompile($this->getNode('body'))
            ->write("\$context = \$context['_parent'];\n")
        ;
    }
}

class_alias('Twig_Node_With', 'Twig\Node\WithNode', false);
