<?php

namespace Bugloos\FaultToleranceBundle\Exception;

use Exception;

class RuntimeException extends \RuntimeException
{
    /**
     * Exception while retrieving the fallback, if enabled
     */
    private ?Exception $fallbackException;

    /**
     * Class name of the command
     */
    private string $commandClass;

    /**
     * Constructor
     *
     * @param string $message
     * @param int $commandClass
     * @param Exception|null $originalException (Optional) Original exception. May be null if short-circuited
     * @param Exception|null $fallbackException (Optional) Exception thrown while retrieving fallback
     */
    public function __construct(
        $message,
        $commandClass,
        Exception $originalException = null,
        Exception $fallbackException = null
    ) {
        parent::__construct($message, 0, $originalException);
        $this->fallbackException = $fallbackException;
        $this->commandClass = $commandClass;
    }

    /**
     * Returns class name of the command the exception was thrown from
     */
    public function getCommandClass(): string
    {
        return $this->commandClass;
    }

    /**
     * Returns fallback exception if available
     *
     * @return Exception
     */
    public function getFallbackException(): ?Exception
    {
        return $this->fallbackException;
    }
}
