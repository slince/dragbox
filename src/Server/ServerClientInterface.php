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

use React\Socket\ConnectionInterface;

interface ServerClientInterface
{
    /**
     * Gets the client id given by server.
     *
     * @return string
     */
    public function getId();

    /**
     * Sets client ID.
     *
     * @param string $id
     */
    public function setId($id);

    /**
     * Closes the client
     */
    public function close();

    /**
     * Gets the last active time.
     *
     * @return \DateTimeInterface
     */
    public function getActiveAt();

    /**
     * Gets the control connection.
     *
     * @return ConnectionInterface
     */
    public function getConnection();

    /**
     * Gets the info of the client.
     *
     * @return array
     */
    public function getInfo();
}