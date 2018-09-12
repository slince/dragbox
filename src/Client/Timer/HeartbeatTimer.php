<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Client\Timer;

use DragBox\Common\Protocol\Spike;

/**
 * @codeCoverageIgnore
 */
class HeartbeatTimer extends Timer
{
    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        $this->client->getControlConnection()->write(new Spike('ping'));
    }

    /**
     * {@inheritdoc}
     */
    public function getInterval()
    {
        return 50;
    }

    /**
     * {@inheritdoc}
     */
    public function isPeriodic()
    {
        return true;
    }
}