<?php

/*
 * This file is part of the slince/dragbox package.
 *
 * (c) Slince <taosikai@yeah.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace DragBox\Common\Filesystem;

use Symfony\Component\Finder\SplFileInfo;

class FileFactory
{
    /**
     * Creates a file by spl file.
     *
     * @param \SplFileInfo $fileInfo
     * @return FileInterface
     */
    public static function createByFileInfo(\SplFileInfo $fileInfo)
    {
        if ($fileInfo instanceof SplFileInfo) {
            $key = $fileInfo->getRelativePath();
        } else {
            $key = $fileInfo->getRelativePath();
        }
        $createdAt = $updatedAt = $accessAt = new \DateTime();
        $createdAt->setTimestamp($fileInfo->getCTime());
        $updatedAt->setTimestamp($fileInfo->getMTime());
        $accessAt->setTimestamp($fileInfo->getMTime());
        $hash = md5_file($fileInfo->getPathname());
        return new File($key, $hash, $createdAt, $updatedAt, $accessAt);
    }

    /**
     * Creates many files.
     *
     * @param array $data
     * @return FileInterface[]
     */
    public static function createManyFromArray($data)
    {
        return array_map(function($item){
            if (is_numeric($item['createdAt'])) {
                $item['createdAt'] = (new \DateTime())->setTimestamp($item['createdAt']);
            }
            if (is_numeric($item['updatedAt'])) {
                $item['updatedAt'] = (new \DateTime())->setTimestamp($item['updatedAt']);
            }
            if (is_numeric($item['accessAt'])) {
                $item['accessAt'] = (new \DateTime())->setTimestamp($item['accessAt']);
            }
            return new File($item['key'], $item['hash'], $item['createdAt'], $item['updatedAt'], $item['accessAt']);
        }, $data);
    }
}