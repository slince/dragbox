<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Client;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Socket\ConnectionInterface;
use React\Socket\Connector;
use function Dragbox\Common\jsonBuffer;
use Slince\EventDispatcher\Dispatcher;
use Slince\EventDispatcher\DispatcherInterface;
use Slince\EventDispatcher\Event;
use DragBox\Client\Event\Events;
use DragBox\Client\Event\FilterActionHandlerEvent;
use DragBox\Client\Worker\WorkerInterface;
use DragBox\Common\Logger\Logger;
use DragBox\Common\Protocol\Spike;
use DragBox\Common\Timer\MemoryWatchTimer;
use DragBox\Common\Timer\TimersAware;
use DragBox\Version;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Client extends Application implements ClientInterface
{
    use TimersAware;

    /**
     * @var string
     */
    const LOGO = <<<EOT
 _____   _____   _   _   _    _____  
/  ___/ |  _  \ | | | | / /  | ____| 
| |___  | |_| | | | | |/ /   | |__   
\___  \ |  ___/ | | | |\ \   |  __|  
 ___| | | |     | | | | \ \  | |___  
/_____/ |_|     |_| |_|  \_\ |_____| 


EOT;

    /**
     * @var string
     */
    const NAME = 'Spike Client';

    /**
     * @var string
     */
    protected $id;

    /**
     * @var Configuration
     */
    protected $configuration;

    /**
     * @var LoopInterface
     */
    protected $eventLoop;

    /**
     * @var DispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var ConnectionInterface
     */
    protected $controlConnection;

    /**
     * @var \DateTimeInterface
     */
    protected $activeAt;

    /**
     * @var WorkerInterface[]|Collection
     */
    protected $workers;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var bool
     */
    protected $running = false;

    /**
     * Whether connect to server.
     * @var bool
     */
    protected $connected = false;

    public function __construct(Configuration $configuration, LoopInterface $eventLoop = null)
    {
        $this->configuration = $configuration;
        $this->eventLoop = $eventLoop ?: Factory::create();
        $this->eventDispatcher = new Dispatcher();
        $this->workers = new ArrayCollection();
        $this->initializeEvents();
        parent::__construct(static::NAME, Version::VERSION);
    }

    /**
     * {@inheritdoc}
     */
    public function getHelp()
    {
        return static::LOGO.parent::getHelp();
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $this->logger = new Logger(
            $this->eventLoop,
            $this->getConfiguration()->getLogLevel(),
            $this->getConfiguration()->getLogFile(),
            $output
        );
        // Execute command if the command name is exists
        if ($this->getCommandName($input) ||
            true === $input->hasParameterOption(array('--help', '-h'), true)
        ) {
            $exitCode = parent::doRun($input, $output);
        } else {
            $exitCode = $this->start();
        }

        return $exitCode;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        $connector = new Connector($this->eventLoop, [
            'timeout' => $this->configuration->get('timeout', 5)
        ]);
        $connector->connect($this->configuration->getServerAddress())->then(function($connection){
            $this->connected = true; //Set connect status
            $this->initializeTimers();
            $this->handleControlConnection($connection);
        }, function(){
            $this->eventDispatcher->dispatch(new Event(Events::CANNOT_CONNECT_SERVER, $this));
        });
        $this->running = true; //Set running status
        $this->eventDispatcher->dispatch(Events::CLIENT_RUN);
        $this->eventLoop->run();

        return 0;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if (!$this->running) {
            return;
        }
        //Reset the client
        $this->reset();
        if ($this->controlConnection) {
            //don't trigger "close" event if closed by client
            $this->controlConnection->removeListener('close', [$this, 'handleDisconnectServer']);
            $this->controlConnection->end();
        }
        $this->running = false;
    }


    protected function reset()
    {
        $this->connected = false;
        foreach ($this->getTimers() as $timer) {
            $this->cancelTimer($timer);
        }
        $this->timers = [];
        foreach ($this->workers as $worker) {
            $worker->stop();
        }
        $this->workers = new ArrayCollection();
    }

    /**
     * Reconnect if disconnect from server.
     */
    public function handleDisconnectServer()
    {
        $this->eventDispatcher->dispatch(new Event(Events::DISCONNECT_FROM_SERVER, $this));
        $this->close();
    }

    /**
     * Handles the control connection.
     *
     * @param ConnectionInterface $connection
     * @codeCoverageIgnore
     */
    protected function handleControlConnection(ConnectionInterface $connection)
    {
        $this->controlConnection = $connection;
        //Emit the event
        $this->eventDispatcher->dispatch(new Event(Events::CLIENT_CONNECT, $this, [
            'connection' => $connection,
        ]));
        //Sends auth request
        $this->sendAuthRequest($connection);
        //Disconnect from server
        $connection->on('close', [$this, 'handleDisconnectServer']);

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
                        //ignore bad message
                    }
                }
            }
        }, function($exception) use ($connection){
            $this->eventDispatcher->dispatch(new Event(Events::CONNECTION_ERROR, $this, [
                'connection' => $connection,
                'exception' => $exception,
            ]));
        });
    }

    /**
     * Request for auth.
     *
     * @param ConnectionInterface $connection
     * @codeCoverageIgnore
     */
    protected function sendAuthRequest(ConnectionInterface $connection)
    {
        $authInfo = array_replace([
            'os' => PHP_OS,
            'version' => Version::VERSION,
        ], $this->configuration->get('auth', []));

        $connection->write(new Spike('auth', $authInfo));
    }

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConnection()
    {
        return $this->controlConnection;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveAt()
    {
        return $this->activeAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @param \DateTimeInterface $activeAt
     */
    public function setActiveAt($activeAt)
    {
        $this->activeAt = $activeAt;
    }

    /**
     * @return LoopInterface
     */
    public function getEventLoop()
    {
        return $this->eventLoop;
    }

    /**
     * @return Collection|WorkerInterface[]
     */
    public function getWorkers()
    {
        return $this->workers;
    }

    /**
     * @return Logger
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), [
            new Command\ShowProxyHostsCommand($this),
            new Command\InitCommand($this),
        ]);
    }

    protected function initializeEvents()
    {
        $this->eventDispatcher->addSubscriber(new Listener\ClientListener());
        $this->eventDispatcher->addSubscriber(new Listener\LoggerListener($this));
    }

    /**
     * Creates default timers.
     *
     * @codeCoverageIgnore
     */
    protected function initializeTimers()
    {
        $this->addTimer(new Timer\HeartbeatTimer($this));
        $this->addTimer(new MemoryWatchTimer($this->getLogger()));
    }
}