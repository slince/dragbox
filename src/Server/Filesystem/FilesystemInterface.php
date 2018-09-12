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

interface FilesystemInterface
{
    /**
     * Gets the storage path
     *
     * @return string
     */
    public function getStoragePath();

    /**
     * Checks whether the file is fresh.
     *
     * @param string $key
     * @param string $hash
     * @return boolean
     */
    public function isFresh($key, $hash);

    /**
     * Create a stream pipe.
     *
     * @param string $key
     * @return resource
     */
    public function createStreamPipe($key);

    /**
     * Uploads the a file
     * @param string $key
     * @param string $content
     * @param boolean $overwrite
     */
    public function upload($key, $content, $overwrite);

    /**
     * Creates index
     */
    public function createIndex();
}