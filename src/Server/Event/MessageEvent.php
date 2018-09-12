<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Server\Event;

use DragBox\Common\Protocol\SpikeInterface;
use React\Socket\ConnectionInterface;
use Slince\EventDispatcher\Event;

class MessageEvent extends Event
{
    /**
     * @var SpikeInterface
     */
    protected $message;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    public function __construct($name, SpikeInterface $message, ConnectionInterface $connection)
    {
        $this->message = $message;
        $this->connection = $connection;
        parent::__construct($name);
    }

    /**
     * @return SpikeInterface
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }
}