<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Common\Timer;

use DragBox\Common\Logger\Logger;

class MemoryWatchTimer implements TimerInterface
{
    /**
     * @var Logger
     */
    protected $logger;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $this->logger->info(sprintf('Memory usage: %s', memory_get_usage()));
    }

    /**
     * {@inheritdoc}
     */
    public function getInterval()
    {
        return 60;
    }

    /**
     * {@inheritdoc}
     */
    public function isPeriodic()
    {
        return true;
    }
}