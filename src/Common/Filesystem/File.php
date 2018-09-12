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

class File implements FileInterface
{
    protected $key;

    protected $hash;

    protected $createdAt;

    protected $updatedAt;

    protected $accessAt;

    /**
     * @var array
     */
    protected $parameters;

    public function __construct($key, $hash, $createdAt, $updatedAt, $accessAt)
    {
        $this->key = $key;
        $this->hash = $hash;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->accessAt = $accessAt;
    }

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

    public function getParameters()
    {
        return $this->parameters;
    }
}