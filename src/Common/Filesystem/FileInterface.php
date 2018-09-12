<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Common\Filesystem;

interface FileInterface
{
    const STATE_MISSING = 'missing';

    const STATE_FRESH = 'fresh';

    const STATE_OUTDATE = 'outdate';

    const STATE_NO_CHANGE = 'no_change';

    /**
     * Gets the hash of the file
     *
     * @return string
     */
    public function getKey();

    /**
     * @param string $key
     */
    public function setKey($key);

    /**
     * Gets the hash of the file
     *
     * @return string
     */
    public function getHash();

    /**
     * @param string $hash
     */
    public function setHash($hash);

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt();

    /**
     * @return \DateTimeInterface
     */
    public function getUpdatedAt();

    /**
     * @return \DateTimeInterface
     */
    public function getAccessAt();

    /**
     * @return array
     */
    public function getParameters();
}