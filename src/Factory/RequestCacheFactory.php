<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle\Factory;

use Bugloos\FaultToleranceBundle\RequestCache\RequestCache;
use Bugloos\FaultToleranceBundle\RequestCache\Storage\CacheStorageFactory;
use Exception;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
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
