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

interface ActionHandlerInterface
{
    /**
     * Handling the message.
     *
     * @param SpikeInterface $message
     */
    public function handle(SpikeInterface $message);
}