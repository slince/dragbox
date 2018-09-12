<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Server;

use React\Socket\ConnectionInterface;

class Client implements ServerClientInterface
{
    /**
     * @var string
     */
    protected $id;

    /**
     * Client information.
     *
     * @var array
     */
    protected $info;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var \DateTime
     */
    protected $activeAt;

    public function __construct($info, ConnectionInterface $connection)
    {
        $this->info = $info;
        $this->connection = $connection;
        $this->activeAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id ?: ($this->id = spl_object_hash($this));
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Gets the client information.
     *
     * @return array
     */
    public function getInfo()
    {
        return array_replace($this->info, [
            'id' => $this->getId(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        $this->connection->close();
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveAt()
    {
        return $this->activeAt;
    }
}