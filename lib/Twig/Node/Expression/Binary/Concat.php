<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 * (c) Armin Ronacher
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Twig\Node\Expression\Binary\AbstractBinary;
use Twig\Compiler;
class Twig_Node_Expression_Binary_Concat extends AbstractBinary
{
    public function operator(Compiler $compiler)
    {
        return $compiler->raw('.');
    }
}

class_alias('Twig_Node_Expression_Binary_Concat', 'Twig\Node\Expression\Binary\ConcatBinary', false);
