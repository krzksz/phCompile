<?php
/*
 * This file is part of the ngPhCompile package.
 *
 * (c) Mateusz Krzeszowiak <mateusz.krzeszowiak@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhRender\Template;

/**
 * Exception thrown when path to template file provided by user does not
 * point to any readable file.
 */
class TemplateNotFoundException extends \Exception
{

}