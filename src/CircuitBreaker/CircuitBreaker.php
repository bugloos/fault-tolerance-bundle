<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle\CircuitBreaker;

use Bugloos\FaultToleranceBundle\CircuitBreaker\Storage\Storage;
use Psr\Cache\InvalidArgumentException;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
class CircuitBreaker
{
    private Storage $storage;

    private string $commandKey;

    private array $config;

    public function __construct(
        string $commandKey,
        Storage $storage,
        array $config
    ) {
        $this->commandKey = $commandKey;
        $this->storage = $storage;
        $this->config = $config;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function isOpen(): bool
    {
        if ($this->storage->isOpen($this->commandKey)) {
            return true;
        }

        if (
            $this->storage->failureCount($this->commandKey) >= $this->config['failureRateThreshold']
        ) {
            $this->storage->setOpenCircuit(
                $this->commandKey,
                $this->config['timeWindow']
            );
            $this->storage->setHalfOpenCircuit(
                $this->commandKey,
                $this->config['timeWindow'],
                $this->config['intervalToHalfOpen']
            );

            return true;
        }

        return false;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function allowRequest(): bool
    {
        if ($this->config['forceOpen']) {
            return false;
        }

        if ($this->config['forceClosed']) {
            return true;
        }

        return ! $this->isOpen();
    }

    /**
     * @throws InvalidArgumentException
     */
    public function markAsSuccess()
    {
        $this->storage->setSuccess($this->commandKey);
    }

    /**
     * @throws InvalidArgumentException
     */
    public function markAsFailure(): void
    {
        if ($this->storage->isHalfOpen($this->commandKey)) {
            $this->storage->setOpenCircuit(
                $this->commandKey,
                $this->config['timeWindow']
            );
            $this->storage->setHalfOpenCircuit(
                $this->commandKey,
                $this->config['timeWindow'],
                $this->config['intervalToHalfOpen']
            );
        }

        $this->storage->incrementFailure(
            $this->commandKey,
            $this->config['timeWindow']
        );
    }
}
