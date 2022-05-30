<?php

namespace Bugloos\FaultToleranceBundle\Enum;

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
