<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Common\Filesystem\Exception;

class FileNotExistsException extends \Exception
{
    public function __construct($key, $code = 0)
    {
        $message = sprintf(sprintf('The file with key "%s" is not exists.', $key));
        parent::__construct($message, $code);
    }
}