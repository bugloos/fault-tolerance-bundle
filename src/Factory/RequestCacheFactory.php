<?php

namespace Bugloos\FaultToleranceBundle\Factory;

use Bugloos\FaultToleranceBundle\RequestCache\RequestCache;
use Bugloos\FaultToleranceBundle\RequestCache\Storage\CacheStorageFactory;
use Exception;

class RequestCacheFactory
{
    private CacheStorageFactory $storageFactory;

    public function __construct(CacheStorageFactory $storageFactory)
    {
        $this->storageFactory = $storageFactory;
    }

    /**
     * @throws Exception
     */
    public function create(string $storageType): RequestCache
    {
        $storage = $this->storageFactory->create($storageType);

        return new RequestCache($storage);
    }
}
