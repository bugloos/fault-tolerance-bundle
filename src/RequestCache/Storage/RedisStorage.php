<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle\RequestCache\Storage;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
class RedisStorage implements StorageInterface
{
    private RedisTagAwareAdapter $adapter;

    public function __construct(string $redisUrl)
    {
        $client = RedisAdapter::createConnection($redisUrl);
        $this->adapter = new RedisTagAwareAdapter($client);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function get(string $bucket, string $key)
    {
        return $this->adapter->getItem($bucket . '.' . $key)->get();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function set(string $bucket, string $key, $value, int $expiresAfterSeconds): void
    {
        $item = $this->adapter->getItem($bucket . '.' . $key);
        $item->set($value);
        $item->expiresAfter($expiresAfterSeconds);
        $item->tag($bucket);
        $this->adapter->save($item);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function exists(string $bucket, string $key): bool
    {
        return (bool) $this->adapter->getItem($bucket . '.' . $key)->get();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function remove(string $bucket, string $key): void
    {
        $this->adapter->deleteItem($bucket . '.' . $key);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function removeBucket(string $bucket): void
    {
        $this->adapter->invalidateTags([$bucket]);
    }
}
