<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle\RequestCache;

use Bugloos\FaultToleranceBundle\RequestCache\Storage\StorageInterface;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
class RequestCache
{
    private StorageInterface $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function clearAll(string $commandKey): void
    {
        $this->storage->removeBucket($commandKey);
    }

    public function clear(string $commandKey, string $cacheKey): void
    {
        $this->storage->remove($commandKey, $cacheKey);
    }

    public function get(string $commandKey, string $cacheKey)
    {
        return $this->storage->get($commandKey, $cacheKey);
    }

    public function put(string $commandKey, string $cacheKey, $value, int $expiresAfterSeconds): void
    {
        $this->storage->set($commandKey, $cacheKey, $value, $expiresAfterSeconds);
    }

    public function exists(string $commandKey, string $cacheKey): bool
    {
        return $this->storage->exists($commandKey, $cacheKey);
    }
}
