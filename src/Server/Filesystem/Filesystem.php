<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Server\Filesystem;

use DragBox\Common\Filesystem\File;
use DragBox\Common\Filesystem\FileInterface;
use DragBox\Common\Filesystem\Exception\FileNotExistsException;
use DragBox\Server\Filesystem\Index\IndexerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Filesystem implements FilesystemInterface
{
    /**
     * @var string
     */
    protected $path;

    /**
     * @var IndexerInterface
     */
    protected $indexer;

    /**
     * @var Finder
     */
    protected $finder;

    public function __construct(
        $path,
        IndexerInterface $indexer,
        Finder $finder = null
    ) {
        $this->path = $path;
        $this->indexer = $indexer;
        $this->finder = $finder ?: new Finder();
    }

    /**
     * {@inheritdoc}
     */
    public function getStoragePath()
    {
        return $this->path;
    }

    public function createStreamPipe($key, $mode = 'r')
    {
        $realPath = $this->getStoragePath() . '/' . $key;
        $resource = fopen($realPath, $mode);
        return $resource;
    }

    /**
     * {@inheritdoc}
     */
    public function isFresh($key, $hash)
    {
        $file = $this->indexer->find($key);
        if (is_null($file)) {
            throw new FileNotExistsException($key);
        }
        return $file->getHash() !== $hash;
    }

    /**
     * {@inheritdoc}
     */
    public function index()
    {
        $this->finder->files()->in($this->getStoragePath());
        $files = [];
        foreach ($this->finder as $fileInfo) {
            $files[] = $this->createFileFromFileInfo($fileInfo);
        }
        $this->indexer->index($files);
    }

    public function upload($key, $content, $overwrite)
    {

    }


    public function queryFiles($files)
    {
        $dstFiles = $this->indexer->findAll(array_column($files, 'key'));
        foreach ($files as &$fileData) {
            if (!isset($dstFiles[$fileData['key']])) {
                $fileData['state'] = FileInterface::STATE_MISSING;
                continue;
            }
            $dstFile = $dstFiles[$fileData['key']];
            if ($dstFile->getHash() === $fileData['hash']) {
                $fileData['state'] = FileInterface::STATE_NO_CHANGE;
                continue;
            }
            if ($dstFile->getUpdatedAt() > $fileData['updatedAt']) {
                $fileData['state'] = FileInterface::STATE_OUTDATE;
                continue;
            }
            if ($dstFile->getUpdatedAt() <= $fileData['updatedAt']) {
                $fileData['state'] = FileInterface::STATE_FRESH;
                continue;
            }
        }
        return $files;
    }

    protected function createFileFromFileInfo(SplFileInfo $fileInfo)
    {
        $key = $fileInfo->getRelativePath();
        $createdAt = $updatedAt = $accessAt = new \DateTime();
        $createdAt->setTimestamp($fileInfo->getCTime());
        $updatedAt->setTimestamp($fileInfo->getMTime());
        $accessAt->setTimestamp($fileInfo->getMTime());
        $hash = md5_file($fileInfo->getPathname());
        return new File($key, $hash, $createdAt, $updatedAt, $accessAt);
    }
}