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
use Slince\Config\Config;

class Configuration extends Config
{
    /**
     * Gets the current timezone.
     *
     * @return string
     */
    public function getTimezone()
    {
        return $this->get('timezone', 'Asia/shanghai');
    }

    /**
     * Gets the log file.
     *
     * @return string
     */
    public function getLogFile()
    {
        return isset($this['log']['file']) ? $this['log']['file'] : getcwd().'/access.log';
    }

    /**
     * Gets the log level.
     *
     * @return int
     */
    public function getLogLevel()
    {
        return  isset($this['log']['level']) ? $this['log']['level'] : 'info';
    }

    public function getServerAddress()
    {
        $address = $this->get('server-address', '127.0.0.1:8090');

        return $address;
    }

    public function getDefaultConfigFile()
    {
        return getcwd().'/'.'spike.json';
    }
}