<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Server\Filesystem;

class Filesystem implements FilesystemInterface
{
    protected $path;

    protected $indexFile;

    public function __construct($path, $indexFile)
    {
        $this->path = $path;
        $this->indexFile = $indexFile;
    }

    public function getStoragePath()
    {
        return $this->path;
    }

    public function createIndex()
    {

    }
}