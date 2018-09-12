<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Client\Handler;

use DragBox\Client\Event\Events;
use DragBox\Common\Protocol\Spike;
use DragBox\Common\Protocol\SpikeInterface;
use Slince\EventDispatcher\Event;

class AuthResponseHandler extends MessageActionHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        if (200 !== $message->getHeader('code')) {
            $this->getEventDispatcher()->dispatch(new Event(Events::AUTH_ERROR, $this->client, [
                'message' => $message,
            ]));
            //Close client if auth error
            $this->client->close();
        } else {
            $this->getEventDispatcher()->dispatch(new Event(Events::AUTH_SUCCESS, $this->client, [
                'message' => $message,
            ]));
            $clientInfo = $message->getBody();
            $this->client->setId($clientInfo['id']);
            Spike::setGlobalHeader('client-id', $clientInfo['id']);
            $this->registerTunnels();
        }
    }

    /**
     * Reports the proxy hosts to the server.
     */
    protected function registerTunnels()
    {
        $tunnels = $this->client->getConfiguration()->getTunnels();
        $this->getEventDispatcher()->dispatch(new Event(Events::REGISTER_TUNNELS, $this, [
            'tunnels' => $tunnels,
        ]));
        foreach ($tunnels as $tunnel) {
            $this->connection->write(new Spike('register_tunnel', $tunnel));
        }
    }
}