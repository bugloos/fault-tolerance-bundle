<?php

/**
 * This file is part of the bugloos/fault-tolerance-bundle project.
 * (c) Bugloos <https://bugloos.com/>
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Bugloos\FaultToleranceBundle\Enum;

/**
 * @author Mojtaba Gheytasi <mjgheytasi@gmail.com>
 */
class EventEnum
{
    public const SUCCESS = 'SUCCESS';
    public const FAILURE = 'FAILURE';
    public const TIMEOUT = 'TIMEOUT';
    public const SHORT_CIRCUITED = 'SHORT_CIRCUITED';
    public const FALLBACK_SUCCESS = 'FALLBACK_SUCCESS';
    public const FALLBACK_FAILURE = 'FALLBACK_FAILURE';
    public const EXCEPTION_THROWN = 'EXCEPTION_THROWN';
    public const RESPONSE_FROM_CACHE = 'RESPONSE_FROM_CACHE';
}
