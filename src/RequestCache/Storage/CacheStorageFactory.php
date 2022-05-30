<?php

namespace Bugloos\FaultToleranceBundle\RequestCache\Storage;

use Exception;

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
