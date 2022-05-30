<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle\CircuitBreaker\Enum;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
class CircuitBreakerStatus
{
    public const OPEN = 'OPEN';
    public const CLOSED = 'CLOSED';
    public const HALF_OPEN = 'HALF_OPEN';
}
