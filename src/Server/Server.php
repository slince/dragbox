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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use DragBox\Client\ClientInterface;
use function Dragbox\Common\jsonBuffer;
use DragBox\Common\Protocol\Spike;
use DragBox\Server\Event\Events;
use DragBox\Server\Event\FilterActionHandlerEvent;
use DragBox\Server\Filesystem\FilesystemInterface;
use DragBox\Server\Filesystem\Index\SqlLiteIndexer;
use DragBox\Version;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Server as Socket;
use Slince\EventDispatcher\Dispatcher;
use Slince\EventDispatcher\Event;
use Symfony\Component\Console\Application;

class Server extends Application implements ServerInterface
{
    /**
     * @var string
     */
    const NAME = 'DragBox';

    /**
     * @var string
     */
    const VERSION = Version::VERSION;

    /**
     * @var ClientInterface[]|Collection
     */
    protected $clients;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var LoopInterface
     */
    protected $eventLoop;

    /**
     * @var Dispatcher
     */
    protected $eventDispatcher;

    /**
     * @var Socket
     */
    protected $socket;

    /**
     * @var FilesystemInterface
     */
    protected $filesystem;

    public function __construct(Configuration $configuration, LoopInterface $eventLoop = null)
    {
        $this->configuration = $configuration;
        $this->eventLoop = $eventLoop ?: Factory::create();
        $this->eventDispatcher = new Dispatcher();
        $this->clients = new ArrayCollection();

        $this->filesystem = new Filesystem\Filesystem(
            $this->configuration->getStoragePath(),
            new SqlLiteIndexer($this->configuration->getSqlLiteFile())
        );
        parent::__construct(static::NAME, static::VERSION);
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $this->socket = new Socket($this->configuration->getAddress(), $this->eventLoop);
        $this->eventDispatcher->dispatch(Events::SERVER_RUN);
        $this->socket->on('connection', function(ConnectionInterface $connection){
            jsonBuffer($connection, function($messages) use ($connection){
                foreach ($messages as $messageData) {
                    if (!$messageData) {
                        continue;
                    }
                    $message = Spike::fromArray($messageData);

                    //Fires filter action handler event
                    $event = new FilterActionHandlerEvent($this, $message, $connection);
                    $this->eventDispatcher->dispatch($event);
                    if ($actionHandler = $event->getActionHandler()) {
                        try {
                            $actionHandler->handle($message);
                        } catch (\Exception $exception) {
                            //Ignore bad message
                        }
                    }
                }
            }, function($exception) use ($connection){
                $this->eventDispatcher->dispatch(new Event(Events::CONNECTION_ERROR, $this, [
                    'connection' => $connection,
                    'exception' => $exception,
                ]));
            });
            //Distinct
            $connection->on('close', function() use($connection){
                //If client has been registered and then close it.
                $client = $this->clients->filter(function(ClientInterface $client) use ($connection){
                    return $client->getControlConnection() === $connection;
                })->first();
                if ($client) {
                    $this->stopClient($client);
                } else {
                    $connection->end();
                }
                $this->eventDispatcher->dispatch(new Event(Events::CLIENT_CLOSE, $this, [
                    'connection' => $connection,
                ]));
            });
        });
        $this->eventLoop->run();
    }

    /**
     * {@inheritdoc}
     */
    public function stopClient(ClientInterface $client)
    {
        $this->eventDispatcher->dispatch(new Event(Events::CLIENT_CLOSE, $this, [
            'client' => $client,
        ]));
        $client->close(); //Close the client
        $this->clients->removeElement($client); //Removes the client
    }

    /**
     * {@inheritdoc}
     */
    public function getClients()
    {
        return $this->clients;
    }

    /**
     * {@inheritdoc}
     */
    public function getClientById($id)
    {
        return $this->clients->filter(function(Client $client) use ($id){
            return $client->getId() === $id;
        })->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }
}