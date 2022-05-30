<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle\RequestCache\Storage;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
class MongoDbStorage implements StorageInterface
{
    public function __construct(string $mongodbUrl)
    {
    }

    public function get(string $bucket, string $key)
    {
        // TODO: Implement get() method.
    }

    public function set(string $bucket, string $key, $value, int $expiresAfterSeconds): void
    {
        // TODO: Implement set() method.
    }

    public function exists(string $bucket, string $key): bool
    {
        // TODO: Implement exists() method.
    }

    public function remove(string $bucket, string $key): void
    {
        // TODO: Implement remove() method.
    }

    public function removeBucket(string $bucket): void
    {
        // TODO: Implement removeBucket() method.
    }
}
