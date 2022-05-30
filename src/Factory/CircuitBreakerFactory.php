<?php

namespace Bugloos\FaultToleranceBundle\Factory;

use Bugloos\FaultToleranceBundle\CircuitBreaker\CircuitBreaker;
use Bugloos\FaultToleranceBundle\CircuitBreaker\ClosedCircuitBreaker;
use Bugloos\FaultToleranceBundle\CircuitBreaker\Storage\Storage;

class CircuitBreakerFactory
{
    private Storage $storage;

    protected array $circuitBreakersByCommand = [];

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function create(string $commandKey, array $circuitBreakerConfig)
    {
        if (! isset($this->circuitBreakersByCommand[$commandKey])) {
            if ($circuitBreakerConfig['enabled']) {
                $this->circuitBreakersByCommand[$commandKey] =
                    new CircuitBreaker($commandKey, $this->storage, $circuitBreakerConfig);
            } else {
                $this->circuitBreakersByCommand[$commandKey] = new ClosedCircuitBreaker();
            }
        }

        return $this->circuitBreakersByCommand[$commandKey];
    }
}
