<?php

namespace Bugloos\FaultToleranceBundle\Config;

class Config
{
    private array $config = [
        'circuitBreaker' => [
            'enabled' => true,
            'forceOpen' => false,
            'forceClosed' => false,
            'timeWindow' => 50,
            'failureRateThreshold' => 10,
            'intervalToHalfOpen' => 30,
        ],
        'fallback' => [
            'enabled' => true,
        ],
        'requestCache' => [
            'enabled' => false,
            'expiresCacheAfter' => 30,//seconds
            'storage' => 'redis',
        ],
        'requestLog' => [
            'enabled' => false,
        ],
    ];

    public function toArray(): array
    {
        return $this->config;
    }

    public function enableFallback(): self
    {
        $this->config['fallback']['enabled'] = true;

        return $this;
    }

    public function disableFallback(): self
    {
        $this->config['fallback']['enabled'] = false;

        return $this;
    }

    public function enableCache(): self
    {
        $this->config['requestCache']['enabled'] = true;

        return $this;
    }

    public function disableCache(): self
    {
        $this->config['requestCache']['enabled'] = false;

        return $this;
    }

    public function cacheStorage(string $storage): self
    {
        $this->config['requestCache']['storage'] = $storage;

        return $this;
    }

    public function expiresCacheAfter(int $second): self
    {
        $this->config['requestCache']['expiresCacheAfter'] = $second;

        return $this;
    }

    public function enableLog(): self
    {
        $this->config['requestLog']['enabled'] = true;

        return $this;
    }

    public function disableLog(): self
    {
        $this->config['requestLog']['enabled'] = false;

        return $this;
    }

    public function enableCircuitBreaker(): self
    {
        $this->config['circuitBreaker']['enabled'] = true;

        return $this;
    }

    public function disableCircuitBreaker(): self
    {
        $this->config['circuitBreaker']['enabled'] = false;

        return $this;
    }

    public function forceOpenCircuitBreaker(bool $forceOpen = true): self
    {
        $this->config['circuitBreaker']['forceOpen'] = $forceOpen;

        return $this;
    }

    public function forceCloseCircuitBreaker(bool $forceClose = true): self
    {
        $this->config['circuitBreaker']['forceClosed'] = $forceClose;

        return $this;
    }

    public function timeWindow(int $timeWindow): self
    {
        $this->config['circuitBreaker']['timeWindow'] = $timeWindow;

        return $this;
    }

    public function failureRateThreshold(int $failureRate): self
    {
        $this->config['circuitBreaker']['failureRateThreshold'] = $failureRate;

        return $this;
    }

    public function intervalToHalfOpen(int $halfOpenTime): self
    {
        $this->config['circuitBreaker']['intervalToHalfOpen'] = $halfOpenTime;

        return $this;
    }
}
