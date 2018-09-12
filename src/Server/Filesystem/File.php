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

class File implements FileInterface
{
    protected $key;

    protected $hash;

    protected $createdAt;

    protected $updatedAt;

    protected $accessAt;

    /**
     * @return \DateTimeInterface
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param \DateTimeInterface $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param \DateTimeInterface $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTimeInterface $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTimeInterface $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getAccessAt()
    {
        return $this->accessAt;
    }

    /**
     * @param \DateTimeInterface $accessAt
     */
    public function setAccessAt($accessAt)
    {
        $this->accessAt = $accessAt;
    }
}