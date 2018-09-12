<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Client\Event;

use React\Socket\ConnectionInterface;
use Slince\EventDispatcher\Event;
use DragBox\Common\Protocol\SpikeInterface;
use DragBox\Client\Handler\ActionHandlerInterface;

class FilterActionHandlerEvent extends Event
{
    /**
     * @var SpikeInterface
     */
    protected $message;

    /**
     * @var ConnectionInterface
     */
    protected $connection;

    /**
     * @var ActionHandlerInterface
     */
    protected $actionHandler;

    public function __construct($subject, SpikeInterface $message, ConnectionInterface $connection)
    {
        $this->message = $message;
        $this->connection = $connection;
        parent::__construct(Events::CLIENT_ACTION, $subject);
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

    /**
     * @param ActionHandlerInterface $actionHandler
     */
    public function setActionHandler($actionHandler)
    {
        $this->actionHandler = $actionHandler;
    }

    /**
     * @return ActionHandlerInterface
     */
    public function getActionHandler()
    {
        return $this->actionHandler;
    }
}