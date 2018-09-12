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

use DragBox\Common\Authentication;
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
        return isset($this['log']['level']) ? $this['log']['level'] : 'info';
    }

    /**
     * Gets the server binding address
     *
     * @return string
     */
    public function getAddress()
    {
        $address = $this->get('address', '0.0.0.0:8090');

        return $address;
    }

    public function getStoragePath()
    {
        return $this['storage_path'];
    }

    public function getDefaultConfigFile()
    {
        return getcwd().'/'.'dragboxd.json';
    }

    /**
     * Gets the authentication.
     *
     * @return Authentication\AuthenticationInterface|null
     */
    public function getAuthentication()
    {
        $auth = $this->get('auth', []);
        $type = isset($auth['type']) ? $auth['type'] : 'simple_password';
        unset($auth['type']);
        if ($auth) {
            switch ($type) {
                default:
                    $authentication = new Authentication\PasswordAuthentication($auth);
                    break;
            }
        } else {
            $authentication = null;
        }

        return $authentication;
    }
}