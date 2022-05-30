<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */

namespace Bugloos\FaultToleranceBundle\CircuitBreaker\Storage;

use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Cache\Adapter\AdapterInterface;

class Storage
{
    private AdapterInterface $adapter;

    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function isOpen(string $commandKey): bool
    {
        return (bool) $this->adapter->getItem($this->openCacheKey($commandKey))->get();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function isHalfOpen(string $commandKey): bool
    {
        return (bool) $this->adapter->getItem($this->halfOpenCacheKey($commandKey))->get();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setOpenCircuit(string $commandKey, int $timeWindow): void
    {
        $openCircuit = $this->adapter->getItem($this->openCacheKey($commandKey));
        $openCircuit
            ->set(true)
            ->expiresAfter($timeWindow);

        $this->adapter->save($openCircuit);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setHalfOpenCircuit(string $commandKey, int $timeWindow, int $intervalToHalfOpen): void
    {
        $halfOpenCircuit = $this->adapter->getItem($this->halfOpenCacheKey($commandKey));
        $halfOpenCircuit
            ->set(true)
            ->expiresAfter($timeWindow + $intervalToHalfOpen);

        $this->adapter->save($halfOpenCircuit);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function incrementFailure(string $commandKey, int $timeWindow): void
    {
        $failures = $this->adapter->getItem($this->failuresCacheKey($commandKey));

        if ($failures->get()) {
            $failuresExpireDatetime = $this->getFailuresExpireTime($commandKey);

            $failures->set($failures->get() + 1)
                ->expiresAt($failuresExpireDatetime);
        } else {
            $expirationDatetime = $this->makeFailuresExpireTime($timeWindow);

            $failures->set(1)
                ->expiresAt($expirationDatetime);

            $this->rememberFailuresExpireTime($commandKey, $expirationDatetime);
        }

        $this->adapter->save($failures);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function failureCount(string $commandKey): int
    {
        return $this->adapter->getItem($this->failuresCacheKey($commandKey))->get() ?? 0;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function setSuccess(string $commandKey): void
    {
        $this->adapter->deleteItems([
            $this->halfOpenCacheKey($commandKey),
            $this->openCacheKey($commandKey),
            $this->failuresCacheKey($commandKey),
            $this->failuresExpireTimeCacheKey($commandKey),
        ]);
    }

    private function failuresCacheKey(string $commandKey): string
    {
        return $commandKey . '.failures';
    }

    private function openCacheKey(string $commandKey): string
    {
        return $commandKey . '.open';
    }

    private function halfOpenCacheKey(string $commandKey): string
    {
        return $commandKey . '.halfOpen';
    }

    private function failuresExpireTimeCacheKey(string $commandKey): string
    {
        return $this->failuresCacheKey($commandKey) . '.expireTime';
    }

    /**
     * @throws InvalidArgumentException
     */
    private function getFailuresExpireTime(string $commandKey): \DateTime
    {
        return $this->adapter->getItem(
            $this->failuresExpireTimeCacheKey($commandKey)
        )->get();
    }

    /**
     * @throws InvalidArgumentException
     */
    private function rememberFailuresExpireTime(string $commandKey, \DateTime $expirationDatetime)
    {
        $failuresExpireDatetime = $this->adapter->getItem(
            $this->failuresExpireTimeCacheKey($commandKey)
        )
            ->set($expirationDatetime)
            ->expiresAt($expirationDatetime);

        $this->adapter->save($failuresExpireDatetime);
    }

    private function makeFailuresExpireTime(int $timeWindow): \DateTime
    {
        return (new \DateTime())
            ->add(
                date_interval_create_from_date_string("$timeWindow seconds")
            );
    }
}
