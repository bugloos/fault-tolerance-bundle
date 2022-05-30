<?php

namespace Bugloos\FaultToleranceBundle\CircuitBreaker\Enum;

class CircuitBreakerStatus
{
    public const OPEN = 'OPEN';
    public const CLOSED = 'CLOSED';
    public const HALF_OPEN = 'HALF_OPEN';
}
