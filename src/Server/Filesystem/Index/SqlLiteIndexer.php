<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Server\Filesystem\Index;

class SqlLiteIndexer implements IndexerInterface
{
    /**
     * @var \PDO
     */
    protected $pdo;

    public function __construct($sqlFile)
    {
        $this->pdo = new \PDO('sqlite:' . $sqlFile);
        $this->prepareSchema();
    }

    protected function prepareSchema()
    {
        $sql = @file_get_contents(__DIR__ . '/schema.sql');
        $this->pdo->exec($sql);
    }

    /**
     * {@inheritdoc}
     */
    public function index(array $files)
    {

    }
}