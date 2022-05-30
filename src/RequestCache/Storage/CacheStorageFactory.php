<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle\RequestCache\Storage;

use Exception;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
class CacheStorageFactory
{
    private string $redisUrl;

    private string $mongodbUrl;

    public function __construct(string $redisUrl = '', string $mongodbUrl = '')
    {
        $this->redisUrl = $redisUrl;
        $this->mongodbUrl = $mongodbUrl;
    }

    /**
     * @param string $storage
     *
     * @return StorageInterface
     *
     * @throws Exception
     */
    public function create(string $storage): StorageInterface
    {
        switch ($storage) {
            case 'redis':
                return new RedisStorage($this->redisUrl);
            case 'mongodb':
                return new MongoDbStorage($this->mongodbUrl);
            default:
                throw new Exception();
        }
    }
}
