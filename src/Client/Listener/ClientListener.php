<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Client\Listener;

use React\Socket\ConnectionInterface;
use Slince\EventDispatcher\SubscriberInterface;
use DragBox\Client\Client;
use DragBox\Common\Exception\InvalidArgumentException;
use DragBox\Common\Protocol\SpikeInterface;
use DragBox\Client\Event\Events;
use DragBox\Client\Event\FilterActionHandlerEvent;
use DragBox\Client\Handler;

class ClientListener implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::CLIENT_ACTION => 'onClientAction',
        ];
    }

    /**
     * @param FilterActionHandlerEvent $event
     */
    public function onClientAction(FilterActionHandlerEvent $event)
    {
        $actionHandler = $this->createMessageHandler(
            $event->getSubject(),
            $event->getMessage(),
            $event->getConnection()
        );
        $event->setActionHandler($actionHandler);
    }

    /**
     * Creates the handler for the received message.
     *
     * @param Client              $client
     * @param SpikeInterface      $message
     * @param ConnectionInterface $connection
     *
     * @return Handler\ActionHandlerInterface
     * @codeCoverageIgnore
     */
    protected function createMessageHandler(Client $client, SpikeInterface $message, ConnectionInterface $connection)
    {
        switch ($message->getAction()) {
            case 'auth_response':
                $handler = new Handler\AuthResponseHandler($client, $connection);
                break;
            case 'register_tunnel_response':
                $handler = new Handler\RegisterTunnelResponseHandler($client, $connection);
                break;
            case 'request_proxy':
                $handler = new Handler\RequestProxyHandler($client, $connection);
                break;
            default:
                throw new InvalidArgumentException(sprintf('Cannot find handler for the message: "%s"',
                    $message->getAction()
                ));
        }

        return $handler;
    }
}