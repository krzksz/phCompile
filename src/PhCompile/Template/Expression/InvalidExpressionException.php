<?php
/*
 * This file is part of the ngPhCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhCompile\Template\Expression;

/**
 * Exception thrown when exception string that application have to compile has
 * major error inside it e.g. function call.
 */
class InvalidExpressionException extends \Exception
{

}