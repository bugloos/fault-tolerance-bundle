<?php

namespace Bugloos\FaultToleranceBundle\CircuitBreaker;

class ClosedCircuitBreaker
{
    /**
     * Request will always be allowed
     */
    public function allowRequest(): bool
    {
        return true;
    }

    public function markAsSuccess()
    {
    }

    public function markAsFailure()
    {
    }
}
