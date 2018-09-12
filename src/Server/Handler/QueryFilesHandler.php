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

use DragBox\Common\Filesystem\FileFactory;
use DragBox\Common\Protocol\Spike;
use Slince\EventDispatcher\Event;
use DragBox\Common\Exception\BadRequestException;
use DragBox\Common\Protocol\SpikeInterface;
use DragBox\Server\Event\Events;

class QueryFilesHandler extends RequireAuthHandler
{
    /**
     * {@inheritdoc}
     */
    public function handle(SpikeInterface $message)
    {
        parent::handle($message);
        //Fires 'query_files' event
        $this->getEventDispatcher()->dispatch(new Event(Events::QUERY_FILES, $this, [
            'message' => $message,
        ]));
        $files = FileFactory::createManyFromArray($message->getBody()['files']);
        $queriedFiles = $this->server->getFilesystem()->queryFiles($files);
        $message = new Spike('query_files_response', [
            'files' => $queriedFiles
        ]);
        $this->connection->write($message);
    }
}