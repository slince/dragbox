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

use Doctrine\Common\Collections\Collection;

interface ServerInterface
{
    /**
     * Starts the server.
     */
    public function start();

    /**
     * Gets all clients
     *
     * @return ServerClientInterface[]|Collection
     */
    public function getClients();

    /**
     * Gets the client by ID.
     *
     * @param string $id
     *
     * @return null|ServerClientInterface
     */
    public function getClientById($id);

    /**
     * Gets the configuration
     *
     * @return Configuration
     */
    public function getConfiguration();
}