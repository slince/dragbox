<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Client\Worker;

use GuzzleHttp\Psr7;
use DragBox\Client\Client;
use DragBox\Common\Tunnel\HttpTunnel;
use DragBox\Common\Tunnel\TunnelInterface;
use DragBox\Version;

class HttpWorker extends TcpWorker
{
    /**
     * {@inheritdoc}
     */
    public function resolveTargetHost()
    {
        return $this->tunnel->getForwardHost($this->tunnel->getProxyHost());
    }

    /**
     * {@inheritdoc}
     */
    public function handleConnectLocalError(\Exception $exception)
    {
        $response = $this->makeErrorResponse(500, $exception->getMessage());
        $this->proxyConnection->end(Psr7\str($response));
        $this->stop();
    }

    /**
     * Make an error psr7 response.
     *
     * @param int    $code
     * @param string $message
     *
     * @return Psr7\Response
     */
    protected function makeErrorResponse($code, $message)
    {
        $message = $message ?: sprintf('Cannot connect to "%s"', $this->resolveTargetHost());

        return new Psr7\Response($code, [
            'Content-Length' => strlen($message),
            'X-Spike' => Client::NAME .' '. Version::VERSION,
        ], $message);
    }

    /**
     * {@inheritdoc}
     */
    public static function support(TunnelInterface $tunnel)
    {
        $tunnel instanceof HttpTunnel;
    }
}