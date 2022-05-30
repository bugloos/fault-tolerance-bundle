<?php

namespace Bugloos\FaultToleranceBundle\RequestCache\Storage;

interface StorageInterface
{
    public function get(string $bucket, string $key);

    public function set(string $bucket, string $key, $value, int $expiresAfterSeconds): void;

    public function exists(string $bucket, string $key): bool;

    public function remove(string $bucket, string $key): void;

    public function removeBucket(string $bucket): void;
}
