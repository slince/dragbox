<?php

/*
 * This file is part of the slince/spike package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Dragbox\Common;

use DragBox\Common\Exception\RuntimeException;
use DragBox\Common\Protocol\StreamingJsonParser;
use React\Stream\ReadableStreamInterface;

/**
 * @param ReadableStreamInterface $stream
 * @param callable                $resolve
 * @param callable                $reject
 * @param StreamingJsonParser     $streamParser
 */
function jsonBuffer(ReadableStreamInterface $stream, callable $resolve, callable $reject = null, StreamingJsonParser $streamParser = null)
{
    // stream already ended => resolve with empty buffer
    if (!$stream->isReadable()) {
        return;
    }
    if (null === $streamParser) {
        $streamParser = new StreamingJsonParser();
    }
    $bufferer = function ($data) use ($resolve, $streamParser) {
        $parsed = $streamParser->push($data);
        if ($parsed) {
            $resolve($parsed);
        }
    };
    $stream->on('data', $bufferer);
    $stream->on('error', function ($error) use ($stream, $bufferer, $reject) {
        $stream->removeListener('data', $bufferer);
        $reject && $reject(new RuntimeException('An error occured on the underlying stream while buffering', 0, $error));
    });
    $stream->on('close', function () use ($resolve, $streamParser) {
        $resolve($streamParser->push(''));
    });
}