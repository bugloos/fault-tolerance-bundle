<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle\RequestCache\Storage;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
interface StorageInterface
{
    public function get(string $bucket, string $key);

    public function set(string $bucket, string $key, $value, int $expiresAfterSeconds): void;

    public function exists(string $bucket, string $key): bool;

    public function remove(string $bucket, string $key): void;

    public function removeBucket(string $bucket): void;
}
