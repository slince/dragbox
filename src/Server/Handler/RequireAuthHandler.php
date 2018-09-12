<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Server\Handler;

use DragBox\Common\Protocol\SpikeInterface;
use DragBox\Server\Event\Events;
use DragBox\Server\ServerClientInterface;
use Slince\EventDispatcher\Event;

class RequireAuthHandler extends MessageActionHandler
{
    /**
     * @var ServerClientInterface
     */
    protected $client;

    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        $clientId = $message->getHeader('client-id');
        $client = $this->server->getClientById($clientId);
        if (!$client){
            $event = new Event(Events::UNAUTHORIZED_CLIENT, $this, [
                'clientId' => $clientId,
                'connection' => $this->connection,
            ]);
            $this->getEventDispatcher()->dispatch($event);
            $this->connection->close();
        } else {
            $this->client = $client;
            $this->client->setActiveAt(new \DateTime()); //Update last active time.
        }
    }

    /**
     * Gets the current client.
     *
     * @return ServerClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }
}