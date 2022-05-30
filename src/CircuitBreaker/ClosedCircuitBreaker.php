<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle\CircuitBreaker;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
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
