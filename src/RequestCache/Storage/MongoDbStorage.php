<?php

namespace Bugloos\FaultToleranceBundle\RequestCache\Storage;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;

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
