<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Server\Filesystem\Index;

use DragBox\Common\Filesystem\FileInterface;

interface IndexerInterface
{
    /**
     * Index the file
     * @param FileInterface $file
     */
    public function index(FileInterface $file);

    /**
     * Index files
     *
     * @param FileInterface[] $files
     *
     * index all files
     */
    public function indexAll(array $files);

    /**
     * Finds the file with the key
     *
     * @param string $key
     * @return null|FileInterface
     */
    public function find($key);

    /**
     * Finds files
     *
     * @param array $keys
     * @return FileInterface[]
     */
    public function findAll($keys);
}